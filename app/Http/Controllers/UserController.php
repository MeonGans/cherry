<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\ImageManager;
use Throwable;

class UserController extends Controller
{
    private const PROFILE_IMAGE_UPLOAD_LIMIT_KB = 15360;
    private const PROFILE_IMAGE_MAX_SIDE = 1600;
    private const PROFILE_IMAGE_WEBP_QUALITY = 82;
    private const PROFILE_IMAGE_JPEG_QUALITY = 85;

    public function index()
    {
        // Отримати користувачів, у яких сесія активна
        $users = User::with(['team.element'])
            ->whereHas('session', function($query) {
            $query->where('active', true);
        })->orderBy('team_id')->get();

        return view('list', compact('users'));
    }

    public function random()
    {
        // Отримати користувачів, у яких сесія активна
        $users = User::with(['team.element'])
            ->whereHas('session', function($query) {
            $query->where('active', true);
        })->inRandomOrder()->get();

        return view('random_list', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'session_id' => 'required|exists:sessions,id',
            'phone_number' => 'required|string|max:15',
            'date_of_birth' => 'required|date',
            'liceum_id' => 'required|exists:liceums,id',
            'team_id' => 'exists:teams,id',
            'gender' => 'required|in:male,female',
            'pin_code' => 'nullable|string|max:255|unique:users'
        ]);

        User::create($request->all());
        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', [
            'user' => $user,
            'teams' => Team::orderBy('id')->get(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'session_id' => 'sometimes|required|exists:sessions,id',
            'phone_number' => 'sometimes|required|string|max:15',
            'date_of_birth' => 'sometimes|required|date',
            'liceum_id' => 'sometimes|required|exists:liceums,id',
            'team_id' => 'sometimes|exists:teams,id',
            'desired_team_id' => 'nullable|integer|exists:teams,id',
            'gender' => 'sometimes|required|in:male,female',
            'pin_code' => 'nullable|string|max:255|unique:users,pin_code,' . $user->id,
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:' . self::PROFILE_IMAGE_UPLOAD_LIMIT_KB,
        ]);

        unset($validated['image']);

        $user->update($validated);
        $this->storeImage($request, $user);

        return redirect()->route('list')->with('success', 'Учня оновлено.');
    }

    public function destroy(User $user)
    {
        $this->deleteImage($user->image_path);

        $user->delete();
        return redirect()->route('list')->with('success', 'Учня видалено.');
    }

    private function storeImage(Request $request, User $user): void
    {
        if (!$request->hasFile('image')) {
            return;
        }

        $directory = public_path('images/users');
        File::ensureDirectoryExists($directory);

        $oldPath = $user->image_path;
        $file = $request->file('image');
        $relativePath = $this->saveProfileImage($file, $directory, $user->id);

        $user->update([
            'image_path' => $relativePath,
        ]);

        $this->deleteImage($oldPath);
    }

    private function saveProfileImage(UploadedFile $file, string $directory, int $userId): string
    {
        $baseName = $userId . '-' . Str::random(12);

        $driver = $this->imageOptimizationDriver();

        if ($driver) {
            $manager = $this->imageManager($driver);
            $extension = $this->supportsWebpOutput($driver) ? 'webp' : 'jpg';
            $filename = "{$baseName}.{$extension}";
            $fullPath = $directory . DIRECTORY_SEPARATOR . $filename;

            try {
                $this->saveOptimizedProfileImage($manager, $file, $fullPath, $extension);

                if ($this->optimizedFileIsSmaller($file, $fullPath)) {
                    return 'images/users/' . $filename;
                }

                File::delete($fullPath);
            } catch (Throwable $exception) {
                report($exception);

                if (File::exists($fullPath)) {
                    File::delete($fullPath);
                }
            }
        }

        $extension = $this->safeImageExtension($file);
        $filename = "{$baseName}.{$extension}";
        $file->move($directory, $filename);

        return 'images/users/' . $filename;
    }

    private function saveOptimizedProfileImage(
        ImageManager $manager,
        UploadedFile $file,
        string $path,
        string $extension
    ): void
    {
        $image = $manager
            ->read($file->getPathname())
            ->scaleDown(self::PROFILE_IMAGE_MAX_SIDE, self::PROFILE_IMAGE_MAX_SIDE);

        if ($extension === 'jpg') {
            $canvas = $manager
                ->create($image->width(), $image->height())
                ->fill('ffffff')
                ->place($image);

            $canvas->toJpeg(self::PROFILE_IMAGE_JPEG_QUALITY)->save($path);

            return;
        }

        $image->toWebp(self::PROFILE_IMAGE_WEBP_QUALITY)->save($path);
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

    private function optimizedFileIsSmaller(UploadedFile $file, string $path): bool
    {
        $originalSize = $file->getSize();

        if (!$originalSize || !File::exists($path)) {
            return true;
        }

        return File::size($path) < $originalSize;
    }

    private function safeImageExtension(UploadedFile $file): string
    {
        $extension = strtolower($file->extension() ?: $file->getClientOriginalExtension() ?: 'jpg');

        if ($extension === 'jpeg') {
            return 'jpg';
        }

        return in_array($extension, ['jpg', 'png', 'webp'], true) ? $extension : 'jpg';
    }

    private function deleteImage(?string $path): void
    {
        if (!$path) {
            return;
        }

        $path = str_replace('\\', '/', $path);

        if (!str_starts_with($path, 'images/users/')) {
            return;
        }

        $fullPath = public_path($path);

        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }
}
