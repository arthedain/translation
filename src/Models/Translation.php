<?php

namespace App\Models;

use Arthedain\Translation\HasTranslation;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use HasTranslation;

    protected $fillable = ['key', 'value'];

    protected $table = 'translations';
}
