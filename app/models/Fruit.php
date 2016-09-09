<?php

class Fruit extends Eloquent {
    use SoftDeletingTrait;
    public $timestamps = true;
    protected $table='fruits';

    function purchase() {
        return $this->belongsTo('Purchase');
    }

    function purchased_seed() {
        return $this->belongsTo('Purchase','purchase_id');
    }

    function purchased_land() {
    	return $this->belongsTo('Purchase','land_id','id');
    }

    function purchased_fertilizer() {
    	return $this->belongsTo('Purchase','fertilizer_id','id');
    }

} 