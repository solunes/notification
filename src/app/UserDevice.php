<?php

namespace Solunes\Notification\App;

use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model {
	
	protected $table = 'user_devices';
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array(
		'user_id'=>'required',
		'token'=>'required',
		'active'=>'required',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'user_id'=>'required',
		'token'=>'required',
		'active'=>'required',
	);
    
    public function user() {
        return $this->belongsTo('App\User');
    }

}