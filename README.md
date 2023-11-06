> 
> Disclaimer 2023: This package is not maintained anymore. The parts of the package are now own packages. Please use them instead.
> 

# various-php-snippets

Just a collection of various php snippets I used from time to time.

## Install the package

>
> Deprecated: Please use the new packages instead:
> - [basteyy/php-stringer](https://github.com/basteyy/php-stringer)
> - [basteyy/php-i18n](https://github.com/basteyy/php-i18n)
> - [basteyy/var-debug](https://github.com/basteyy/var-debug)
>

Please use composer to install the package:

```bash
composer require basteyy/various-php-snippets
```

## Snippets Overview

### `getDateTimeFormat`

The function `getDateTimeFormat` create a valid [MySQL-Datetime-Formatted](https://dev.mysql.com/doc/refman/8.0/en/datetime.html) datetime.

By default the function will create the current datetime. You can pass a [DateTime](https://www.php.net/manual/en/class.datetime) Instance to change the output.

#### Usage
```php
echo \basteyy\VariousPhpSnippets\getDateTimeFormat();
// Result: current date time in format: yyy-dd-mm hh:mm:ii

echo \basteyy\VariousPhpSnippets\getDateTimeFormat((new DateTime('2020-01-01 10:10:10'))->modify('+2 years'));
// Result: 2022-01-01 10:10:10
```

### `getNiceDateTimeFormat`

The function `getNiceDateTimeFormat` returns a nice to read version of a given or, if first argument is null, the current  [MySQL-Datetime-Formatted](https://dev.mysql.com/doc/refman/8.0/en/datetime.html) datetime.

By default, the function will create the current datetime. You can pass a [DateTime](https://www.php.net/manual/en/class.datetime) Instance to change the output.

Be default, the function used the current default Locale. You can set up a Locale somewhere in your code (before calling the function) or pass a locale as a string as the 
second parameter to the function. 

#### Usage
```php
echo \basteyy\VariousPhpSnippets\getNiceDateTimeFormat();
// Result: current date time in format: May 01 22, 09:59 pm 

echo \basteyy\VariousPhpSnippets\getDateTimeFormat((new DateTime('2020-01-01 10:10'))->modify('+2 years'), 'de');
// Result: 01. Januar 2022, 10:10

echo \basteyy\VariousPhpSnippets\getDateTimeFormat((new DateTime('2020-01-01 10:10'))->modify('+2 years'));
// Result: January 01 22, 10:10 am
```

### `remove_double_slashes` 

>
> Deprecated: Use `\basteyy\Stringer\removeDoubleSlashes` instead.
>

### `getRandomString`

>
> Deprecated: Use `\basteyy\Stringer\getRandomString` instead.
>

### `slugify`

>
> Deprecated: Use `\basteyy\Stringer\slugify` instead.
>

### `varDebug`

>
> Deprecated: Use `\varDebug` from `basteyy/var-debug` instead.
>

## i18n

>
> Deprecated: Use `basteyy/php-i18n` instead.
>