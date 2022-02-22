# various-php-snippets

Just a collection of various php snippets I used from time to time

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

echo __('Remove');
// Result: Entfernen

echo __('Remove %s Items ', 3212);
// Result: 3212 Dinge entfernen

echo __('A new string');
// Result: A new string

echo __('A new string with placeholder %s', 'inside');
// Result: A new string with placeholder inside
```