Routes collector
================

[![Build Status](https://travis-ci.org/cetver/yii2-routes-collector.svg?branch=master)](https://travis-ci.org/cetver/yii2-routes-collector)
[![Coverage Status](https://coveralls.io/repos/github/cetver/yii2-routes-collector/badge.svg?branch=master)](https://coveralls.io/github/cetver/yii2-routes-collector?branch=master)

Provides tools for collecting, saving and manipulating routes of web applications.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require --prefer-dist cetver/yii2-routes-collector
```

or add

```
"cetver/yii2-routes-collector": "^1.0"
```

to the require section of your `composer.json` file.


Configuring
-----------

Run the migration command

```
./yii migrate --migrationPath=@cetver/RoutesCollector/migrations
```

Update the console application configuration file

```php
'controllerMap' => [
    'routes' => 'cetver\RoutesCollector\commands\RoutesController',
],
```

Usage
-----

Create [web-applications configuration file](tests/_data/config/command-routes-collect/config.php) in the
following format:

```php
<?php

return [
    [], // the configuration of the first web-app
    [], // the configuration of the second web-app
];
```

Run the collect command

```
./yii routes/collect <path-to-config-file>
```

If you need to display routes to the user, run the "routes/extract-message" command

``` 
./yii routes/extract-messages '{"messagePath":"@app/messages","languages":["en-US","ru-RU"]}' routes
```

The "routes/extract-message" command arguments:
- The first argument is the "message/extract" command options in JSON format
- The second argument is the first argument of the [translator function](https://github.com/yiisoft/yii2/blob/2.0.12/framework/console/controllers/MessageController.php#L65)

Now you can set aliases/translations by editing the files:
- `@app/messages/en-US/routes.php`
- `@app/messages/ru-RU/routes.php`

Tests
-----

Run the following command

```
composer create-project --prefer-source cetver/yii2-routes-collector
```

Create virtual hosts ([nginx example](travis-ci-nginx.conf)):
- basic.cetver-yii2-routes-collector    points to "tests/_data/apps/basic/web"
- frontend.cetver-yii2-routes-collector points to "tests/_data/apps/advanced/backend/web"
- backend.cetver-yii2-routes-collector  points to "tests/_data/apps/advanced/frontend/web"

Change DB configuration [here](tests/functional/_bootstrap.php) or run the following command

```
export DB=sqlite
```

Run the following commands

```
cd yii2-routes-collector
vendor/bin/codecept run unit,functional
```

Examples
--------

After running the tests, open the links
- [http://basic.cetver-yii2-routes-collector/examples/default/tree](http://basic.cetver-yii2-routes-collector/examples/default/tree)
- [http://backend.cetver-yii2-routes-collector/examples/default/tree](http://backend.cetver-yii2-routes-collector/examples/default/tree)

If you follow the instructions correctly, you should see the pages as in the screenshots below
- Basic app
    - [Tree](tests/_data/screenshots/basic/tree.png) 
    - [Ordered tree](tests/_data/screenshots/basic/ordered-tree.png)
    - [Aliases(i18n)](tests/_data/screenshots/basic/aliases-i18n.png)
    - [Real life example](tests/_data/screenshots/basic/real-life.png)
- Advanced apps
    - [Tree](tests/_data/screenshots/advanced/tree.png) 
    - [Ordered tree](tests/_data/screenshots/advanced/ordered-tree.png)
    - [Aliases(i18n)](tests/_data/screenshots/advanced/aliases-i18n.png)
    - [Real life example](tests/_data/screenshots/advanced/real-life.png)