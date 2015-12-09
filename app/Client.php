<?php

namespace app;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{

    use SoftDeletes;

    public $timestamps = false;

    protected $dates = ['deleted_at'];

    public function comments() {
        return $this->morphMany('App\Comment', 'object');
    }

    public function children() {
        return $this->hasMany('App\Client', 'parent_id');
    }

    public function parent() {
        return $this->belongsTo('App\Client', 'parent_id');
    }

    public function root() {
        $root = $this;

        while(true) {
            if (! $root->parent) break;
            $root = $root->parent;
        }

        return $root;
    }
}
