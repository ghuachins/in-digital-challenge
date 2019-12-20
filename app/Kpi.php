<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class Kpi extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'average', 'variance', 'total_clients'
    ];
}
