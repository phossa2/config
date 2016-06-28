# phossa2/config
[![Build Status](https://travis-ci.org/phossa2/config.svg?branch=master)](https://travis-ci.org/phossa2/config)
[![Code Quality](https://scrutinizer-ci.com/g/phossa2/config/badges/quality-score.png?b=master)](https://travis-ci.org/phossa2/config)
[![HHVM](https://img.shields.io/hhvm/phossa2/config.svg?style=flat)](http://hhvm.h4cc.de/package/phossa2/config)
[![Latest Stable Version](https://img.shields.io/packagist/vpre/phossa2/config.svg?style=flat)](https://packagist.org/packages/phossa2/config)
[![License](https://poser.pugx.org/phossa2/config/license)](http://mit-license.org/)

**phossa2/config** is a configuration management library for PHP. The design
idea is inspired by another github project
[mrjgreen/config](https://github.com/ecfectus/config) but with some cool
features.

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

- Support `.php`, `.json`, `.ini`, `.xml` and `.serialized` type of config
  files.

- Use of [reference](#ref) in configuration value is supported, such as
  using `${system.tmpdir}` in configure file.

- On demand configuration loading (lazy loading).

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

- [Array access](#array) to the configuration value

  ```php
  // $config is type of Phossa2\Config\Config
  $config['db.auth.user'] = 'www';
  ```

- [Reference lookup delegation and config chaining](#delegate)

Usage
---

- <a name="env"></a>Use Environment

  Usually running environment is different for different servers. A good
  practice is setting environment in a `.env` file in the installation root
  and put all configuration files in the `config/` directory.

  In the `bootstrap.php` file,

  ```php
  use Phossa2\Config\Config;
  use Phossa2\Env\Environment;
  use Phossa2\Config\Loader\ConfigFileLoader;

  // load environment for .env
  (new Environment())->load(__DIR__ . '/.env');

  // create config object with the config file loader
  $config = new Config(
      new ConfigFileLoader(
          getenv('CONFIG_DIR'), // loaded from .env file
          getenv('APP_ENV')     // loaded from .env file
      )
  );

  // object access
  $db_config = $config->get('db');

  // array access
  var_dump($config['db.user']);
  ```

- <a name="group"></a>Grouping

  Configurations are grouped into files. For example, the `system.php` holds
  all `system.*` configurations

  ```php
  // system.php
  return [
      'tmpdir' => '/usr/local/tmp',
      ...
  ];
  ```

  Later, system related configs can be retrieved as

  ```php
  // object acess
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
  // now reference is something like '%{system.tmpdir}%'
  $config->setReferencePattern('%{', '}%');
  ```

- <a name="overwrite"></a>Overwriting

  If the environment is set to `production/host1`, the config file loading
  order is,

  - `config/config/*.php`

  - `config/production/*.php`

  - `config/production/host1/*.php`

- <a name="array"></a>ArrayAccess

  `Config` class implements `ArrayAccess` interface. So config values can
  be accessed just like an array.

  ```php
  if (!isset($config['db.auth.user'])) {
      $config['db.auth.user'] = 'www';
  }
  ```

- <a name="delegate"></a>Reference lookup delegation and config chaining

  Reference lookup delegation is similar to the delegation idea of
  [Interop Container Delegate Lookup](https://github.com/container-interop/fig-standards/blob/master/proposed/container.md)

  - Calls to the `get()` method should only return an entry if the entry is
    part of the config registry. If the entry is not part of the registry, an
    `NULL` will be returned as described in `ConfigInterface`.

  - Calls to the `has()` method should only return true if the entry is part
    of the config registry. If the entry is not part of the registry, false
    should be returned.

  - If the fetched entry has dependencies (references), **instead** of
    performing the reference lookup in this config registry, the lookup is
    performed on the delegator.

  - **Important** By default, the lookup *SHOULD* be performed on the delegator
    only, not on the config registry itself.

   ```php
   use Phossa2\Config\Config;
   use Phossa2\Config\Delegator;
   use Phossa2\Config\Loader\DummyLoader;

   $config1 = new Config(new DummyLoader());
   $config2 = new Config(new DummyLoader());
   $delegator = new Delegator();

   $config1['db.user'] = '${system.user}';
   $config2['system.user'] = 'root';

   // reference unresolved, return TRUE
   var_dump($config1['db.user'] === '${system.user}');

   $config1->setDelegator($delegator);
   $config2->setDelegator($delegator);

   // reference resolved, return TRUE
   var_dump($config1['db.user'] === 'root');
   ```

  `Delegator` class also implements the `ConfigInterface` and `ArrayAccess`
  interfaces, thus can be used as a front end for a config chain.

  ```php
  $dbUser = $delegator['db.user'];
  ```

  Multiple configs can register with the delegator,

  ```php
  $config3 = (new Config(
      new ConfigFileLoader(
          getenv('CONFIG_DIR'), // loaded from .env file
          getenv('APP_ENV')     // loaded from .env file
      )
  ))->setDelegator($delegator);

  // not delegator contains $config1, $config2 and $config3
  if ($delegator->has('redis.port')) {
    ...
  }
  ```

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

  - Reference related, `setReferencePattern()`, `hasReference()` and
    `deReference()`, `deReferenceArray()`.

Dependencies
---

- PHP >= 5.4.0

- phossa2/shared >= 2.0.1

License
---

[MIT License](http://mit-license.org/)
