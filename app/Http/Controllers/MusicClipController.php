<?php

namespace App\Http\Controllers;

use App\Models\MusicClipCard;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MusicClipController extends Controller
{
    private const VISIBLE_SLOTS = 7;
    private const TARGET_INDEX = 34;

    public function index()
    {
        $genres = $this->availableCards(MusicClipCard::TYPE_GENRE);
        $songs = $this->availableCards(MusicClipCard::TYPE_SONG);
        $canSpin = $genres->isNotEmpty() && $songs->isNotEmpty();
        $genre = $canSpin ? $this->pickWeightedCard($genres) : null;
        $song = $canSpin ? $this->pickWeightedCard($songs) : null;

        return view('music-clip', [
            'canSpin' => $canSpin,
            'genreCount' => $genres->count(),
            'songCount' => $songs->count(),
            'genreTotal' => $genres->sum('quantity'),
            'songTotal' => $songs->sum('quantity'),
            'genre' => $genre ? $this->presentCard($genre) : null,
            'song' => $song ? $this->presentCard($song) : null,
            'genreReelItems' => $genre ? $this->buildReelItems($genres, $genre) : collect(),
            'songReelItems' => $song ? $this->buildReelItems($songs, $song) : collect(),
            'targetIndex' => self::TARGET_INDEX,
            'visibleSlots' => self::VISIBLE_SLOTS,
            'targetSlot' => intdiv(self::VISIBLE_SLOTS, 2),
        ]);
    }

    public function catch(Request $request)
    {
        $validated = $request->validate([
            'genre_id' => 'required|integer|exists:music_clip_cards,id',
            'song_id' => 'required|integer|exists:music_clip_cards,id',
        ]);

        DB::transaction(function () use ($validated) {
            $cards = MusicClipCard::whereIn('id', [$validated['genre_id'], $validated['song_id']])
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $genre = $cards->get((int) $validated['genre_id']);
            $song = $cards->get((int) $validated['song_id']);

            if (!$genre || $genre->type !== MusicClipCard::TYPE_GENRE || $genre->quantity <= 0) {
                throw ValidationException::withMessages([
                    'genre_id' => 'Обраний жанр вже недоступний. Запустіть прокрут ще раз.',
                ]);
            }

            if (!$song || $song->type !== MusicClipCard::TYPE_SONG || $song->quantity <= 0) {
                throw ValidationException::withMessages([
                    'song_id' => 'Обрана пісня вже недоступна. Запустіть прокрут ще раз.',
                ]);
            }

            $genre->decrement('quantity');
            $song->decrement('quantity');
        });

        return redirect()
            ->route('music.clip')
            ->with('success', 'Жанр і пісню списано з бази.');
    }

    private function availableCards(string $type): Collection
    {
        return MusicClipCard::where('type', $type)
            ->where('quantity', '>', 0)
            ->orderBy('name')
            ->get();
    }

    private function pickWeightedCard(Collection $cards): MusicClipCard
    {
        $total = max(1, (int) $cards->sum('quantity'));
        $ticket = random_int(1, $total);
        $cursor = 0;

        foreach ($cards as $card) {
            $cursor += max(0, (int) $card->quantity);

            if ($ticket <= $cursor) {
                return $card;
            }
        }

        return $cards->last();
    }

    private function buildReelItems(Collection $cards, MusicClipCard $winner): Collection
    {
        $items = collect();
        $beforeCount = self::TARGET_INDEX;
        $afterCount = 44;

        for ($i = 0; $i < $beforeCount; $i++) {
            $items->push($this->presentCard($this->pickWeightedCard($cards)));
        }

        $items->push($this->presentCard($winner));

        for ($i = 0; $i < $afterCount; $i++) {
            $items->push($this->presentCard($this->pickWeightedCard($cards)));
        }

        return $items->values();
    }

    private function presentCard(MusicClipCard $card): array
    {
        return [
            'id' => $card->id,
            'name' => $card->name,
            'image_url' => $card->image_url,
            'quantity' => $card->quantity,
            'type' => $card->type,
            'type_label' => $card->type_label,
        ];
    }
}
