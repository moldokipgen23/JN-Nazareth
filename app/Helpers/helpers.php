<?php

use App\Helpers\Settings;

if (! function_exists('setting')) {
    /**
     * Fetch a school/site setting value.
     */
    function setting(string $key, mixed $default = ''): mixed
    {
        return Settings::get($key, $default);
    }
}

if (! function_exists('login_path')) {
    /**
     * The configurable login URL slug for a portal ('admin' or 'teacher').
     * Read from site settings, with safe defaults if the settings table
     * is not available yet (e.g. before the first migration).
     */
    function login_path(string $which): string
    {
        $defaults = ['admin' => 'admin-portal', 'teacher' => 'teacher-portal'];
        $default  = $defaults[$which] ?? 'admin-portal';

        try {
            $raw = (string) Settings::get($which . '_login_path', '');
        } catch (\Throwable $e) {
            $raw = '';
        }

        $slug = trim($raw, " \t\n\r/");

        // Only allow safe URL-slug characters; fall back if empty/invalid.
        return ($slug !== '' && preg_match('/^[A-Za-z0-9\-_\/]+$/', $slug))
            ? $slug
            : $default;
    }
}

if (! function_exists('admission_items')) {
    /**
     * The Admission page information items (Eligibility, Documents, …).
     * Editable in Site Customizer → Admission. Falls back to defaults.
     */
    function admission_items(): array
    {
        $raw = json_decode((string) Settings::get('admission_items', ''), true);
        if (is_array($raw) && $raw) {
            return $raw;
        }

        return [
            ['icon' => 'fas fa-user-check', 'title' => 'Eligibility', 'active' => true,
             'desc' => 'Children between ages 3–16. Students from any school background are welcome to apply for classes from Preparatory to Class 10.'],
            ['icon' => 'fas fa-file-lines', 'title' => 'Required Documents', 'active' => true,
             'desc' => 'Birth certificate · Previous class marksheet / TC (if applicable) · 2 passport-size photos · Parent / guardian ID proof.'],
            ['icon' => 'fas fa-map-pin', 'title' => 'Visit Us In Person', 'active' => true,
             'desc' => 'Come to the school during school hours to complete admission directly.'],
            ['icon' => 'fas fa-indian-rupee-sign', 'title' => 'Affordable Fee Structure', 'active' => true,
             'desc' => 'We offer quality education at an affordable cost. Contact us on WhatsApp or visit school for detailed fee information.'],
        ];
    }
}

if (! function_exists('admission_contacts')) {
    /**
     * The Admission page WhatsApp contacts (named help desks).
     * Editable in Site Customizer → Admission. Falls back to the
     * site-wide WhatsApp number.
     */
    function admission_contacts(): array
    {
        $raw = json_decode((string) Settings::get('admission_whatsapp', ''), true);
        if (is_array($raw) && $raw) {
            return $raw;
        }

        return [
            ['name' => 'Admission Help Desk', 'active' => true,
             'number' => preg_replace('/\D+/', '', (string) Settings::get('whatsapp', '919862880292'))],
        ];
    }
}

if (! function_exists('people_members')) {
    /**
     * Members of an About-Us people group (Administration, SMC, PTA).
     * Stored as a JSON list under "{$pkey}_members". Each entry has
     * name / role / photo. Returns an empty array when nothing is set.
     */
    function people_members(string $pkey): array
    {
        $raw = json_decode((string) Settings::get($pkey . '_members', ''), true);

        return is_array($raw) ? array_values($raw) : [];
    }
}

if (! function_exists('acad_calendar_images')) {
    /**
     * Academic-calendar images. Stored as a JSON list under
     * "acad_calendar_images". Each entry has file / caption.
     */
    function acad_calendar_images(): array
    {
        $raw = json_decode((string) Settings::get('acad_calendar_images', ''), true);

        return is_array($raw) ? array_values($raw) : [];
    }
}

if (! function_exists('acad_curriculum_items')) {
    /**
     * Curriculum cards. Stored as a JSON list under
     * "acad_curriculum_items". Each entry has icon / title / desc.
     */
    function acad_curriculum_items(): array
    {
        $raw = json_decode((string) Settings::get('acad_curriculum_items', ''), true);

        return is_array($raw) ? array_values($raw) : [];
    }
}

if (! function_exists('cert_items')) {
    /**
     * About-Us certificates / documents. Stored as a JSON list under
     * "about_certs_list". Each entry has title / file / active.
     * Returns an empty array when nothing is set.
     */
    function cert_items(): array
    {
        $raw = json_decode((string) Settings::get('about_certs_list', ''), true);

        return is_array($raw) ? array_values($raw) : [];
    }
}

if (! function_exists('wa_link')) {
    /**
     * Build a wa.me link from a phone number. Adds the 91 country code
     * for bare 10-digit Indian numbers.
     */
    function wa_link(?string $phone, string $message = ''): string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);
        if (strlen($digits) === 10) {
            $digits = '91' . $digits;
        }
        $url = 'https://wa.me/' . $digits;
        if ($message !== '') {
            $url .= '?text=' . rawurlencode($message);
        }
        return $url;
    }
}
