<?php

namespace App\Http\Controllers;

use App\Models\OscarVote;
use App\Models\PhotoVote;
use App\Models\Session as CampSession;
use App\Models\Team;
use App\Models\User;
use App\Models\UserVote;
use App\Models\Vote;
use App\Models\VotePhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class VoteController extends Controller
{
    public function index()
    {
        $votes = Vote::with('session')->withCount('photos')->latest()->get();

        return view('votes.index', compact('votes'));
    }

    public function create()
    {
        $activeSession = CampSession::where('active', true)->first();

        return view('votes.create', compact('activeSession'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in([Vote::TYPE_TEAM, Vote::TYPE_PHOTO, Vote::TYPE_OSCAR])],
        ]);

        if ($data['type'] === Vote::TYPE_PHOTO) {
            $request->validate([
                'photos' => ['required', 'array', 'size:10'],
                'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            ], [
                'photos.size' => 'Для фото-голосування потрібно завантажити 10 фото.',
            ]);
        }

        $activeSession = null;

        if ($data['type'] === Vote::TYPE_OSCAR) {
            $activeSession = CampSession::where('active', true)->first();

            if (!$activeSession) {
                return back()
                    ->withInput()
                    ->withErrors(['type' => 'Спочатку активуйте сесію, щоб створити голосування “Оскар”.']);
            }
        }

        $voteUrl = Str::random(10);
        $vote = Vote::create([
            'name' => $data['name'],
            'vote_url' => $voteUrl,
            'type' => $data['type'],
            'session_id' => $activeSession?->id,
        ]);

        if ($vote->isPhotoVote()) {
            $this->storeUploadedPhotos($vote, $request->file('photos', []));
        }

        return redirect()->route('votes.show', $voteUrl);
    }

    public function show($voteUrl)
    {
        $vote = Vote::where('vote_url', $voteUrl)->firstOrFail();

        return view('votes.show', compact('vote'));
    }

    public function authenticate(Request $request, $voteUrl)
    {
        $data = $request->validate([
            'pin_code' => ['required', 'string', 'max:255'],
        ]);

        $user = User::where('pin_code', $data['pin_code'])->first();

        if (!$user) {
            return redirect()->route('votes.show', $voteUrl)->withErrors(['message' => 'Invalid PIN code.']);
        }

        return redirect()->route('votes.vote', ['voteUrl' => $voteUrl, 'userId' => $user->id]);
    }

    public function vote($voteUrl, $userId)
    {
        $vote = Vote::where('vote_url', $voteUrl)->firstOrFail();
        $user = User::findOrFail($userId);

        if ($vote->isPhotoVote()) {
            $photos = $vote->photos()->get();
            $alreadyVoted = PhotoVote::where('vote_id', $vote->id)
                ->where('user_id', $user->id)
                ->where('source', PhotoVote::SOURCE_USER)
                ->exists();

            return view('votes.photo-vote', compact('vote', 'user', 'photos', 'alreadyVoted'));
        }

        if ($vote->isOscarVote()) {
            $nominations = Vote::OSCAR_NOMINATIONS;
            $candidatesByNomination = $this->oscarCandidatesByNomination($vote);
            $alreadyVoted = OscarVote::where('vote_id', $vote->id)
                ->where('user_id', $user->id)
                ->exists();

            return view('votes.oscar-vote', compact('vote', 'user', 'nominations', 'candidatesByNomination', 'alreadyVoted'));
        }

        $teams = Team::where('id', '!=', $user->team_id)
            ->where('id', '!=', 10)
            ->get();

        return view('votes.vote', compact('vote', 'user', 'teams'));
    }

    public function submitVote(Request $request, $voteUrl, $userId)
    {
        $vote = Vote::where('vote_url', $voteUrl)->firstOrFail();
        $user = User::findOrFail($userId);

        if ($vote->isPhotoVote()) {
            return $this->submitPhotoVote($request, $vote, $user);
        }

        if ($vote->isOscarVote()) {
            return $this->submitOscarVote($request, $vote, $user);
        }

        $data = $request->validate([
            'team_id' => ['required', 'exists:teams,id'],
        ]);

        $existingVote = UserVote::where('vote_id', $vote->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingVote) {
            return redirect()
                ->route('votes.vote', ['voteUrl' => $voteUrl, 'userId' => $userId])
                ->withErrors(['message' => 'You have already voted.']);
        }

        if ((int) $user->team_id === (int) $data['team_id']) {
            return redirect()
                ->route('votes.vote', ['voteUrl' => $voteUrl, 'userId' => $userId])
                ->withErrors(['message' => 'You cannot vote for your own team.']);
        }

        UserVote::create([
            'vote_id' => $vote->id,
            'team_id' => $data['team_id'],
            'user_id' => $user->id,
            'points' => 1,
        ]);

        return redirect()->route('votes.success');
    }

    public function success()
    {
        return view('votes.success');
    }

    public function result($voteUrl)
    {
        $vote = Vote::where('vote_url', $voteUrl)->firstOrFail();

        if ($vote->isPhotoVote()) {
            return $this->photoResult($vote);
        }

        if ($vote->isOscarVote()) {
            return $this->oscarResult($vote);
        }

        $results = UserVote::where('vote_id', $vote->id)
            ->with('team.element')
            ->get()
            ->groupBy('team_id')
            ->map(function ($votes) {
                return $votes->sum('points');
            });

        $maxVotes = $results->max();

        $teams = Team::whereIn('id', $results->keys())->with('element')->get()->mapWithKeys(function ($team) use ($results) {
            return [$team->name => ['count' => $results[$team->id], 'color' => $team->element->color]];
        });

        return view('votes.result', compact('vote', 'teams', 'maxVotes'));
    }

    public function addPointsForm($voteUrl)
    {
        $vote = Vote::where('vote_url', $voteUrl)->firstOrFail();

        if ($vote->isPhotoVote()) {
            $photos = $vote->photos()->get();

            return view('votes.add-photo-points', compact('vote', 'photos'));
        }

        abort_if($vote->isOscarVote(), 404);

        $teams = Team::with('element')->get();

        return view('votes.add-points', compact('vote', 'teams'));
    }

    public function addPoints(Request $request, $voteUrl)
    {
        $vote = Vote::where('vote_url', $voteUrl)->firstOrFail();

        if ($vote->isPhotoVote()) {
            return $this->addPhotoPoints($request, $vote);
        }

        abort_if($vote->isOscarVote(), 404);

        $data = $request->validate([
            'team_id' => ['required', 'exists:teams,id'],
            'points' => ['required', 'integer', 'min:1'],
        ]);

        $team = Team::findOrFail($data['team_id']);

        UserVote::create([
            'vote_id' => $vote->id,
            'team_id' => $team->id,
            'user_id' => auth()->id() ?? null,
            'points' => $data['points'],
        ]);

        return redirect()->route('votes.result', $voteUrl);
    }

    public function photosForm($voteUrl)
    {
        $vote = Vote::where('vote_url', $voteUrl)->with('photos')->firstOrFail();
        abort_unless($vote->isPhotoVote(), 404);

        return view('votes.photos', compact('vote'));
    }

    public function storePhotos(Request $request, $voteUrl)
    {
        $vote = Vote::where('vote_url', $voteUrl)->firstOrFail();
        abort_unless($vote->isPhotoVote(), 404);

        $remainingSlots = 10 - $vote->photos()->count();

        if ($remainingSlots <= 0) {
            return back()->withErrors(['photos' => 'Для цього голосування вже завантажено 10 фото.']);
        }

        $request->validate([
            'photos' => ['required', 'array', 'max:' . $remainingSlots],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'photos.max' => "Можна додати ще не більше {$remainingSlots} фото.",
        ]);

        $this->storeUploadedPhotos($vote, $request->file('photos', []));

        return redirect()->route('votes.photosForm', $vote->vote_url);
    }

    private function submitPhotoVote(Request $request, Vote $vote, User $user)
    {
        $alreadyVoted = PhotoVote::where('vote_id', $vote->id)
            ->where('user_id', $user->id)
            ->where('source', PhotoVote::SOURCE_USER)
            ->exists();

        if ($alreadyVoted) {
            return redirect()
                ->route('votes.vote', ['voteUrl' => $vote->vote_url, 'userId' => $user->id])
                ->withErrors(['message' => 'Ви вже проголосували.']);
        }

        $data = $request->validate([
            'photo_ids' => ['required', 'array', 'size:3'],
            'photo_ids.*' => ['integer', 'distinct', 'exists:vote_photos,id'],
        ], [
            'photo_ids.required' => 'Оберіть 3 фотографії.',
            'photo_ids.size' => 'Оберіть рівно 3 фотографії.',
            'photo_ids.*.distinct' => 'Оберіть 3 різні фотографії.',
        ]);

        $photoIds = VotePhoto::where('vote_id', $vote->id)
            ->whereIn('id', $data['photo_ids'])
            ->pluck('id');

        if ($photoIds->count() !== 3) {
            return back()
                ->withInput()
                ->withErrors(['photo_ids' => 'Оберіть фото тільки з цього голосування.']);
        }

        foreach ($photoIds as $photoId) {
            PhotoVote::create([
                'vote_id' => $vote->id,
                'vote_photo_id' => $photoId,
                'user_id' => $user->id,
                'source' => PhotoVote::SOURCE_USER,
                'points' => 1,
            ]);
        }

        return redirect()->route('votes.success');
    }

    private function addPhotoPoints(Request $request, Vote $vote)
    {
        $data = $request->validate([
            'vote_photo_id' => ['required', 'exists:vote_photos,id'],
            'points' => ['required', 'integer', 'min:1'],
        ]);

        $photo = $vote->photos()->whereKey($data['vote_photo_id'])->firstOrFail();

        PhotoVote::create([
            'vote_id' => $vote->id,
            'vote_photo_id' => $photo->id,
            'user_id' => auth()->id() ?? null,
            'source' => PhotoVote::SOURCE_JURY,
            'points' => $data['points'],
        ]);

        return redirect()->route('votes.result', $vote->vote_url);
    }

    private function photoResult(Vote $vote)
    {
        $scores = PhotoVote::where('vote_id', $vote->id)
            ->selectRaw('vote_photo_id, SUM(points) as total')
            ->groupBy('vote_photo_id')
            ->pluck('total', 'vote_photo_id');

        $photos = $vote->photos()->get()->map(function (VotePhoto $photo) use ($scores) {
            $photo->score = (int) ($scores[$photo->id] ?? 0);

            return $photo;
        });

        $maxVotes = $photos->max('score') ?? 0;
        $photos = $photos->sortBy([
            ['score', 'asc'],
            ['id', 'asc'],
        ])->values();

        return view('votes.photo-result', compact('vote', 'photos', 'maxVotes'));
    }

    private function submitOscarVote(Request $request, Vote $vote, User $user)
    {
        $alreadyVoted = OscarVote::where('vote_id', $vote->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyVoted) {
            return redirect()
                ->route('votes.vote', ['voteUrl' => $vote->vote_url, 'userId' => $user->id])
                ->withErrors(['message' => 'Ви вже проголосували.']);
        }

        $rules = [];
        $messages = [];

        foreach (Vote::OSCAR_NOMINATIONS as $key => $nomination) {
            $limit = $nomination['limit'];
            $rules["oscar_votes.{$key}"] = ['required', 'array', 'size:' . $limit];
            $rules["oscar_votes.{$key}.*"] = ['integer', 'distinct', 'exists:users,id'];
            $messages["oscar_votes.{$key}.required"] = "Оберіть номінантів для категорії “{$nomination['title']}”.";
            $messages["oscar_votes.{$key}.size"] = $limit === 1
                ? "У категорії “{$nomination['title']}” потрібно обрати одного номінанта."
                : "У категорії “{$nomination['title']}” потрібно обрати {$limit} номінантів.";
            $messages["oscar_votes.{$key}.*.distinct"] = "У категорії “{$nomination['title']}” номінанти не можуть повторюватися.";
        }

        $data = $request->validate($rules, $messages);
        $candidatesByNomination = $this->oscarCandidatesByNomination($vote);

        foreach (Vote::OSCAR_NOMINATIONS as $key => $nomination) {
            $allowedIds = $candidatesByNomination[$key]->pluck('id');
            $selectedIds = collect($data['oscar_votes'][$key]);

            if ($selectedIds->diff($allowedIds)->isNotEmpty()) {
                return back()
                    ->withInput()
                    ->withErrors(['oscar_votes' => "Оберіть номінантів для “{$nomination['title']}” тільки з цього заїзду."]);
            }
        }

        DB::transaction(function () use ($data, $vote, $user) {
            foreach (Vote::OSCAR_NOMINATIONS as $key => $nomination) {
                foreach ($data['oscar_votes'][$key] as $nomineeId) {
                    OscarVote::create([
                        'vote_id' => $vote->id,
                        'nomination' => $key,
                        'user_id' => $user->id,
                        'nominee_user_id' => $nomineeId,
                        'points' => 1,
                    ]);
                }
            }
        });

        return redirect()->route('votes.success');
    }

    private function oscarResult(Vote $vote)
    {
        $scores = OscarVote::where('vote_id', $vote->id)
            ->selectRaw('nomination, nominee_user_id, SUM(points) as total')
            ->groupBy('nomination', 'nominee_user_id')
            ->get()
            ->groupBy('nomination');

        $candidatesByNomination = $this->oscarCandidatesByNomination($vote);
        $results = collect();

        foreach (Vote::OSCAR_NOMINATIONS as $key => $nomination) {
            $scoreMap = ($scores[$key] ?? collect())->pluck('total', 'nominee_user_id');
            $nominees = $candidatesByNomination[$key]
                ->filter(fn (User $candidate) => (int) ($scoreMap[$candidate->id] ?? 0) > 0)
                ->map(function (User $candidate) use ($scoreMap) {
                    $candidate->oscar_score = (int) ($scoreMap[$candidate->id] ?? 0);

                    return $candidate;
                })
                ->sortBy('name')
                ->sortBy('oscar_score')
                ->values();

            $results[$key] = [
                'title' => $nomination['title'],
                'limit' => $nomination['limit'],
                'nominees' => $nominees,
                'maxScore' => $nominees->max('oscar_score') ?? 0,
            ];
        }

        return view('votes.oscar-result', compact('vote', 'results'));
    }

    private function oscarCandidatesByNomination(Vote $vote)
    {
        $candidates = User::where('session_id', $vote->session_id)
            ->orderBy('name')
            ->get();

        return collect(Vote::OSCAR_NOMINATIONS)->mapWithKeys(function (array $nomination, string $key) use ($candidates) {
            $nominationCandidates = $candidates;

            if ($nomination['gender']) {
                $nominationCandidates = $nominationCandidates
                    ->where('gender', $nomination['gender'])
                    ->values();
            }

            return [$key => $nominationCandidates];
        });
    }

    private function storeUploadedPhotos(Vote $vote, array $files): void
    {
        $directory = "images/votes/{$vote->vote_url}";
        File::ensureDirectoryExists(public_path($directory));

        $startOrder = (int) $vote->photos()->max('sort_order');

        foreach ($files as $index => $file) {
            $sortOrder = $startOrder + $index + 1;
            $extension = strtolower($file->getClientOriginalExtension());
            $fileName = str_pad((string) $sortOrder, 2, '0', STR_PAD_LEFT) . '-' . Str::random(12) . ".{$extension}";

            $file->move(public_path($directory), $fileName);

            VotePhoto::create([
                'vote_id' => $vote->id,
                'title' => "Фото {$sortOrder}",
                'image_path' => "{$directory}/{$fileName}",
                'sort_order' => $sortOrder,
            ]);
        }
    }
}
