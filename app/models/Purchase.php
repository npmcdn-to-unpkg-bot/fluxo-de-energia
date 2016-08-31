<?php
/**
 * Created by PhpStorm.
 * User: aneeshdash
 * Date: 27/11/14
 * Time: 10:34 AM
 */

class Purchase extends Eloquent {
    
    protected $table='purchases';

    public $timestamps = true;

    function product() {
        return $this->belongsTo('Product');
    }
    
    function farmer() {
        return $this->belongsTo('Farmer');
    }
} 