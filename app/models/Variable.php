<?php

class Variable extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'variables';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */

	public static function __($key)
	{
		return static::where('k', $key)->get()->first();
	}

}
