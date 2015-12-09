<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    public $timestamps = false;

    protected $dates = ['deleted_at'];

    public function product() {
        return $this->belongsTo('App\Product', 'product_id');
    }

    public function client() {
        return $this->belongsTo('App\Client', 'client_id');
    }
}
