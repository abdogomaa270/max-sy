<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;
    protected $primaryKey = 'user_id';
    protected $keyType = 'string';
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    protected $fillable=[
        'user_id',
        'gender',
        'phone',
        'inheritor',
        'national_number',
        'qid',
        'amana',
        'birth_country',
        'birth_city',
        'birth_street',
        'birthday',
        'man7_history',
        'address_country',
        'address_city',
        'address_street',
        'shipping_country',
        'shipping_city',
        'shipping_street',
        'identity_front',
        'identity_back',
        'healthDoc',
    ];
    protected $casts = [
        'userId' => 'string',
    ];






}
