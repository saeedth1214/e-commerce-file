<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Notifications\SendVerificationCodeNotification;
use Illuminate\Database\Eloquent\SoftDeletes;
use Qirolab\Laravel\Reactions\Contracts\ReactsInterface;
use Qirolab\Laravel\Reactions\Traits\Reacts;


class User extends Authenticatable implements HasMedia, ReactsInterface
{
    use HasApiTokens, HasFactory, Notifiable, InteractsWithMedia, Reacts, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'email',
        'mobile',
        'password',
        'email_verified_at',
        'mobile_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'mobile_verified_at' => 'datetime'
    ];
    public function files()
    {
        return $this->belongsToMany(File::class, 'user_has_files')->withPivot(['amount', 'bought_at']);
    }
    public function vouchers()
    {
        return $this->belongsToMany(Voucher::class, 'user_has_vouchers', 'user_id', 'voucher_id')->withPivot([
            'number_authorize_use',
            'number_times_use',
            'last_date_of_use'
        ]);
    }
    public function plans()
    {
        return $this->belongsToMany(Plan::class, 'user_has_plans')->withPivot(['amount', 'activation_at', 'expired_at', 'bought_at']);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function transactions()
    {
        return $this->hasManyThrough(Transaction::class, Order::class);
    }

    public static function findByEmail($email)
    {
        return static::query()->where('email', $email)->first();
    }
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar-image')->singleFile();
    }

    public function sendVerificationCode($code)
    {
        $this->notify(new SendVerificationCodeNotification($code));
    }
    public static function findByUserNameType($type, $value)
    {
        return static::query()->where($type, $value)->first();
    }

    public function orders()
    {

        return $this->hasMany(Order::class);
    }

    public function activePlan()
    {
        return $this->plans()->wherePivot('expired_at', '>=', now())->first();
    }
}
