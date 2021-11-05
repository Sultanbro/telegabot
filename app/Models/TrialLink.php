<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrialLink extends Model
{
    protected $fillable = [
        'trial_link',
        'telegram_user_id'
    ];
    use HasFactory;
}
