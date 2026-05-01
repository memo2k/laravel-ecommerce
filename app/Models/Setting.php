<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['label', 'key', 'value', 'type', 'group', 'is_public', 'description'];
}
