# Kohana PHP Framework

Для проверки окружения надо раскоментить строчки в index.php:
/*if (file_exists('install'.EXT))
{
	// Load the installation check
	return include 'install'.EXT;
}*/

При разработке на локальной машине надо установить Kohana::$environment = Kohana::DEVELOPMENT либо в httpd.conf сервера добавить:
SetEnv KOHANA_ENV development

В корне лежит файлик Migrate_script.sh это скрипт запущенный из определенной папки переименовывает все файлы и папки с заглавной буквы,
полезно когда надо подключить модуль который был написан для Kohana < 3.3

[Соглашение по стандартам оформления кода](http://kohanaframework.org/3.3/guide/kohana/conventions)
