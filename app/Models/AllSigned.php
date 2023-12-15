<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllSigned extends Model
{
    use HasFactory;
    protected $table='all_signed';
    protected $fillable=[
        'parent_id',
        'child_id',
        'direction',
        'level'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'child_id', 'id');
    }
}
//
//
