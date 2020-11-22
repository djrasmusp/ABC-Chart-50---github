<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class ChartTrack extends Model {

    protected $fillable = ['chart_id', 'position', 'track_id'];

    protected $hidden = ['created_by','deleted_at','created_at', 'updated_at'];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    // Relationships

}
