<?php

/**
 * Route for links module
 */

Route::group(['namespace' => 'Avl\AdminLinks\Controllers\Admin', 'middleware' => ['web', 'admin'], 'as' => 'adminlinks::'], function () {

		Route::group(['namespace' => 'Ajax', 'prefix' => 'ajax'], function () {
			/* маршруты для работы с медиа */
				Route::post('links-images', 'MediaController@linksImages');
			/* маршруты для работы с медиа */
		});

		Route::resource('sections/{id}/links', 'LinksController', ['as' => 'sections']);
});

Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => [ 'localizationRedirect']], function() {
	Route::group(['namespace' => 'Avl\AdminLinks\Controllers\Site'], function() {
		Route::get('links/{alias}', 'LinksController@index');
	});
});
