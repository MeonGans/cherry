<?php

namespace App\Http\Controllers;

use App\Models\OscarVote;
use App\Models\OscarNominee;
use App\Models\PhotoVote;
use App\Models\Session as CampSession;
use App\Models\Team;
use App\Models\User;
use App\Models\UserVote;
use App\Models\Vote;
use App\Models\VotePhoto;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\ImageManager;
use Throwable;

class VoteController extends Controller
{
    private const PHOTO_FINALIST_LIMIT = 10;
    private const PHOTO_UPLOAD_LIMIT_KB = 30720;
    private const PHOTO_PREVIEW_MAX_SIDE = 1800;
    private const PHOTO_PREVIEW_WEBP_QUALITY = 82;
    private const PHOTO_PREVIEW_JPEG_QUALITY = 85;

    public function index()
    {
        $votes = Vote::with('session')
            ->withCount([
                'photos',
                'photos as finalist_photos_count' => fn ($query) => $query->where('is_finalist', true),
            ])
            ->latest()
            ->get();

        return view('votes.index', compact('votes'));
    }

    public function create()
    {
        $activeSession = CampSession::where('active', true)->first();
        $oscarCandidatesByNomination = $activeSession
            ? $this->oscarCandidatesByNominationForSession($activeSession->id)
            : collect();

        return view('votes.create', compact('activeSession', 'oscarCandidatesByNomination'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in([Vote::TYPE_TEAM, Vote::TYPE_PHOTO, Vote::TYPE_OSCAR])],
        ]);

        if ($data['type'] === Vote::TYPE_PHOTO && $request->hasFile('photos')) {
            $request->validate([
                'photos' => ['required', 'array', 'size:10'],
                'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            ], [
                'photos.size' => 'Для фото-голосування потрібно завантажити 10 фото.',
            ]);
        }

        $activeSession = CampSession::where('active', true)->first();

