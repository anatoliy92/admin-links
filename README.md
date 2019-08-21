# admin-links

### Информация

Модуль каталога ссылок заточенный для CMS IRsite.

### Установка

```
$ composer require avl/admin-links
```
Или в секцию **require** добавить строчку **"avl/admin-links": "^1.0"**

```json
{
    "require": {
        "avl/admin-links": "^1.0"
    }
}
```
### Настройка

Для публикации файла настроек необходимо выполнить команду:

```
$ php artisan vendor:publish --provider="Avl\AdminLinks\AdminLinksServiceProvider" --force
```
