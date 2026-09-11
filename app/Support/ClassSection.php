<?php

namespace App\Support;

use App\Models\Section;
use Illuminate\Validation\ValidationException;

/** Keeps every class/section write tied to a configured active section. */
class ClassSection
{
    public static function forStudent(?string $class, ?string $section): ?string
    {
        if (! $class) {
            return null;
        }

        $sections = Section::active()->where('class', $class)->orderBy('sort_order')->pluck('name');
        if ($sections->isEmpty()) {
            throw ValidationException::withMessages(['class' => "{$class} has no active sections configured."]);
        }

        $section = trim((string) $section);
        if ($section === '') {
            // Preserve the established default, otherwise only infer when unambiguous.
            $section = $sections->contains('A') ? 'A' : ($sections->count() === 1 ? $sections->first() : '');
        }

        if ($section === '' || ! $sections->contains($section)) {
            throw ValidationException::withMessages(['section' => "Choose an active section configured for {$class}."]);
        }

        return $section;
    }
}
