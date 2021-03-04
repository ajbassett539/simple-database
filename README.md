# simple-database
Simple mysqli Database Wrapper to Retrieve stdClass Objects.

## Installation
`composer require ajbassett539/simple-database`

## Instantiation
`include 'vendor/autoload.php';`

`$db = new \Database\DB();`

## Credentials
```
## .htaccess
SetEnv DATABASE_HOST localhost
SetEnv DATABASE_USER usernaem
SetEnv DATABASE_PASS passwerd
SetEnv DATABASE_NAME fancy_db
SetEnv LOG_SQL_QUERIES 1
````

## Methods
### query
`public function query($sql = "", $multirow = false, $raw = false)`

Takes an SQL string and attempts to execute. You are responsible for sanitization and syntax.

If you anticipate multiple rows, `$multirow = true`.

If you'd rather have the [mysqli result object](https://www.php.net/manual/en/class.mysqli-result.php), `$raw = true`. 

Returns stdClass PHP object(s) for the result or false. 

### execute
`public static function execute($sql = "", $multirow = false, $raw = false)`

Does the same exact thing, just called statically.

Creates a new connection for each call, so it's not advisable to use this in a loop.

### escape
`public function escape($string)`

Returns the [escaped](https://www.php.net/manual/en/mysqli.real-escape-string.php) string.


### real_escape
`public static function real_escape($string)`

Does the same thing, just [called statically](https://www.php.net/manual/en/language.oop5.static.php).

### getLastError
`public function getLastError()`

Returns the last error as string.

### affected_rows
`public function affected_rows()`

Returns the [affected rows](https://www.php.net/manual/en/mysqli.affected-rows.php) of the last query.

### insert_id
`public function insert_id()`

Return the [insert id](https://www.php.net/manual/en/mysqli.insert-id.php) of the last query.

