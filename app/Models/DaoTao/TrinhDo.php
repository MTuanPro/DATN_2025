<?php

namespace App\Models\Daotao;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrinhDo extends Model
{
    use SoftDeletes;

    protected $table = 'dm_trinh_do';

    protected $fillable = [
        'ten_trinh_do',
    ];
}
