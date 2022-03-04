#!/usr/bin/php
<?php

const _DOUBLE_EOL = PHP_EOL . PHP_EOL;

$special_word_list = [
        'yes', 'no', 'Yes', 'No'
];

function enl(...$strings)
{
    if (count($strings) === 1) {
        echo "\n" . "\t" . $strings[0] . "\n";
    } else {
        foreach ($strings as $string) {
            echo "\n" . "\t" . $string;
        }
        echo _DOUBLE_EOL;
    }
}

function sanFolder() {

}

if ($argc !== 3) {
    enl('[Error]: Required parameter missing',
        'Usage: php ' . $argv[0] . ' SOURCE TARGET',
        'Source is a folder and target a file',
        'Example:',
        'php ' . $argv[0] . ' ' . dirname(__DIR__) . ' ' . dirname(__DIR__) . '/lang/en_US.ini');
} else {

    if (!is_dir($argv[1])) {
        enl('[Error] First argument is not a valid folder!');
        return false;
    }

    /** @var DirectoryIterator $dir */
    /** @var SplFileInfo $file */
    //$dir = new DirectoryIterator($argv[1]);
    $dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($argv[1]));
    $files_found = [];
    $translations_counter = 0;
    $skip = false;
    $build_string = '; File build on ' . date('d.m.y H:i:s') . _DOUBLE_EOL;
    $randkomkey = '{{' . uniqid() . '}}';
    $processed_strings = [];
    foreach ($dir as $file) {
        if ($file->isFile()) {
            $content = file_get_contents($file->getPathname());
            if (str_contains($content, '__(')) {
                $files_found[] = $file->getRealPath();

                $build_string .= '; From file: ' . $file->getRealPath() . _DOUBLE_EOL;

                preg_match_all(
                    '/__\(([^\)]*)\)/',
                    $content,
                    $matches,
                    PREG_PATTERN_ORDER
                );

                foreach ($matches[1] as $match) {

                    if ('"' === $match[0]) {
                        $sign = '"';
                        $sign_escape = '\"';
                    } elseif ("'" === $match[0]) {
                        $sign = "'";
                        $sign_escape = "\'";
                    } else {
                        $skip = true;
                    }

                    if (!$skip) {
                        $translations_counter++;

                        // Remove first sign
                        $m = substr($match, 1, -1);
                        $m = str_replace($sign_escape, $randkomkey, $m);
                        $new_m = strstr($m, $sign, true);
                        if (false === $new_m) {
                            $new_m = $m;
                        }

                        $new_m = str_replace($randkomkey, $sign_escape, $new_m);
                        //enl('Found in ' . $file->getRealPath(), '"' . $match, "\t" . '====> ' . $new_m );
                        enl('Found in ' . $file->getRealPath(), "\t" . '==> ' . $new_m);

                        $hash_value = hash('xxh3', $new_m);

                        if(!isset($processed_strings[$hash_value])) {
                            $processed_strings[$hash_value] = htmlspecialchars($new_m, ENT_COMPAT);
                            $build_string .= '; Original: ' . $new_m . PHP_EOL . $hash_value . ' = "' . htmlspecialchars($new_m, ENT_COMPAT) . '"' . _DOUBLE_EOL;
                        }
                    }
                }

            }

        }

    }

    if (count($files_found) < 1) {
        enl('No files found');
    } else {
        enl(count($files_found) . ' Files found with __() Function. ' . $translations_counter . ' Translations found and put into target file ' . $argv[2]);

        if (file_exists($argv[2])) {
            // Add to the existing file
        }

        file_put_contents($argv[2], $build_string);
    }


}