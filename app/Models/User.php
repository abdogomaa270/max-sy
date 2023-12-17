<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\BlackList;

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
            $usersCount = User::count() + 100001; // Get the current number of users and add 100000 to it
            $model->id = 'WO' . $usersCount;
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
        'bocket_password',
        'parent',
        'left_user_id',
        'right_user_id',
        'total_points',
        'left_children',
        'right_children',
        'calculated_children',
        'total_work',
        'level',
        'product_id',
    ];

    public function leftChild()
    {
        return $this->belongsTo(User::class, 'left_user_id', 'id');
    }
    public function details()
    {
        return $this->hasOne(UserDetail::class, 'user_id','id');
    }
    public function rightChild()
    {
        return $this->belongsTo(User::class, 'right_user_id', 'id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    // ...

    public function blackList()
    {
        return $this->hasOne(BlackList::class);
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
