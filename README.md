JAQB
===========

[![Latest Stable Version](https://poser.pugx.org/jaqb/jaqb/v/stable.svg?style=flat)](https://packagist.org/packages/jaqb/jaqb)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](LICENSE)
[![Build Status](https://travis-ci.org/jaredtking/jaqb.svg?branch=master&style=flat)](https://travis-ci.org/jaredtking/jaqb)
[![Coverage Status](https://coveralls.io/repos/jaredtking/jaqb/badge.svg?style=flat)](https://coveralls.io/r/jaredtking/jaqb)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jaredtking/jaqb/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jaredtking/jaqb/?branch=master)
[![Total Downloads](https://poser.pugx.org/jaqb/jaqb/downloads.svg?style=flat)](https://packagist.org/packages/jaqb/jaqb)
[![HHVM Status](http://hhvm.h4cc.de/badge/jaqb/jaqb.svg?style=flat)](http://hhvm.h4cc.de/package/jaqb/jaqb)


JAQB: Just Another Query Builder for PHP (pronounced "jacob")

## Requirements

- PHP 5.4+, PHP 7, or HHVM 3.3+
- [PDO](http://php.net/pdo)

## Installation

The easiest way to install JAQB is with [composer](http://getcomposer.org):

```
composer require jaqb/jaqb
```

## Usage

```php
$qb = new JAQB\QueryBuilder($pdo);
```

### SELECT Query

```php
$qb->select('*')
   ->from('Movies')
   ->join('Directors', 'Directors.id = Movies.director_id')
   ->where('Directors.name', 'Quentin Tarantino')
   ->between('year', 1990, 2015)
   ->groupBy('category')
   ->having('rating', 4.5, '>')
   ->orderBy('rating', 'DESC')
   ->limit(100, 10)
   ->all();
```

### INSERT Query

```php
$qb->insert(['name' => 'Catcher in the Rye', 'author' => 'JD Salinger'])
   ->into('Books')
   ->execute();
```

### UPDATE Query

```php
$qb->update('Users')
   ->where('uid', 10)
   ->values(['first_name' => 'JAQB', 'website' => 'example.com'])
   ->orderBy('uid', 'ASC')
   ->limit(100)
   ->execute();
```

### DELETE Query

```php
$qb->delete('Users')
   ->where('last_login', strtotime('-1 year'), '<')
   ->limit(100)
   ->orderBy('last_login', 'ASC')
   ->execute();
```

### SQL Query

```php
$qb->raw('SHOW COLUMNS FROM `Events`')
   ->execute();
```

### Executing a Query
The following methods can be used to execute a query and retrieve results:
- `execute()` - returns a `PDOStatement`
- `all()` - returns all of the rows
- `one()` - returns the first row
- `column($index = 0)` - returns a specific column from each row
- `scalar($index = 0)` - returns a specific column from the first row

Also:
- `rowCount()` - returns the number of rows affected by the last executed statement

### Building a Query

If you want to build a query without executing it just use `build()` instead. `getValues()` will retrieve any [ordered question mark parameters](http://php.net/manual/en/pdo.prepare.php).

## Tests

Use phpunit to run the included tests:

```
phpunit
```

## Contributing

Please feel free to contribute by participating in the issues or by submitting a pull request. :-)

## License

The MIT License (MIT)

Copyright © 2015 Jared King

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the “Software”), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.