<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'whatsapp',
        'email',
        'class_interested',
        'message',
        'status',
    ];
}
