# phossa2/config
[![Build Status](https://travis-ci.org/phossa2/config.svg?branch=master)](https://travis-ci.org/phossa2/config)
[![Code Quality](https://scrutinizer-ci.com/g/phossa2/config/badges/quality-score.png?b=master)](https://travis-ci.org/phossa2/config)
[![HHVM](https://img.shields.io/hhvm/phossa2/config.svg?style=flat)](http://hhvm.h4cc.de/package/phossa2/config)
[![Latest Stable Version](https://img.shields.io/packagist/vpre/phossa2/config.svg?style=flat)](https://packagist.org/packages/phossa2/config)
[![License](https://poser.pugx.org/phossa2/config/license)](http://mit-license.org/)

**phossa2/config** is a configuration management library for PHP. The design
idea is inspired by another github project `mrjgreen/config` but with lots of
cool features.

It requires PHP 5.4, supports PHP 7.0+ and HHVM. It is compliant with
[PSR-1][PSR-1], [PSR-2][PSR-2], [PSR-4][PSR-4].

[PSR-1]: http://www.php-fig.org/psr/psr-1/ "PSR-1: Basic Coding Standard"
[PSR-2]: http://www.php-fig.org/psr/psr-2/ "PSR-2: Coding Style Guide"
[PSR-4]: http://www.php-fig.org/psr/psr-4/ "PSR-4: Autoloader"

Installation
---
Install via the `composer` utility.

```
composer require "phossa2/config=2.*"
```

or add the following lines to your `composer.json`

```json
{
    "require": {
       "phossa2/config": "^2.0.0"
    }
}
```

Features
---

- One central place for all config files

  ```
  config/
   |
   |___ production/
   |       |
   |       |___ host1/
   |       |      |___ db.php
   |       |      |___ redis.php
   |       |
   |       |___ db.php
   |
   |___ dev/
   |     |
   |     |___ redis.php
   |     |___ db.php
   |
   |___ db.php
   |___ redis.php
   |___ system.php
  ```

- Use an [environment](#env) value, e.g. `production` or `production/host1`
  for switching between different configurations.

- Support `.php`, `.json`, `.ini` and `.xml` type of configuration files.

- Use of [reference](#ref) in configuration value is supported, such as
  using `${system.tmpdir}` in configure file.

- On demand configuration loading (lazy loading).

- Able to load all configuration files in one shot with `$config->getAll()`

- Hierachy configuration structure with dot notation like `db.auth.host`.

  ```php
  // returns the db config array
  $db_config = $config->get('db');

  // returns a string
  $db_host = $config->get('db.auth.host');
  ```

- Both flat notation and array notation are supported and can co-exist at the
  same time.

  ```php
  // db config file
  return [
      // array notation
      'auth' => [
          'host' => 'localhost',
          'port' => 3306
      ],

      // flat notation
      'auth.user' => 'dbuser'
  ];
  ```

Usage
---

- <a name="env"></a>Use Environment

  Usually running environment is different for different servers. A good
  practice is setting environment in a `.env` file in the installation root
  and put all configuration files in the `config/` directory.

  In the `bootstrap.php` file,

  ```php
  // load environment
  (new Phossa2\Env\Environment())->load(__DIR__ . '/.env');

  // create config object
  $config = new Phossa2\Config\Config(
      getenv('CONFIG_DIR'), // loaded from .env file
      getenv('APP_ENV')     // loaded from .env file
  );

  // load all configs in one shot
  $conf_data = $config->get(null);
  ```

- <a name="group"></a>Grouping

  Configurations are grouped into groups or files. For example, the `system.php`
  holds all `system.*` configurations

  ```php
  // system.php
  return [
      'tmpdir' => '/usr/local/tmp',
      ...
  ];
  ```

  Later, system related configs can be retrieved as

  ```php
  $dir = $config->get('system.tmpdir');
  ```

  Or being used in other configs as [reference](#ref).

- <a name="ref"></a>Reference

  References make your configuration easy to manage.

  For example, in the `system.php`

  ```php
  // group: system
  return [
      'tmpdir' => '/var/local/tmp',
      ...
  ];
  ```

  In your `cache.php` file,

  ```php
  // group: cache
  return [
      // a local filesystem cache driver
      'local' => [
          'driver' => 'filesystem',
          'params' => [
              'root_dir'   => '${system.tmpdir}/cache',
              'hash_level' => 2
          ]
      ],
      ...
  ];
  ```

  You may reset the reference start and ending chars,

  ```php
  // now reference is something like '%system.tmpdir%'
  $config = (new Config())->setReferencePattern('%', '%');
  ```

  Or even disable the reference feature,

  ```php
  // now reference is not recognised
  $config = (new Config())->setReferencePattern('', '');
  ```

- <a name="overwrite"></a>Overwriting

  If the environment is set to `production/host1`, the precedence order is,

  - `config/production/host1/db.php` over

  - `config/production/db.php` over

  - `config/config/db.php`

- <a name="api"></a>Config API

  - `get($key, $default = null)`

    `$key` is the a flat notation like `db.auth.host`. `$default` is used if
    no configs found.

    Return value might be a `string` or `array` base on the `$key`.

  - `has($key)`

    Test if `$key` exists or not. Returns a `boolean` value.

  - `set($key, $value)`

    Set the configuration manually in this *session*. The value will **NOT**
    be reflected in any config files unless you modify config file manually.

    `$value` can be a `string` or `array`.

- <a name="api"></a>Other public methods

  - `getAll()`

    Get all the configurations (with references resolved) in array

  - `setReferencePattern()`, `hasReference()` and `deReference()`

Dependencies
---

- PHP >= 5.4.0

- phossa2/shared >= 2.0.1

License
---

[MIT License](http://mit-license.org/)
