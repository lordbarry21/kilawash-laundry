<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->validate([
            'avatar_data' => ['nullable', 'string', 'max:450000'],
            'avatar'      => ['nullable', 'file', 'max:4096'],
        ]);

        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->boolean('remove_avatar')) {
            $user->avatar = null;
        } elseif ($request->filled('avatar_data')) {
            $dataUri = trim((string) $request->input('avatar_data'));

            if (! preg_match('/^data:image\/(jpeg|jpg|png|webp);base64,([A-Za-z0-9+\/=]+)$/', $dataUri, $matches)) {
                throw ValidationException::withMessages([
                    'avatar' => 'Format foto profil tidak valid. Gunakan gambar JPG, PNG, atau WebP.',
                ]);
            }

            $decoded = base64_decode($matches[2], true);
            if ($decoded === false || strlen($decoded) < 32) {
                throw ValidationException::withMessages([
                    'avatar' => 'Data gambar rusak. Silakan pilih foto lain.',
                ]);
            }

            $user->avatar = $dataUri;
        } elseif ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $path = $file->getRealPath();
            $info = $path ? @getimagesize($path) : false;

            if (! is_array($info) || ! in_array($info['mime'] ?? '', ['image/jpeg', 'image/png', 'image/webp'], true)) {
                throw ValidationException::withMessages([
                    'avatar' => 'File harus berupa gambar JPG, PNG, atau WebP.',
                ]);
            }

            $user->avatar = 'data:' . $info['mime'] . ';base64,' . base64_encode((string) file_get_contents($path));
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
