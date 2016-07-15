# phossa2/config
[![Build Status](https://travis-ci.org/phossa2/config.svg?branch=master)](https://travis-ci.org/phossa2/config)
[![Code Quality](https://scrutinizer-ci.com/g/phossa2/config/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/phossa2/config/)
[![PHP 7 ready](http://php7ready.timesplinter.ch/phossa2/config/master/badge.svg)](https://travis-ci.org/phossa2/config)
[![HHVM](https://img.shields.io/hhvm/phossa2/config.svg?style=flat)](http://hhvm.h4cc.de/package/phossa2/config)
[![Latest Stable Version](https://img.shields.io/packagist/vpre/phossa2/config.svg?style=flat)](https://packagist.org/packages/phossa2/config)
[![License](https://poser.pugx.org/phossa2/config/license)](http://mit-license.org/)

**phossa2/config** is a simple, easy yet powerful configuration management library
for PHP. The design idea is inspired by another github project
[mrjgreen/config](https://github.com/ecfectus/config) but with some cool features.

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
       "phossa2/config": "^2.0.7"
    }
}
```

Features
---

- Simple interface with `get($id, $default = null)` and `has($id)`.

- One central place for all config files for ease of management.

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

- Use an [environment](#env) value, such as `production` or `production/host1` for
  switching between different configurations.

- Use of [references](#ref) in configuration value is fully supported, such as
  `${system.tmpdir}`.

- On demand configuration loading (lazy loading).

- Hierachy configuration structure with dot notation like `db.auth.host`.

- [Array access](#array) for ease of use. e.g. `$config['db.user'] = 'www';`.

- Reference lookup [delegation](#delegate) and config chaining.

- Support `.php`, `.json`, `.ini`, `.xml` and `.serialized` type of config files.

Usage
---

- <a name="env"></a>Use environment value

  Usually application running environment is different on different servers. A
  good practice is setting environment in a `.env` file somewhere on the host, and
  put all configuration files in one central `config/` dir.

  A sample `.env` file,

  ```shell
  # installation base
  BASE_DIR=/www

  # app directory
  APP_DIR=${BASE_DIR}/app

  # config directory
  CONFIG_DIR=${APP_DIR}/config

  # app env for current host
  APP_ENV=production/host1
  ```

  In a sample `bootstrap.php` file,

  ```php
  use Phossa2\Config\Config;
  use Phossa2\Env\Environment;
  use Phossa2\Config\Loader\ConfigFileLoader;

  // load environment from '.env' file
  (new Environment())->load(__DIR__ . '/.env');

  // create config instance with the config file loader
  $config = new Config(
      new ConfigFileLoader(
          getenv('CONFIG_DIR'),
          getenv('APP_ENV')
      )
  );

  // object access of $config
  $db_config = $config->get('db');

  // array access of $config
  $config['db.user'] = 'www';
  ```

- <a name="group"></a>Central config directory and configuration grouping

  - Configuration grouping

    Configurations are gathered into one directory and are grouped into files and
    subdirectories for ease of management.

    For example, the `config/system.php` holds `system.*` configurations

    ```php
    // system.php
    return [
        'tmpdir' => '/usr/local/tmp',
        // ...
    ];
    ```

    Later, `system` related configs can be retrieved as

    ```php
    // object acess of config
    $dir = $config->get('system.tmpdir');

    // array access of $config
    $dir = $config['system.tmpdir'];
    ```

    Or being used in other configs as [references](#ref).

  - Configuration file loading order

    If the environment is set to `production/host1`, the config file loading
    order is,

    - `config/config/*.php`

    - `config/production/*.php`

    - `config/production/host1/*.php`

    Configuration values are overwritten and replaced by later loaded files.

- <a name="ref"></a>Use of references

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

  You may reset the reference start and ending matching pattern,

  ```php
  // now reference is something like '%{system.tmpdir}%'
  $config->setReferencePattern('%{', '}%');
  ```

- <a name="array"></a>ArrayAccess and dot notation

  `Config` class implements `ArrayAccess` interface. So config values can be
  accessed just like an array.

  ```php
  // test
  if (!isset($config['db.auth.user'])) {
      // set
      $config['db.auth.user'] = 'www';
  }
  ```

  Hierachy configuration structure with dot notation like `db.auth.host`.

  ```php
  // returns the db config array
  $db_config = $config->get('db');

  // returns a string
  $db_host = $config->get('db.auth.host');
  ```

  Both flat notation and array notation are supported and can co-exist at the same
  time.

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

- <a name="delegate"></a>Reference lookup delegation and config chaining

  - Config delegation

    Reference lookup delegation is similar to the delegation idea of
    [Interop Container Delegate Lookup](https://github.com/container-interop/fig-standards/blob/master/proposed/container.md)

    - Calls to the `get()` method should only return an entry if the entry is part of
      the config registry. If the entry is not part of the registry, a `NULL` will be
      returned as described in `ConfigInterface`.

    - Calls to the `has()` method should only return true if the entry is part of the
      config registry. If the entry is not part of the registry, false should be
      returned.

    - If the fetched entry has dependencies (references), **instead** of performing
      the reference lookup in this config registry, the lookup is performed on the
      delegator.

    - **Important** By default, the lookup *SHOULD* be performed on the delegator
      only, not on the config registry itself.

      ```php
      $config1 = new Config();
      $config2 = new Config();
      $delegator = new Delegator();

      // add some values
      $config1['db.user'] = '${system.user}';
      $config2['system.user'] = 'root';

      // reference unresolved in $config1
      var_dump($config1['db.user'] === '${system.user}'); // true

      // add both configs to the delegator
      $delegator->addConfig($config1);
      $delegator->addConfig($config2);

      // reference resolved thru the delegator
      var_dump($config1['db.user'] === 'root'); // true
      ```

    `Delegator` class implements the `ConfigInterface` and `ArrayAccess` interfaces,
    thus can be used just like a normal config.

    ```php
    $dbUser = $delegator['db.user'];
    ```
  - Config chaining

    Config chaining can be achieved via delegation feature. For example,

    ```php
    // configs
    $config1 = new Config();
    $config2 = new Config();

    // delegators
    $delegator1 = new Delegator();
    $delegator2 = new Delegator();

    // register $config1 with $delegator1
    $delegator1->addConfig($config1);

    // chaining
    $delegator2->addConfig($delegator1);
    $delegator2->addConfig($config2);

    // get from the chain
    $db = $delegator2->get('db');
  ```

APIs
---

- <a name="api_1"></a>`ConfigInterface` API

  - `get(string $id, $default = null): mixed`

    `$id` is the a flat notation like `db.auth.host`. `$default` is used if
    no config value found.

    Return value might be a `string`, `array` or even `object`.

  - `has(string $id): bool`

    Test if `$id` exists or not. Returns a `boolean` value.

- <a name="api_2"></a>`WritableInterface` API

  - `set(string $id, mixed $value): $this`

    Set the configuration manually in this *session*. The value will **NOT**
    be reflected in any config files unless you modify config file manually.

    `$value` can be a `string`, `array` or `object`.

    This feature can be disabled by

    ```php
    // disable writing to the $config
    $config->setWritable(false);
    ```

  - `setWritable(bool $writable): $this`

    Enable or disable the `set()` functionality.

  - `isWritable(): bool`

    Test to see if current config writable or not.

- <a name="api_3"></a>`ReferenceInterface` API

  - `setReferencePattern(string $start, string $end): $this`

    Reset the reference start chars and ending chars. The default are `'${'` and
    `'}'`

  - `hasReference(string $string): bool`

    Test to see if there are references in the `$string`

  - `deReference(string $string): mixed`

    Dereference all the references in the `$string`. The result might be `string`,
    `array` or even `object`.

  - `deReferenceArray(mixed &$data): $this`

    Recursively dereference everything in the `$data`. `$data` might be `string`
    or `array`. Other data type will be ignored and untouched.

- <a name="api_4"></a>`DelegatorInterface` API

  - `addConfig(ConfigInterface $config): $this`

    Added one `Phossa2\Config\Interfaces\ConfigInterface` instance to the delegator.

Dependencies
---

- PHP >= 5.4.0

- phossa2/shared >= 2.0.15

License
---

[MIT License](http://mit-license.org/)
