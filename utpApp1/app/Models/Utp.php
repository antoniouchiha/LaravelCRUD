<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Utp extends Model
{
    use HasFactory;
    //

    protected $table = "datos";

    protected $fillable = ['img', 'descripcion'];
}
