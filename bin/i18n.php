#!/usr/bin/php
<?php

/**
 * @deprecated Use "basteyy/php-i18n" instead
 */

include __DIR__ . "/../vendor/autoload.php";

// Redirect to "vendor/basteyy/php-i18n/src/bin/I18nBaker" with arguments
shell_exec('php ' . __DIR__ . '/../vendor/basteyy/php-i18n/src/bin/I18nBaker' . ' ' . implode(' ', $argv));
