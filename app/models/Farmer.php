<?php

class Farmer extends Eloquent {
    
    protected $table='farmers';

    public $timestamps = true;

    function user() {
        return $this->belongsTo('User');
    }

    function purchase() {
        return $this->hasMany('Purchase');
    }


} 