        if ($data['type'] === Vote::TYPE_OSCAR) {
            if (!$activeSession) {
                return back()
                    ->withInput()
                    ->withErrors(['type' => 'Спочатку активуйте сесію, щоб створити голосування “Оскар”.']);
            }

            $request->validate($this->oscarNomineeRules(), $this->oscarNomineeMessages());
            $this->validateOscarNomineesBelongToSession($request, $activeSession->id);
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

        if ($vote->isOscarVote()) {
            $this->storeOscarNominees($vote, $request->input('oscar_nominees', []));
        }

        if ($vote->isPhotoVote()) {
            return redirect()
                ->route('votes.photosForm', $voteUrl)
                ->with('success', 'Голосування створено. Посилання для завантаження фото готове.');
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
        $vote = Vote::where('vote_url', $voteUrl)->firstOrFail();
        $data = $request->validate([
            'pin_code' => ['required', 'string', 'max:255'],
        ]);

        $user = User::where('pin_code', $data['pin_code'])->first();

        if (!$user) {
            return redirect()->route('votes.show', $voteUrl)->withErrors(['message' => 'Невірний PIN-код.']);
        }

        if ($vote->session_id && (int) $user->session_id !== (int) $vote->session_id) {
            return redirect()
                ->route('votes.show', $voteUrl)
                ->withErrors(['message' => 'Цей PIN-код належить іншому заїзду.']);
        }

        return redirect()->route('votes.vote', ['voteUrl' => $voteUrl, 'userId' => $user->id]);
    }

    public function vote($voteUrl, $userId)
    {
        $vote = Vote::where('vote_url', $voteUrl)->firstOrFail();
        $user = User::findOrFail($userId);

        if ($vote->isPhotoVote()) {
            $photos = $vote->photos()->where('is_finalist', true)->get();
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

        $teams = Team::with('element')
            ->where('id', '!=', $user->team_id)
            ->where('id', '!=', 10)
            ->get();
        $alreadyVoted = UserVote::where('vote_id', $vote->id)
            ->where('user_id', $user->id)
            ->exists();

        return view('votes.vote', compact('vote', 'user', 'teams', 'alreadyVoted'));
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
                ->withErrors(['message' => 'Ви вже проголосували.']);
        }

        if ((int) $user->team_id === (int) $data['team_id']) {
            return redirect()
                ->route('votes.vote', ['voteUrl' => $voteUrl, 'userId' => $userId])
                ->withErrors(['message' => 'Не можна голосувати за власну команду.']);
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
        $showScores = request()->boolean('scores');

        if ($vote->isPhotoVote()) {
            return $this->photoResult($vote, $showScores);
        }

        if ($vote->isOscarVote()) {
            return $this->oscarResult($vote, $showScores);
        }

        $results = UserVote::where('vote_id', $vote->id)
            ->with('team.element')
            ->get()
            ->groupBy('team_id')
            ->map(function ($votes) {
                return $votes->sum('points');
            });

        $maxVotes = (int) ($results->max() ?? 0);

        $teams = Team::whereIn('id', $results->keys())
            ->with('element')
            ->get()
            ->map(function (Team $team) use ($results, $maxVotes) {
                $count = (int) ($results[$team->id] ?? 0);

                return [
                    'id' => $team->id,
                    'name' => $team->name,
                    'count' => $count,
                    'color' => $team->element?->color ?? '#4361ee',
                    'element_name' => $team->element?->name ?? $team->name,
                    'logo' => $this->elementLogoPath($team),
                    'is_winner' => $maxVotes > 0 && $count === $maxVotes,
                    'order' => $this->teamRevealOrder($team),
                ];
            })
            ->sortBy([
                ['order', 'asc'],
                ['name', 'asc'],
            ])
            ->values();

        return view('votes.result', compact('vote', 'teams', 'maxVotes', 'showScores'));
    }

    public function participation($voteUrl)
    {
        $vote = Vote::where('vote_url', $voteUrl)
            ->with('session')
            ->firstOrFail();
        $sessionResolution = $this->resolveParticipationSession($vote);
        $session = $sessionResolution['session'];
        $sessionNote = $sessionResolution['note'];
        $participants = collect();

        if ($session) {
            $selections = $this->participationSelections($vote);
            $participants = User::with(['team.element'])
                ->where('session_id', $session->id)
                ->get()
                ->sortBy(fn (User $user) => sprintf(
                    '%04d-%s',
                    $user->team_id ?? 9999,
                    mb_strtolower($user->name)
                ))
                ->values()
                ->map(function (User $user) use ($selections) {
                    $selection = $selections->get($user->id);

                    return [
                        'user' => $user,
                        'has_voted' => (bool) $selection,
                        'kind' => $selection['kind'] ?? null,
                        'summary' => $selection['summary'] ?? null,
                        'choices' => $selection['choices'] ?? collect(),
                        'voted_at' => $selection['voted_at'] ?? null,
                    ];
                });
        }

        $totalCount = $participants->count();
        $votedCount = $participants->where('has_voted', true)->count();
        $pendingCount = max(0, $totalCount - $votedCount);

        return view('votes.participation', compact(
            'vote',
            'session',
            'sessionNote',
            'participants',
            'totalCount',
            'votedCount',
            'pendingCount'
        ));
    }

    public function addPointsForm($voteUrl)
    {
        $vote = Vote::where('vote_url', $voteUrl)->firstOrFail();

        if ($vote->isPhotoVote()) {
            $photos = $vote->photos()->where('is_finalist', true)->get();
            $scoreTotals = PhotoVote::where('vote_id', $vote->id)
                ->selectRaw('vote_photo_id, SUM(points) as total')
                ->groupBy('vote_photo_id')
                ->pluck('total', 'vote_photo_id');

            return view('votes.add-photo-points', compact('vote', 'photos', 'scoreTotals'));
        }

        if ($vote->isOscarVote()) {
            $nominations = Vote::OSCAR_NOMINATIONS;
            $candidatesByNomination = $this->oscarCandidatesByNomination($vote);
            $scoreTotals = OscarVote::where('vote_id', $vote->id)
                ->selectRaw('nomination, nominee_user_id, SUM(points) as total')
                ->groupBy('nomination', 'nominee_user_id')
                ->get()
                ->mapWithKeys(fn (OscarVote $oscarVote) => [
                    $oscarVote->nomination . ':' . $oscarVote->nominee_user_id => (int) $oscarVote->total,
                ]);

            return view('votes.add-oscar-points', compact('vote', 'nominations', 'candidatesByNomination', 'scoreTotals'));
        }

        $teams = Team::with('element')->get();
        $scoreTotals = UserVote::where('vote_id', $vote->id)
            ->selectRaw('team_id, SUM(points) as total')
            ->groupBy('team_id')
            ->pluck('total', 'team_id');

        return view('votes.add-points', compact('vote', 'teams', 'scoreTotals'));
    }

    public function addPoints(Request $request, $voteUrl)
    {
        $vote = Vote::where('vote_url', $voteUrl)->firstOrFail();

        if ($vote->isPhotoVote()) {
            return $this->addPhotoPoints($request, $vote);
        }

        if ($vote->isOscarVote()) {
            return $this->addOscarPoints($request, $vote);
        }

        $data = $request->validate([
            'points' => ['required', 'array'],
            'points.*' => ['nullable', 'integer', 'min:0'],
        ]);

        $pointsByTeam = $this->positivePointInputs($data['points']);

        if ($pointsByTeam->isEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['points' => 'Вкажіть бали хоча б для однієї команди.']);
        }

        $teams = Team::whereIn('id', $pointsByTeam->keys())->pluck('id');

        if ($teams->count() !== $pointsByTeam->count()) {
            return back()
                ->withInput()
                ->withErrors(['points' => 'Одна з команд не знайдена. Оновіть сторінку та спробуйте ще раз.']);
        }

        DB::transaction(function () use ($vote, $pointsByTeam) {
            $pointsByTeam->each(function (int $points, int $teamId) use ($vote) {
                UserVote::create([
                    'vote_id' => $vote->id,
                    'team_id' => $teamId,
                    'user_id' => auth()->id() ?? null,
                    'points' => $points,
                ]);
            });
        });

        return redirect()
            ->route('votes.addPointsForm', $vote->vote_url)
            ->with('success', 'Бали журі додано: ' . $pointsByTeam->sum() . '.');
    }

    public function photosForm($voteUrl)
    {
        $vote = Vote::where('vote_url', $voteUrl)
            ->with(['photos.user'])
            ->firstOrFail();
        abort_unless($vote->isPhotoVote(), 404);

        $photos = $vote->photos()
            ->with('user')
            ->orderByDesc('is_finalist')
            ->orderBy('sort_order')
            ->get();
        $finalistCount = $photos->where('is_finalist', true)->count();
        $uploadUrl = route('votes.photoUpload', $vote->vote_url);

        return view('votes.photos', compact('vote', 'photos', 'finalistCount', 'uploadUrl'));
    }

    public function storePhotos(Request $request, $voteUrl)
    {
        $vote = Vote::where('vote_url', $voteUrl)->firstOrFail();
        abort_unless($vote->isPhotoVote(), 404);

        $remainingSlots = self::PHOTO_FINALIST_LIMIT - $vote->photos()->where('is_finalist', true)->count();

        if ($remainingSlots <= 0) {
            return back()->withErrors(['photos' => 'Для цього голосування вже завантажено 10 фото.']);
        }

        $request->validate([
            'photos' => ['required', 'array', 'max:' . $remainingSlots],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:' . self::PHOTO_UPLOAD_LIMIT_KB],
        ], [
            'photos.max' => "Можна додати ще не більше {$remainingSlots} фото.",
        ]);

        $this->storeUploadedPhotos($vote, $request->file('photos', []), null, true);

        return redirect()
            ->route('votes.photosForm', $vote->vote_url)
            ->with('success', 'Фото додано у фінал.');
    }

    public function updatePhotoFinalists(Request $request, $voteUrl)
    {
        $vote = Vote::where('vote_url', $voteUrl)->firstOrFail();
        abort_unless($vote->isPhotoVote(), 404);

        $data = $request->validate([
            'photo_ids' => ['nullable', 'array', 'max:' . self::PHOTO_FINALIST_LIMIT],
            'photo_ids.*' => ['integer', 'distinct', 'exists:vote_photos,id'],
        ], [
            'photo_ids.max' => 'У фінал можна обрати не більше 10 фото.',
            'photo_ids.*.distinct' => 'Одне фото не можна обрати двічі.',
        ]);

        $photoIds = collect($data['photo_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->values();

        $validPhotoIds = $vote->photos()
            ->whereIn('id', $photoIds)
            ->pluck('id');

        if ($validPhotoIds->count() !== $photoIds->count()) {
            return back()->withErrors(['photo_ids' => 'Одне з фото не належить цьому голосуванню.']);
        }

        DB::transaction(function () use ($vote, $photoIds) {
            $vote->photos()->update([
                'is_finalist' => false,
                'finalist_selected_at' => null,
            ]);

            $photoIds->each(function (int $photoId, int $index) use ($vote) {
                $vote->photos()
                    ->where('id', $photoId)
                    ->update([
                        'is_finalist' => true,
                        'finalist_selected_at' => now(),
                        'sort_order' => $index + 1,
                    ]);
            });
        });

        return redirect()
            ->route('votes.photosForm', $vote->vote_url)
            ->with('success', 'Фіналістів оновлено.');
    }

    public function photoUploadForm($voteUrl)
    {
        $vote = Vote::where('vote_url', $voteUrl)->firstOrFail();
        abort_unless($vote->isPhotoVote(), 404);

        return view('votes.photo-upload', compact('vote'));
    }

    public function storePhotoSubmission(Request $request, $voteUrl)
    {
        $vote = Vote::where('vote_url', $voteUrl)->firstOrFail();
        abort_unless($vote->isPhotoVote(), 404);

        $data = $request->validate([
            'pin_code' => ['required', 'string', 'max:255'],
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:' . self::PHOTO_UPLOAD_LIMIT_KB],
        ]);

        $user = User::where('pin_code', $data['pin_code'])->first();

        if (!$user) {
            return back()
                ->withInput()
                ->withErrors(['pin_code' => 'Невірний PIN-код.']);
        }

        if ($vote->session_id && (int) $user->session_id !== (int) $vote->session_id) {
            return back()
                ->withInput()
                ->withErrors(['pin_code' => 'Цей PIN-код належить іншому заїзду.']);
        }

        $alreadyUploaded = $vote->photos()
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyUploaded) {
            return back()
                ->withInput()
                ->withErrors(['photo' => 'За цим PIN-кодом фото вже завантажено.']);
        }

        $this->storeUploadedPhotos($vote, [$request->file('photo')], $user, false);

        return redirect()
            ->route('votes.photoUpload', $vote->vote_url)
            ->with('success', 'Фото завантажено. Дякуємо!');
    }

    public function printPhoto(VotePhoto $votePhoto)
    {
        $votePhoto->load(['vote', 'user']);

        return view('votes.photo-print', compact('votePhoto'));
    }

    public function destroyPhoto(VotePhoto $votePhoto)
    {
        $votePhoto->load('vote');
        $voteUrl = $votePhoto->vote?->vote_url;
        $paths = [
            $votePhoto->image_path,
            $votePhoto->original_image_path,
        ];

        $votePhoto->delete();
        $this->deleteVotePhotoFiles($paths);

        return redirect()
            ->route('votes.photosForm', $voteUrl)
            ->with('success', 'Фото видалено. Учень може завантажити нове фото за своїм PIN-кодом.');
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
            'photo_ids' => ['required', 'array', 'size:1'],
            'photo_ids.*' => ['integer', 'distinct', 'exists:vote_photos,id'],
        ], [
            'photo_ids.required' => 'Оберіть фото.',
            'photo_ids.size' => 'Оберіть одне фото.',
            'photo_ids.*.distinct' => 'Оберіть одне фото.',
        ]);

        $photoIds = VotePhoto::where('vote_id', $vote->id)
            ->where('is_finalist', true)
            ->whereIn('id', $data['photo_ids'])
            ->pluck('id');

        if ($photoIds->count() !== 1) {
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
            'points' => ['required', 'array'],
            'points.*' => ['nullable', 'integer', 'min:0'],
        ]);

