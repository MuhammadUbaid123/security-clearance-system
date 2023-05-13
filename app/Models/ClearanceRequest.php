<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class ClearanceRequest extends Model
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $fillable = [
        'requester_id',
        'session',
        'req_to_members',
        'approvedd_by',
    ];
}
