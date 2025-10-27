<?php

namespace App\Models\Daotao;
use App\Models\DaoTao\Nganh;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Khoa extends Model
{
    use HasFactory;

    protected $table = 'khoa';

    protected $fillable = [
        'ma_khoa',
        'ten_khoa',
        'truong_khoa_id',
        'mo_ta',
    ];

    // Một khoa có nhiều ngành
    public function nganhs()
    {
        return $this->hasMany(Nganh::class, 'khoa_id');
    }
}
