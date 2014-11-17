<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateObjectsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('objects', function($table)
		{
			$table->increments('ID');
			$table->string('name');
			$table->integer('parent');
			$table->integer('position');
			$table->string('type');
			$table->string('classes');
			$table->integer('time');
			$table->string('fields');
			$table->decimal('value', 5, 2);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('objects');
	}

}
