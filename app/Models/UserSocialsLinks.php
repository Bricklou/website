<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class UserSocialsLinks extends Model
{
    use HasFactory, AsSource;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'social_network',
        'social_link',
    ];

    protected $casts = [
        'social_link' => 'string',
    ];

    public $timestamps = false;

    public static $SOCIAL_LINKS = [
        'youtube' => 'YouTube',
        'discord' => 'Discord',
        'github' => 'GitHub',
        'twitter' => 'Twitter',
        'facebook' => 'Facebook',
        'instagram' => 'Instagram',
        'twitch' => 'Twitch',
        'steam' => 'Steam',
        'reddit' => 'Reddit',
    ];
}
