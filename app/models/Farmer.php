<?php

class Farmer extends Eloquent {
    
    protected $table='farmers';

    public $timestamps = true;

    function user() {
        return $this->belongsTo('User');
    }

    function purchases() {
        return $this->hasMany('Purchase');
    }


} 