Сервис курса валют
===

Установка
===
Для работы с проектом в системе должены быть установлены:
- PHP 7.4
- MySQL 5.7
- [Composer](https://getcomposer.org/download/)
- [npm](https://www.npmjs.com/get-npm)
- [Symfony CLI](https://symfony.com/download)

Установите зависимости:
```shell script
composer install
npm install
```

Инициализируйте базу данных.
```shell script
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

Запустите symfony-сервер и сервер ассетов в параллельных окнах терминала:
```shell script
npm start
php -S localhost:8000 -t public
```

Для обновления данных запустите консольную команду:
```shell script
php bin/console app:update-currencies
```