<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Langs;

class CreateLinksTable extends Migration
{
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up()
		{
			Schema::create('links', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('section_id')->nullable();

				$langs = Langs::all();
				foreach ($langs as $lang) { $table->integer('good_' . $lang->key)->default(0); }
				foreach ($langs as $lang) { $table->string('link_' . $lang->key)->nullable(); }
				foreach ($langs as $lang) { $table->string('title_' . $lang->key)->nullable(); }
				foreach ($langs as $lang) { $table->text('description_' . $lang->key)->nullable(); }
				foreach ($langs as $lang) { $table->string('photo_' . $lang->key)->nullable(); }

				$table->string('class')->nullable();
				$table->dateTime('published_at');
				$table->timestamps();
			});
		}

		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down()
		{
			Schema::dropIfExists('links');
		}
}
