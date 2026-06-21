<?php

namespace App\Http\Controllers;

use App\Models\PhotoVote;
use App\Models\Team;
use App\Models\User;
use App\Models\UserVote;
use App\Models\Vote;
use App\Models\VotePhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class VoteController extends Controller
{
    public function index()
    {
        $votes = Vote::withCount('photos')->latest()->get();

        return view('votes.index', compact('votes'));
    }

    public function create()
    {
        return view('votes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in([Vote::TYPE_TEAM, Vote::TYPE_PHOTO])],
        ]);

        if ($data['type'] === Vote::TYPE_PHOTO) {
            $request->validate([
                'photos' => ['required', 'array', 'size:10'],
                'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            ], [
                'photos.size' => 'Для фото-голосування потрібно завантажити 10 фото.',
            ]);
        }

        $voteUrl = Str::random(10);
        $vote = Vote::create([
            'name' => $data['name'],
            'vote_url' => $voteUrl,
            'type' => $data['type'],
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

        $teams = Team::with('element')->get();

        return view('votes.add-points', compact('vote', 'teams'));
    }

    public function addPoints(Request $request, $voteUrl)
    {
        $vote = Vote::where('vote_url', $voteUrl)->firstOrFail();

        if ($vote->isPhotoVote()) {
            return $this->addPhotoPoints($request, $vote);
        }

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
