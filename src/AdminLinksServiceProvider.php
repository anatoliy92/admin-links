<?php namespace Avl\AdminLinks;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Config;

class AdminLinksServiceProvider extends ServiceProvider
{

		/**
		 * Bootstrap the application services.
		 *
		 * @return void
		 */
		public function boot()
		{
				// Публикуем файл конфигурации
				$this->publishes([
						__DIR__ . '/../config/adminlinks.php' => config_path('adminlinks.php'),
				]);

				$this->publishes([
						__DIR__ . '/../public' => public_path('vendor/adminlinks'),
				], 'public');

				$this->loadRoutesFrom(__DIR__ . '/routes.php');

				$this->loadViewsFrom(__DIR__ . '/../resources/views', 'adminlinks');
		}

		/**
		 * Register the application services.
		 *
		 * @return void
		 */
		public function register()
		{
				// Добавляем в глобальные настройки системы новый тип раздела
				Config::set('avl.sections.links', 'Каталог ссылок');

				// объединение настроек с опубликованной версией
				$this->mergeConfigFrom(__DIR__ . '/../config/adminlinks.php', 'adminlinks');

				// migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

		}

}
