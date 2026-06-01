<?php

namespace App\Http\Controllers;

use App\Helpers\Settings;
use Illuminate\Support\Facades\Storage;

class PwaController extends Controller
{
    public function manifest()
    {
        $iconUrl = url('pwa-icon-192.png');

        return response()->json([
            'name'             => config('app.name') . ' — Teacher Portal',
            'short_name'       => 'Teacher Portal',
            'description'      => 'Mobile teaching portal for JN Nazareth School CMS',
            'start_url'        => '/teacher',
            'display'          => 'standalone',
            'background_color' => '#f0f4f8',
            'theme_color'      => '#0f766e',
            'orientation'      => 'portrait-primary',
            'scope'            => '/',
            'categories'       => ['education', 'productivity'],
            'lang'             => 'en',
            'icons'            => [
                [
                    'src'     => url('pwa-icon-192.png'),
                    'sizes'   => '192x192',
                    'type'    => 'image/png',
                    'purpose' => 'any maskable',
                ],
                [
                    'src'     => url('pwa-icon-512.png'),
                    'sizes'   => '512x512',
                    'type'    => 'image/png',
                    'purpose' => 'any maskable',
                ],
            ],
        ]);
    }

    public function icon(int $size)
    {
        $size = in_array($size, [192, 512]) ? $size : 192;

        $logoPath = Settings::get('school_logo');

        if ($logoPath && Storage::disk('public')->exists($logoPath)) {
            $src = Storage::disk('public')->path($logoPath);
            $img = @imagecreatefromstring((string) file_get_contents($src));
        } else {
            $default = public_path('images/logo.png');
            $img = file_exists($default) ? @imagecreatefrompng($default) : null;
        }

        if (!$img) {
            return response(base64_decode(
                'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='
            ), 200, ['Content-Type' => 'image/png']);
        }

        $w = imagesx($img);
        $h = imagesy($img);
        $min = min($w, $h);
        $srcX = (int) (($w - $min) / 2);
        $srcY = (int) (($h - $min) / 2);

        $square = imagecreatetruecolor($size, $size);
        imagecopyresampled($square, $img, 0, 0, $srcX, $srcY, $size, $size, $min, $min);
        imagedestroy($img);

        ob_start();
        imagepng($square);
        $png = ob_get_clean();
        imagedestroy($square);

        return response($png, 200, [
            'Content-Type'  => 'image/png',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
