<?php
namespace App\Cme\Install;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends InstallTable
{
	public $table = 'notifications';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->table, function(Blueprint $table)
		{
			$table->integer('id')->primary();
			$table->string('subject', 200)->nullable();
			$table->text('message', 65535)->nullable();
			$table->string('recipient', 50)->nullable()->index('recipient');
			$table->enum('status', array('Read','Unread'))->nullable()->index('status');
			$table->integer('time');
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
