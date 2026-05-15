<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class GalleryFolder extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',
        'cover_image',
        'description',
        'sort_order',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $folder) {
            if (empty($folder->slug)) {
                $folder->slug = static::uniqueSlug($folder->name);
            }
        });

        static::updating(function (self $folder) {
            if ($folder->isDirty('name') && empty($folder->slug)) {
                $folder->slug = static::uniqueSlug($folder->name);
            }
        });
    }

    private static function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 2;
        while (static::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    // ── Scopes ──────────────────────────────────────────────

    public function scopeGallery(Builder $q): Builder
    {
        return $q->where('type', 'gallery');
    }

    public function scopePrograms(Builder $q): Builder
    {
        return $q->where('type', 'programs');
    }

    // ── Relations ───────────────────────────────────────────

    public function items(): HasMany
    {
        return $this->hasMany(GalleryItem::class);
    }

    /** First 3 images for the 3D stack preview */
    public function previewImages(): HasMany
    {
        return $this->hasMany(GalleryItem::class)
                    ->orderBy('created_at')
                    ->limit(3);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
