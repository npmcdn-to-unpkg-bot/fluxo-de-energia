<?php
/**
 * Created by PhpStorm.
 * User: aneeshdash
 * Date: 27/11/14
 * Time: 10:43 AM
 */
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Investor extends Eloquent implements UserInterface {

    use UserTrait;

    protected $table='investor';

    public $timestamps = true;

    function user() {
        return $this->belongsTo('User');
    }

    function investment() {
        return $this->hasMany('Investment');
    }

} 