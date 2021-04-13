<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model;

class Gateway extends Model
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'gateways';
    public $timestamps = false;
    protected $fillable = [
        'serial', 'name', 'ipv4',
    ];

    public function peripherals()
    {
        return $this->hasMany('App\Models\Peripheral', 'id_gateway', '_id');
    }
}
