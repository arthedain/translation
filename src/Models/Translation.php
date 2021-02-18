<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Arthedain\Translation\HasTranslation;

class Translation extends Model
{
    use HasTranslation;

    protected $fillable = ['key', 'value', 'custom_properties'];

    protected $table = 'translations';

    protected $casts = [
        'custom_properties' => 'array',
    ];
}
