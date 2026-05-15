<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        'title',
        'youtube_url',
        'description',
        'category',
        'sort_order',
        'active',
        'created_by',
    ];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    /**
     * Extract the YouTube video ID from any YouTube URL format.
     */
    public function getYoutubeIdAttribute(): ?string
    {
        $url = $this->youtube_url;
        // youtu.be/ID
        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]{11})/', $url, $m)) return $m[1];
        // youtube.com/watch?v=ID  or  /embed/ID  or  /v/ID
        if (preg_match('/(?:v=|\/embed\/|\/v\/)([a-zA-Z0-9_-]{11})/', $url, $m)) return $m[1];
        return null;
    }

    public function getEmbedUrlAttribute(): ?string
    {
        $id = $this->youtube_id;
        return $id ? "https://www.youtube.com/embed/{$id}?rel=0&modestbranding=1" : null;
    }

    public function getThumbnailAttribute(): ?string
    {
        $id = $this->youtube_id;
        return $id ? "https://img.youtube.com/vi/{$id}/hqdefault.jpg" : null;
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
