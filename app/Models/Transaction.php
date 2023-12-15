<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;
    // Generate UUID for the ID column when creating a new transaction
    protected static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender', 'id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver', 'id');
    }

    protected $fillable = [
        'sender',
        'receiver',
        'amount',
        'direction'
    ];
}
