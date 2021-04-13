<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model;

class Peripheral extends Model
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'peripherals';
    public $timestamps = false;
    protected $fillable = [
        'status', 'created', 'vendor', 'uid', 'id_gateway'
    ];

    public function gateway()
    {
        return $this->belongsTo('App\Models\Gateway','id_gateway');
    }
}
