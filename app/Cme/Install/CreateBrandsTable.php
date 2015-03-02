<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBrandsTable extends Migration
{
	public $table = 'brands';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->table, function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('brand_name', 225)->default('0');
			$table->string('brand_sender_email', 225)->default('0');
			$table->string('brand_sender_name', 225)->default('0');
			$table->string('brand_website_url', 225)->default('0');
			$table->string('brand_domain_name', 225)->default('0');
			$table->string('brand_unsubscribe_url', 225)->default('0');
			$table->string('brand_logo', 225)->default('0');
			$table->integer('brand_created')->default(0);
			$table->integer('brand_deleted_at')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop($this->table);
	}

	public function setTable($table)
	{
	  $this->table = $table;
	}

}
