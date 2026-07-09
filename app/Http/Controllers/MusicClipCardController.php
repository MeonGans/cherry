<?php

namespace App\Http\Controllers;

use App\Models\MusicClipCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MusicClipCardController extends Controller
{
    public function index()
    {
        $cards = MusicClipCard::orderBy('type')
            ->orderBy('name')
            ->get()
            ->groupBy('type');

        $genres = $cards->get(MusicClipCard::TYPE_GENRE, collect());
        $songs = $cards->get(MusicClipCard::TYPE_SONG, collect());

        return view('music-clip-cards.index', [
            'genres' => $genres,
            'songs' => $songs,
            'types' => MusicClipCard::TYPES,
            'totals' => [
                MusicClipCard::TYPE_GENRE => $genres->sum('quantity'),
                MusicClipCard::TYPE_SONG => $songs->sum('quantity'),
            ],
        ]);
    }

    public function create(Request $request)
    {
        $type = $request->query('type', MusicClipCard::TYPE_GENRE);

        if (!array_key_exists($type, MusicClipCard::TYPES)) {
            $type = MusicClipCard::TYPE_GENRE;
        }

        return view('music-clip-cards.create', [
            'type' => $type,
            'types' => MusicClipCard::TYPES,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(array_keys(MusicClipCard::TYPES))],
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
            'audio' => 'nullable|file|mimes:mp3,wav,ogg,m4a,aac,flac|max:20480',
        ]);

        $card = MusicClipCard::create([
            'type' => $validated['type'],
            'name' => $validated['name'],
            'quantity' => $validated['quantity'],
        ]);

        $this->storeImage($request, $card);
        $this->storeAudio($request, $card);

        return redirect()->route('music-clip-cards.index')->with('success', 'Картку додано.');
    }

    public function edit(MusicClipCard $musicClipCard)
    {
        return view('music-clip-cards.edit', [
            'card' => $musicClipCard,
        ]);
    }

    public function update(Request $request, MusicClipCard $musicClipCard)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'audio' => 'nullable|file|mimes:mp3,wav,ogg,m4a,aac,flac|max:20480',
        ]);

        $musicClipCard->update([
            'name' => $validated['name'],
        ]);

        $this->storeImage($request, $musicClipCard);
        $this->storeAudio($request, $musicClipCard);

        return redirect()->route('music-clip-cards.index')->with('success', 'Картку оновлено.');
    }

    public function quickUpdate(Request $request, MusicClipCard $musicClipCard)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $musicClipCard->update($validated);

        return redirect()->route('music-clip-cards.index')->with('success', 'Кількість оновлено.');
    }

    public function destroy(MusicClipCard $musicClipCard)
    {
        $this->deleteImage($musicClipCard->image_path);
        $this->deleteAudio($musicClipCard->audio_path);

        $musicClipCard->delete();

        return redirect()->route('music-clip-cards.index')->with('success', 'Картку видалено.');
    }

    private function storeImage(Request $request, MusicClipCard $card): void
    {
        if (!$request->hasFile('image')) {
            return;
        }

        $directory = public_path('fort/images/music-clip');
        File::ensureDirectoryExists($directory);

        $oldPath = $card->image_path;
        $file = $request->file('image');
        $filename = $card->id . '-' . Str::random(12) . '.' . $file->extension();

        $file->move($directory, $filename);

        $card->update([
            'image_path' => 'fort/images/music-clip/' . $filename,
        ]);

        $this->deleteImage($oldPath);
    }

    private function deleteImage(?string $path): void
    {
        if (!$path) {
            return;
        }

        $path = str_replace('\\', '/', $path);

        if (!str_starts_with($path, 'fort/images/music-clip/')) {
            return;
        }

        $fullPath = public_path($path);

        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }

    private function storeAudio(Request $request, MusicClipCard $card): void
    {
        if (!$request->hasFile('audio')) {
            return;
        }

        $directory = public_path('fort/audio/music-clip');
        File::ensureDirectoryExists($directory);

        $oldPath = $card->audio_path;
        $file = $request->file('audio');
        $filename = $card->id . '-' . Str::random(12) . '.' . $file->extension();

        $file->move($directory, $filename);

        $card->update([
            'audio_path' => 'fort/audio/music-clip/' . $filename,
        ]);

        $this->deleteAudio($oldPath);
    }

    private function deleteAudio(?string $path): void
    {
        if (!$path) {
            return;
        }

        $path = str_replace('\\', '/', $path);

        if (!str_starts_with($path, 'fort/audio/music-clip/')) {
            return;
        }

        $fullPath = public_path($path);

        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }
}
