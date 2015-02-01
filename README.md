jaqb
===========

[![Build Status](https://travis-ci.org/jaredtking/jaqb.png?branch=master)](https://travis-ci.org/jaredtking/jaqb)
[![Coverage Status](https://coveralls.io/repos/jaredtking/jaqb/badge.svg?branch=master)](https://coveralls.io/r/jaredtking/jaqb?branch=master)
[![Latest Stable Version](https://poser.pugx.org/jaredtking/jaqb/v/stable.png)](https://packagist.org/packages/jaredtking/jaqb)
[![Total Downloads](https://poser.pugx.org/jaredtking/jaqb/downloads.png)](https://packagist.org/packages/jaredtking/jaqb)
[![HHVM Status](http://hhvm.h4cc.de/badge/jaredtking/jaqb.svg)](http://hhvm.h4cc.de/package/jaredtking/jaqb)

JAQB: Just Another Query Builder (pronounced "jacob")

## Requirements

- PHP 5.4+ or HHVM 3.3+
- [PDO](http://php.net/pdo)

## Tests

Use phpunit to run the included tests:

```
phpunit
```

## Usage

```php
$qb = new JAQB\QueryBuilder($pdo);
```

### SELECT Query

```php
$qb->select('*')
   ->from('Movies')
   ->where('year', 2015, '<')
   ->where('year', 1990, '>=')
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
$qb->table('Users')
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
$qb->raw('SHOW COLUMNS FROM test')
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

## Contributing

Please feel free to contribute by participating in the issues or by submitting a pull request. :-)

## License

The MIT License (MIT)

Copyright © 2015 Jared King

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the “Software”), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.