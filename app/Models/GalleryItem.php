<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryItem extends Model
{
    protected $table = 'gallery';

    protected $fillable = [
        'gallery_folder_id',
        'title',
        'caption',
        'path',
        'uploaded_by',
    ];

    public function folder(): BelongsTo
    {
        return $this->belongsTo(GalleryFolder::class, 'gallery_folder_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
