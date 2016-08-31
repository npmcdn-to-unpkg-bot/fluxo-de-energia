<?php

class Land extends Eloquent {
    
    protected $table='lands';

    public $timestamps = true;

    function product() {
        return $this->belongsTo('Product');
    }

} 