        $pointsByPhoto = $this->positivePointInputs($data['points']);

        if ($pointsByPhoto->isEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['points' => 'Вкажіть бали хоча б для одного фото.']);
        }

        $photos = $vote->photos()
            ->where('is_finalist', true)
            ->whereIn('id', $pointsByPhoto->keys())
            ->pluck('id');

        if ($photos->count() !== $pointsByPhoto->count()) {
            return back()
                ->withInput()
                ->withErrors(['points' => 'Одне з фото не належить цьому голосуванню. Оновіть сторінку та спробуйте ще раз.']);
        }

        DB::transaction(function () use ($vote, $pointsByPhoto) {
            $pointsByPhoto->each(function (int $points, int $photoId) use ($vote) {
                PhotoVote::create([
                    'vote_id' => $vote->id,
                    'vote_photo_id' => $photoId,
                    'user_id' => auth()->id() ?? null,
                    'source' => PhotoVote::SOURCE_JURY,
                    'points' => $points,
                ]);
            });
        });

        return redirect()
            ->route('votes.addPointsForm', $vote->vote_url)
            ->with('success', 'Бали журі додано: ' . $pointsByPhoto->sum() . '.');
    }

    private function addOscarPoints(Request $request, Vote $vote)
    {
        $data = $request->validate([
            'points' => ['required', 'array'],
            'points.*' => ['array'],
            'points.*.*' => ['nullable', 'integer', 'min:0'],
        ]);

        $candidatesByNomination = $this->oscarCandidatesByNomination($vote);
        $pointRows = collect();

        foreach ($data['points'] as $nomination => $candidatePoints) {
            if (!array_key_exists($nomination, Vote::OSCAR_NOMINATIONS)) {
                return back()
                    ->withInput()
                    ->withErrors(['points' => 'Одна з номінацій не знайдена. Оновіть сторінку та спробуйте ще раз.']);
            }

            $pointsByCandidate = $this->positivePointInputs($candidatePoints);
            $allowedIds = ($candidatesByNomination[$nomination] ?? collect())->pluck('id');

            if ($pointsByCandidate->keys()->diff($allowedIds)->isNotEmpty()) {
                return back()
                    ->withInput()
                    ->withErrors(['points' => 'Один з номінантів не належить до своєї номінації. Оновіть сторінку та спробуйте ще раз.']);
            }

            $pointsByCandidate->each(function (int $points, int $candidateId) use ($nomination, $pointRows) {
                $pointRows->push([
                    'nomination' => $nomination,
                    'nominee_user_id' => $candidateId,
                    'points' => $points,
                ]);
            });
        }

        if ($pointRows->isEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['points' => 'Вкажіть бали хоча б для одного номінанта.']);
        }

        DB::transaction(function () use ($vote, $pointRows) {
            $pointRows->each(function (array $row) use ($vote) {
                OscarVote::create([
                    'vote_id' => $vote->id,
                    'nomination' => $row['nomination'],
                    'user_id' => null,
                    'nominee_user_id' => $row['nominee_user_id'],
                    'points' => $row['points'],
                ]);
            });
        });

        return redirect()
            ->route('votes.addPointsForm', $vote->vote_url)
            ->with('success', 'Бали журі додано: ' . $pointRows->sum('points') . '.');
    }

    private function elementLogoPath(Team $team): string
    {
        return $team->element_logo_path;
    }

    private function positivePointInputs(array $points)
    {
        return collect($points)
            ->mapWithKeys(fn ($points, $id) => [(int) $id => (int) $points])
            ->filter(fn (int $points, int $id) => $id > 0 && $points > 0);
    }

    private function teamRevealOrder(Team $team): int
    {
        $name = mb_strtolower(trim(($team->element?->name ?? '') . ' ' . $team->name));

        $order = [
            'вогонь' => 10,
            'повітря' => 20,
            'вода' => 30,
            'земля' => 40,
            'метал' => 50,
        ];

        foreach ($order as $needle => $position) {
            if (str_contains($name, $needle)) {
                return $position;
            }
        }

        return 1000 + $team->id;
    }

    private function resolveParticipationSession(Vote $vote): array
    {
        if ($vote->session_id) {
            return [
                'session' => $vote->session ?: CampSession::find($vote->session_id),
                'note' => null,
            ];
        }

        $sessionIds = $this->voterSessionIds($vote);

        if ($sessionIds->count() === 1) {
            return [
                'session' => CampSession::find($sessionIds->first()),
                'note' => 'Голосування не має прямої привʼязки до заїзду, тому заїзд визначено за вже поданими голосами.',
            ];
        }

        if ($sessionIds->count() > 1) {
            return [
                'session' => null,
                'note' => 'Не вдалося однозначно визначити заїзд: у голосуванні є учасники з різних сесій.',
            ];
        }

        $activeSession = CampSession::where('active', true)->first();

        return [
            'session' => $activeSession,
            'note' => $activeSession
                ? 'Голосування ще не має голосів і прямої привʼязки до заїзду, тому показано поточну активну сесію.'
                : 'Голосування не має прямої привʼязки до заїзду, а активної сесії зараз немає.',
        ];
    }

    private function voterSessionIds(Vote $vote)
    {
        $userIds = $this->voterUserIds($vote);

        if ($userIds->isEmpty()) {
            return collect();
        }

        return User::whereIn('id', $userIds)
            ->pluck('session_id')
            ->filter()
            ->unique()
            ->values();
    }

    private function voterUserIds(Vote $vote)
    {
        if ($vote->isPhotoVote()) {
            return PhotoVote::where('vote_id', $vote->id)
                ->where('source', PhotoVote::SOURCE_USER)
                ->whereNotNull('user_id')
                ->pluck('user_id')
                ->unique()
                ->values();
        }

        if ($vote->isOscarVote()) {
            return OscarVote::where('vote_id', $vote->id)
                ->whereNotNull('user_id')
                ->pluck('user_id')
                ->unique()
                ->values();
        }

        return UserVote::where('vote_id', $vote->id)
            ->whereNotNull('user_id')
            ->pluck('user_id')
            ->unique()
            ->values();
    }

    private function participationSelections(Vote $vote)
    {
        if ($vote->isPhotoVote()) {
            return $this->photoParticipationSelections($vote);
        }

        if ($vote->isOscarVote()) {
            return $this->oscarParticipationSelections($vote);
        }

        return $this->teamParticipationSelections($vote);
    }

    private function teamParticipationSelections(Vote $vote)
    {
        return UserVote::where('vote_id', $vote->id)
            ->whereNotNull('user_id')
            ->with('team.element')
            ->get()
            ->keyBy('user_id')
            ->map(function (UserVote $userVote) {
                $teamName = $userVote->team?->name ?? 'Команда видалена';

                return [
                    'kind' => Vote::TYPE_TEAM,
                    'summary' => $teamName,
                    'choices' => collect([
                        [
                            'label' => 'Команда',
                            'value' => $teamName,
                            'color' => $userVote->team?->element?->color ?? '#4361ee',
                        ],
                    ]),
                    'voted_at' => $userVote->created_at,
                ];
            });
    }

    private function photoParticipationSelections(Vote $vote)
    {
        return PhotoVote::where('vote_id', $vote->id)
            ->where('source', PhotoVote::SOURCE_USER)
            ->whereNotNull('user_id')
            ->with('photo')
            ->orderBy('created_at')
            ->get()
            ->groupBy('user_id')
            ->map(function ($photoVotes) {
                $photos = $photoVotes
                    ->pluck('photo')
                    ->filter()
                    ->map(fn (VotePhoto $photo) => [
                        'title' => $photo->title,
                        'image_url' => asset($photo->image_path),
                    ])
                    ->values();

                return [
                    'kind' => Vote::TYPE_PHOTO,
                    'summary' => $photos->pluck('title')->implode(', '),
                    'choices' => $photos,
                    'voted_at' => $photoVotes->min('created_at'),
                ];
            });
    }

    private function oscarParticipationSelections(Vote $vote)
    {
        $nominationOrder = array_keys(Vote::OSCAR_NOMINATIONS);

        return OscarVote::where('vote_id', $vote->id)
            ->whereNotNull('user_id')
            ->with('nominee')
            ->get()
            ->groupBy('user_id')
            ->map(function ($oscarVotes) use ($nominationOrder) {
                $choices = collect($nominationOrder)
                    ->map(function (string $nominationKey) use ($oscarVotes) {
                        $nominees = $oscarVotes
                            ->where('nomination', $nominationKey)
                            ->pluck('nominee')
                            ->filter()
                            ->pluck('name')
                            ->values();

                        if ($nominees->isEmpty()) {
                            return null;
                        }

                        return [
                            'title' => Vote::OSCAR_NOMINATIONS[$nominationKey]['title'] ?? $nominationKey,
                            'nominees' => $nominees,
                        ];
                    })
                    ->filter()
                    ->values();

                return [
                    'kind' => Vote::TYPE_OSCAR,
                    'summary' => $choices
                        ->map(fn (array $choice) => $choice['title'] . ': ' . $choice['nominees']->implode(', '))
                        ->implode('; '),
                    'choices' => $choices,
                    'voted_at' => $oscarVotes->min('created_at'),
                ];
            });
    }

    private function photoResult(Vote $vote, bool $showScores = false)
    {
        $scores = PhotoVote::where('vote_id', $vote->id)
            ->selectRaw('vote_photo_id, SUM(points) as total')
            ->groupBy('vote_photo_id')
            ->pluck('total', 'vote_photo_id');

        $photos = $vote->photos()
            ->with('user')
            ->where('is_finalist', true)
            ->get()
            ->map(function (VotePhoto $photo) use ($scores) {
                $photo->score = (int) ($scores[$photo->id] ?? 0);

                return $photo;
            });

        $maxVotes = $photos->max('score') ?? 0;
        $winnerPhoto = $maxVotes > 0
            ? $photos
                ->sortBy([
                    ['score', 'desc'],
                    ['id', 'asc'],
                ])
                ->first()
            : null;
        $photos = $photos->sortBy([
            ['score', 'asc'],
            ['id', 'asc'],
        ])->values();

        return view('votes.photo-result', compact('vote', 'photos', 'maxVotes', 'showScores', 'winnerPhoto'));
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

    private function oscarResult(Vote $vote, bool $showScores = false)
    {
        $results = collect();

        foreach (Vote::OSCAR_NOMINATIONS as $key => $nomination) {
            $scoreTotals = OscarVote::query()
                ->where('vote_id', $vote->id)
                ->where('nomination', $key)
                ->selectRaw('nominee_user_id, SUM(points) as total')
                ->groupBy('nominee_user_id');

            $nominees = User::query()
                ->join('oscar_nominees', 'oscar_nominees.user_id', '=', 'users.id')
                ->leftJoinSub($scoreTotals, 'score_totals', function ($join) {
                    $join->on('score_totals.nominee_user_id', '=', 'users.id');
                })
                ->where('oscar_nominees.vote_id', $vote->id)
                ->where('oscar_nominees.nomination', $key)
                ->select('users.*')
                ->selectRaw('COALESCE(score_totals.total, 0) as oscar_score')
                ->get()
                ->map(function (User $candidate) use ($vote, $key) {
                    $candidate->oscar_score = (int) $candidate->oscar_score;
                    $candidate->oscar_display_order = (int) sprintf(
                        '%u',
                        crc32($vote->id . '|' . $key . '|' . $candidate->id)
                    );

                    return $candidate;
                })
                ->filter(fn (User $candidate) => $candidate->oscar_score > 0)
                ->sortBy($showScores
                    ? [
                        ['oscar_score', 'asc'],
                        ['name', 'asc'],
                    ]
                    : [
                        ['oscar_display_order', 'asc'],
                        ['name', 'asc'],
                    ])
                ->values();

            $winnerScore = $nominees->reduce(
                fn (int $maxScore, User $candidate) => max($maxScore, (int) $candidate->oscar_score),
                0
            );

            $nominees = $nominees
                ->map(function (User $candidate) use ($winnerScore) {
                    $candidate->is_oscar_winner = $winnerScore > 0
                        && (int) $candidate->oscar_score === $winnerScore;

                    return $candidate;
                })
                ->values();

            $results[$key] = [
                'title' => $nomination['title'],
                'limit' => $nomination['limit'],
                'nominees' => $nominees,
                'maxScore' => $winnerScore,
            ];
        }

        return view('votes.oscar-result', compact('vote', 'results', 'showScores'));
    }

    private function oscarCandidatesByNomination(Vote $vote)
    {
        $nominees = $vote->oscarNominees()
            ->with('user')
            ->get()
            ->groupBy('nomination');

        return collect(Vote::OSCAR_NOMINATIONS)->mapWithKeys(function (array $nomination, string $key) use ($nominees) {
            $nominationNominees = ($nominees[$key] ?? collect())
                ->pluck('user')
                ->filter()
                ->sortBy('name')
                ->values();

            return [$key => $nominationNominees];
        });
    }

    private function oscarCandidatesByNominationForSession(int $sessionId)
    {
        $candidates = User::where('session_id', $sessionId)
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

    private function oscarNomineeRules(): array
    {
        $rules = [];

        foreach (Vote::OSCAR_NOMINATIONS as $key => $nomination) {
            $rules["oscar_nominees.{$key}"] = ['required', 'array', 'min:' . $nomination['limit']];
            $rules["oscar_nominees.{$key}.*"] = ['integer', 'distinct', 'exists:users,id'];
        }

        return $rules;
    }

    private function oscarNomineeMessages(): array
    {
        $messages = [];

        foreach (Vote::OSCAR_NOMINATIONS as $key => $nomination) {
            $messages["oscar_nominees.{$key}.required"] = "Оберіть номінантів для “{$nomination['title']}”.";
            $messages["oscar_nominees.{$key}.min"] = "Для “{$nomination['title']}” потрібно обрати щонайменше {$nomination['limit']} номінантів.";
            $messages["oscar_nominees.{$key}.*.distinct"] = "У “{$nomination['title']}” номінанти не можуть повторюватися.";
        }

        return $messages;
    }

    private function validateOscarNomineesBelongToSession(Request $request, int $sessionId): void
    {
        $candidatesByNomination = $this->oscarCandidatesByNominationForSession($sessionId);

        foreach (Vote::OSCAR_NOMINATIONS as $key => $nomination) {
            $allowedIds = $candidatesByNomination[$key]->pluck('id');
            $selectedIds = collect($request->input("oscar_nominees.{$key}", []));

            if ($selectedIds->diff($allowedIds)->isNotEmpty()) {
                throw ValidationException::withMessages([
                    "oscar_nominees.{$key}" => "Номінанти для “{$nomination['title']}” мають бути з активної сесії та відповідної категорії.",
                ]);
            }
        }
    }

    private function storeOscarNominees(Vote $vote, array $nomineesByNomination): void
    {
        foreach (Vote::OSCAR_NOMINATIONS as $key => $nomination) {
            foreach ($nomineesByNomination[$key] ?? [] as $userId) {
                OscarNominee::create([
                    'vote_id' => $vote->id,
                    'nomination' => $key,
                    'user_id' => $userId,
                ]);
            }
        }
    }

    private function storeUploadedPhotos(Vote $vote, array $files, ?User $user = null, bool $isFinalist = true): void
    {
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $this->storeUploadedPhoto($vote, $file, $user, $isFinalist);
            }
        }
    }

    private function storeUploadedPhoto(Vote $vote, UploadedFile $file, ?User $user, bool $isFinalist): VotePhoto
    {
        $baseDirectory = "images/votes/{$vote->vote_url}";
        $originalDirectory = "{$baseDirectory}/originals";
        $previewDirectory = "{$baseDirectory}/preview";

        File::ensureDirectoryExists(public_path($originalDirectory));
        File::ensureDirectoryExists(public_path($previewDirectory));

        $sortOrder = ((int) $vote->photos()->max('sort_order')) + 1;
        $extension = $this->safePhotoExtension($file);
        $baseName = str_pad((string) $sortOrder, 2, '0', STR_PAD_LEFT) . '-' . Str::random(12);
        $originalFileName = "{$baseName}.{$extension}";
        $originalRelativePath = "{$originalDirectory}/{$originalFileName}";
        $originalFullPath = public_path($originalRelativePath);

        $file->move(public_path($originalDirectory), $originalFileName);

        $previewRelativePath = $this->savePhotoPreview(
            $originalFullPath,
            $previewDirectory,
            $baseName,
            $extension
        );

        return VotePhoto::create([
            'vote_id' => $vote->id,
            'user_id' => $user?->id,
            'title' => "Фото {$sortOrder}",
            'image_path' => $previewRelativePath,
            'original_image_path' => $originalRelativePath,
            'is_finalist' => $isFinalist,
            'finalist_selected_at' => $isFinalist ? now() : null,
            'sort_order' => $sortOrder,
        ]);
    }

    private function savePhotoPreview(
        string $originalFullPath,
        string $previewRelativeDirectory,
        string $baseName,
        string $originalExtension
    ): string {
        $driver = $this->imageOptimizationDriver();

        if ($driver) {
            $manager = $this->imageManager($driver);
            $extension = $this->supportsWebpOutput($driver) ? 'webp' : 'jpg';
            $previewRelativePath = "{$previewRelativeDirectory}/{$baseName}.{$extension}";
            $previewFullPath = public_path($previewRelativePath);

            try {
                $image = $manager
                    ->read($originalFullPath)
                    ->scaleDown(self::PHOTO_PREVIEW_MAX_SIDE, self::PHOTO_PREVIEW_MAX_SIDE);

                if ($extension === 'jpg') {
                    $canvas = $manager
                        ->create($image->width(), $image->height())
                        ->fill('ffffff')
                        ->place($image);

                    $canvas->toJpeg(self::PHOTO_PREVIEW_JPEG_QUALITY)->save($previewFullPath);

                    return $previewRelativePath;
                }

                $image->toWebp(self::PHOTO_PREVIEW_WEBP_QUALITY)->save($previewFullPath);

                return $previewRelativePath;
            } catch (Throwable $exception) {
                report($exception);

                if (File::exists($previewFullPath)) {
                    File::delete($previewFullPath);
                }
            }
        }

        $fallbackRelativePath = "{$previewRelativeDirectory}/{$baseName}.{$originalExtension}";
        File::copy($originalFullPath, public_path($fallbackRelativePath));

        return $fallbackRelativePath;
    }

    private function deleteVotePhotoFiles(array $paths): void
    {
        collect($paths)
            ->filter()
            ->map(fn (string $path) => str_replace('\\', '/', $path))
            ->unique()
            ->filter(fn (string $path) => str_starts_with($path, 'images/votes/'))
            ->each(function (string $path) {
                $fullPath = public_path($path);

                if (File::exists($fullPath)) {
                    File::delete($fullPath);
                }
            });
    }

    private function imageOptimizationDriver(): ?string
    {
        if (extension_loaded('imagick') && class_exists('Imagick')) {
            return 'imagick';
        }

        if (extension_loaded('gd')) {
            return 'gd';
        }

        return null;
    }

    private function imageManager(string $driver): ImageManager
    {
        return $driver === 'imagick'
            ? new ImageManager(ImagickDriver::class)
            : new ImageManager(GdDriver::class);
    }

    private function supportsWebpOutput(string $driver): bool
    {
        if ($driver === 'imagick') {
            try {
                return class_exists('Imagick') && in_array('WEBP', \Imagick::queryFormats('WEBP'), true);
            } catch (Throwable) {
                return false;
            }
        }

        return function_exists('imagetypes') && defined('IMG_WEBP') && (imagetypes() & IMG_WEBP) !== 0;
    }

    private function safePhotoExtension(UploadedFile $file): string
    {
        $extension = strtolower($file->extension() ?: $file->getClientOriginalExtension() ?: 'jpg');

        if ($extension === 'jpeg') {
            return 'jpg';
        }

        return in_array($extension, ['jpg', 'png', 'webp'], true) ? $extension : 'jpg';
    }
}
