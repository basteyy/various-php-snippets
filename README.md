# various-php-snippets

Just a collection of various php snippets I used from time to time

## Install the package

Please use composer to install the package:

```bash
composer require basteyy/various-php-snippets
```

## Snippets Overview

### `write_ini_file(string $file, array $array, string $append_string, string $attach_string)`

The function `write_ini_file` will write the array `$array` into a INI-file `$file`. You can specify a header by using third parameter and a footer by using fourth parameter. Be aware that your header/footer is not validated. So, It's possible to break the INI by providing wrong formatted text. 

Function posted from [Lawrence Cherone]() onm [Stackoverflow](). 

#### Usage

```php
echo \basteyy\VariousPhpSnippets\write_ini_file('/var/www/data/foo/config.ini', ['something' => 'somewhere', 'hello' => 'world', 'foobar' => true]);
// Result: /var/www/data/foo/config.ini
// something = "somewhere"
// hello = "world"
// foobar = yes
```

### `remove_double_slashes`

The function "remove_double_slashes" replaces double slashes in a string.

#### Usage

```php
echo \basteyy\VariousPhpSnippets\remove_double_slashes('/var/www//data//foo//');
// Result: /var/www/data/foo/
```

### `getRandomString` 

The function `getRandomString` generates a random string. Default length of the string is 32 signs. You can get longer or shorter strings by providing the int of the length to 
the function.

#### Usage

```php
echo \basteyy\VariousPhpSnippets\getRandomString(12);
// Result: skjf33bfssds
```

### `slugify`

The function `slugify` makes a slugified string from any string.

#### Usage

```php
echo \basteyy\VariousPhpSnippets\slugify('Hallo mein lieber');
// Result: hallo-mein-lieber
```

### `varDebug`

The function `varDebug` is a debugger, which uses var_debug but ends the script.

#### Usage

```php
$var = [1,2,3];
\basteyy\VariousPhpSnippets\varDebug($var);
// Will print something like: https://osob.de/v/wjbbv
```

## i18n

I added a very basic i18n feature. The function based on a class with three static methods and the translation shortcut `__`. Be aware, that this is a really basic implementation. Currently, there is no fancy support for bigger strings. 

### Usage of i18n

The languages files are in ini format. For supporting all variantes of strings, the key is hashed using the xxh3 algorithm (which is the fastest algorithm for php now).

```ini
; Content of /var/www/lang/de_DE.ini

; Original: Add 
2519a9e8bc4544e5 = Hinzufügen

; Original: Remove
d93080c2fe513df2 = Entfernen

; Original: Remove %s Items
4937f99272d45d21 = %s Dinge entfernen
```

```php
// Content of /var/www/index.php

use basteyy\VariousPhpSnippets\i18n;
use function basteyy\VariousPhpSnippets\__;

i18n::addTranslationFolder(__DIR__ . '/lang/');
i18n::addTranslationFolder(__DIR__ . '/another_folder/');
i18n::setTranslationLanguage('de_DE');

echo __('Add');
// Result: Hinzufügen

echo __("Remove");
// Result: Entfernen

echo __('Remove %s Item\'s ', 3212);
// Result: 3212 Dinge entfernen

echo __('A new string');
// Result: A new string

echo __('A new string with placeholder %s', 'inside');
// Result: A new string with placeholder inside
```

#### Translate an app/website

You can use the bash script to generate the translation file. Please be aware, that this is a simple solution. It's easy to break it down with using `=`.
