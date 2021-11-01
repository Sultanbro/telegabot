<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramUser extends Model
{
    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'username',
        'language_code',
    ];
    use HasFactory;
}
