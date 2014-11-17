<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/index', function()
{
	return View::make('hello');
});

Route::get('/tsqli', function()
{
	return View::make('tsqli');
});

Route::get('/', function()
{
	return View::make('index')->with('objects', Object::children(0));
});

Route::get('/path/from/{ID}', function($ID)
{
	return View::make('index')->with('objects', Object::children($ID));
});

Route::get('/path/to/{ID}', function($ID)
{
	return View::make('index')->with('objects', Object::children(0))->with('path', Object::path($ID));
});

Route::post('/populate', function()
{
	$ID = Input::get('ID');
	echo Object::children($ID);
});

Route::post('/register', function()
{
	extract(Input::all());
	$next = Object::next($parent);
	if( ! $next ) $next = 0;
	$type = Object::type($parent);
	$object = Object::create(array(
		'name' => $name,
		'position' => $next,
		'parent' => $parent,
		'type' => $type
	));
	echo json_encode($object->ID);
});

Route::post('/delete', function()
{
	Object::remove(Input::get('ID'));
});

Route::post('/mark/as/class', function()
{
	$object = Object::find(Input::get('ID'));
});

Route::post('/process', function()
{
	extract(Input::all());
	$tsqli = new tsqli("localhost", "root", "root", "laravel_organize");
	switch($action) {

		case 'populate':
			break;
		case 'register':
			break;
		case 'delete':
			break;
		case 'multidelete':
			$tsqli->deleteIndices("objects", $IDs);
			break;
		case 'rename':
			echo $tsqli->renameIndex("objects", $ID, $name);
			//$tsqli->updateType($parent, $type);
			break;
		case 'order':
			$tsqli->setPosition_("objects", $ID, $position, $parent);
			break;
		case 'transfer':
			$tsqli->transfer("objects", $ID, $position, $parent);
			break;
		case 'multitransfer':
			$tsqli->multitransfer("objects", $IDs, $position, $parent);
			break;
		case 'complete':
			$tsqli->obComplete( $ID );
			break;
		case 'style':
			$tsqli->setStyleAttribute( $ID, $attribute, $value );
			break;
		case 'type':
			$tsqli->setType( $ID, $type );
			break;
		case 'class':
			print_r( $_POST );
			$tsqli->classify( $ID, $classification );
			break;
		case 'addfield':
			$tsqli->addField( $ID, $field );
			break;
		case 'setfield':
			print_r( $_POST );
			$tsqli->setField( $ID, $key, $value );
			break;
		case 'time':
			$tsqli->incrementTime( $ID, $time );
			break;
		case 'sumtime':
			echo $tsqli->renderTime( $tsqli->sumTimeTree( $ID ) );
			break;
		case 'scalartime':
			echo $tsqli->get_cell("SELECT time FROM objects WHERE ID=$ID");
			break;

	}
});


Route::get('/object/next/{ID}', function($ID)
{
	print_r( Object::next($ID) );
});
Route::get('/object/type/{ID}', function($ID)
{
	print_r( Object::type($ID) );
});
Route::get('/object/create', function()
{
	Object::create(array(
		'name' => 'Object',
		'position' => 1,
		'parent' => 1,
		'type' => 'object'
	));
});
Route::get('/object/delete/{ID}', function($ID)
{
	Object::remove($ID);
});	
