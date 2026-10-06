<?php

namespace App\Core\Http\Controllers;

use App\Core\Models\Setting;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class AvatarController extends Controller
{
    /**
     * Only raster image types are ever served back — anything else (an
     * SVG or HTML payload with a spoofed mime) would let an uploaded
     * "avatar" execute script on this origin.
     */
    public const ALLOWED_MIMES = ['image/png', 'image/jpeg', 'image/gif', 'image/webp'];

    public function show(User $user): BaseResponse
    {
        if (! $user->avatar_data) {
            return $this->default();
        }

        return $this->imageResponse($user->avatar_data, $user->avatar_mime);
    }

    public function default(): BaseResponse
    {
        $data = Setting::get('default_avatar_data');

        // Not seeded (e.g. SettingsSeeder skipped) — fall back to the bundled image
        if (! $data) {
            return redirect()->to(asset('assets/img/userlogo.png'));
        }

        return $this->imageResponse($data, Setting::get('default_avatar_mime'));
    }

    protected function imageResponse(string $base64, ?string $mime): Response
    {
        $mime = in_array($mime, self::ALLOWED_MIMES, true) ? $mime : 'image/png';

        return response(base64_decode($base64))
            ->header('Content-Type', $mime)
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('Cache-Control', 'private, max-age=86400');
    }
}
