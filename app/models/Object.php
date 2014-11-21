<?php

class Object extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'objects';

	public $fillable = array('name', 'position', 'parent', 'type');

	public $timestamps = false;

	public $primaryKey = 'ID';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */

	public static function queryChildren($ID, $orderBy = 'position')
	{
		return static::where('parent', $ID)->orderBy($orderBy);
	}
	
	public static function children($ID, $orderBy = 'position')
	{
		$ID = static::ID($ID);
		return static::queryChildren($ID, $orderBy)->get();
	}

	// Converts an allowed string to its object ID
	public static function ID($ID)
	{
		if( is_numeric($ID) ) 
			return $ID;
		else
			return static::where('name', $ID)->pluck('ID');
	}

	public static function next($parent)
	{
		return static::queryChildren($parent)->max('position');
	}

	public static function type($ID)
	{
		if( $type = static::where('ID', $ID)->pluck('type') )
			return $type;
		else 
			return 'object';
	}

	public static function remove($ID)
	{
		$children = static::children($ID);
		foreach( $children as $child )
		{
			static::remove($child->ID);
		}
		Object::destroy($ID);
	}

	public static function path($ID, $json = true)
	{
		$path = array_reverse(static::htap($ID));
		if( $json ) 
			return json_encode($path);
		else
			return $path;
	}	

	public static function htap($ID, $path = array())
	{
		$ID = static::ID($ID);	
		$path[] = $ID;
		$parentID = static::parentID($ID);
		if( $parentID == 0 ) return $path;
		return static::htap($parentID, $path);
	}

	public static function parentID($ID)
	{
		return static::where('ID', $ID)->pluck('parent');
	}

	public static function sample()
	{
		$object = Object::create(array(
			'name' => 'Object',
			'position' => 1,
			'parent' => 1,
			'type' => 'object'
		));
		return $object;
	}

	public static function sampleAndCleanup()
	{
		$object = static::sample();
		$object->delete();
	}

	public static function sanitize()
	{
		static::where('name', 'Object')->where('parent', 1)->delete();
	}

	public static function root()
	{
		$root = new Object;
		$root->ID = 0;
		return $root;
	}

}
