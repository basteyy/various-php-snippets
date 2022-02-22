# various-php-snippets

Just a collection of various php snippets I used from time to time

## Install the package

Please use composer to install the package:

```bash
composer require basteyy/various-php-snippets
```

## Snippets Overview

### `getRandomString` 

The function `getRandomString` generates a random string. Default length of the string is 32 signs. You can get longer or shorter strings by providint the int of the length to 
the function: 
```php
echo \basteyy\VariousPhpSnippets\getRandomString(12);
// Result: skjf33bfssds
```

### `slugify`

The function `slugify` makes a slugged string from any string:

```php
echo \basteyy\VariousPhpSnippets\slugify('Hallo mein lieber');
// Result: hallo-mein-lieber
```

### `varDebug`

The function `varDebug` is a debugger, which uses var_debug but ends the script.

## i18n

I added a very basic i18n feature. The function based on a class with three static methods and the translation shortcut `__`.

### Usage of i18n

The languages files are in ini format

```ini
; Content of /var/www/lang/de_DE.ini

Add = Hinzufügen
Remove = Entfernen
Remove %s Items = %s Dinge entfernen
```

```php
// Content of /var/www/index.php

use basteyy\VariousPhpSnippets\i18n;
use function basteyy\VariousPhpSnippets\__;

i18n::addTranslationFolder(__DIR__ . '/lang/');
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

#### Translate a app/website

You can use the bash script to generate the translation file. Please be aware, that this is a simple solution. It's easy to break it down with using `=`.