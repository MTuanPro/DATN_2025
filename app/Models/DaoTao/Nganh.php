<?php

namespace App\Models\Daotao;
use App\Models\DaoTao\Khoa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nganh extends Model
{
    use HasFactory;

    protected $table = 'nganh';

    protected $fillable = [
        'ma_nganh',
        'ten_nganh',
        'khoa_id',
        'mo_ta',
    ];

    // Ngành thuộc về 1 khoa
    public function khoa()
    {
        return $this->belongsTo(Khoa::class, 'khoa_id');
    }
}
