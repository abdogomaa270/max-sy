<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $primaryKey = 'id';
    public $incrementing = false;

    // Generate UUID for the ID column when creating a new user
    protected static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = 'Am' . date('Ymdeis');
        });
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    protected $fillable = [
        'name',
        'nickname',
        'father_name',
        'mother_name',
        'email',
        'password',
        'parent',
        'left_user_id',
        'right_user_id',
        'left_points',
        'right_points',
        'total_points',
    ];

    public function leftChild()
    {
        return $this->belongsTo(User::class, 'left_user_id', 'id');
    }
    public function details()
    {
        return $this->hasOne(UserDetail::class, 'user_id');
    }
    public function rightChild()
    {
        return $this->belongsTo(User::class, 'right_user_id', 'id');
    }

    protected $hidden = [
        'password',
        'bocket_password',
        'created_at',
        'updated_at',
        'remember_token',
    ];












    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
