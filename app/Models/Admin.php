<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Model
{
    use SoftDeletes;

    protected $table = 'admin';

    protected $fillable = [
        'user_id',
        'ho_ten',
        'email',
        'so_dien_thoai',
        'anh_dai_dien',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relationship: Admin belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
