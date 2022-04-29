#!/usr/bin/php
<?php

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

if ($argc < 3 || $argc > 4) {
    enl('[Error]: Required parameter missing',
        'Usage: php ' . $argv[0] . ' SOURCE TARGET --no-comments (output without comments)',
        'Source is a folder and target a file',
        'Example:',
        'php ' . $argv[0] . ' ' . dirname(__DIR__) . ' ' . dirname(__DIR__) . '/lang/en_US.ini');
} else {

    if (!is_dir($argv[1])) {
        enl('[Error] First argument is not a valid folder!');
        return false;
    }

    /* Comments in Output-File? */
    $echo_comments = !in_array('--no-comments', $argv);

    define("_DOUBLE_EOL", $echo_comments ? PHP_EOL . PHP_EOL : PHP_EOL);

    /** @var DirectoryIterator $dir */
    /** @var SplFileInfo $file */
    //$dir = new DirectoryIterator($argv[1]);
    $dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($argv[1]));
    $files_found = [];
    $translations_counter = 0;
    $skip = false;
    if($echo_comments) {
        $build_string = '; File build on ' . date('d.m.y H:i:s') . _DOUBLE_EOL;
    } else {
        $build_string = '';
    }

    $randkomkey = '{{' . uniqid() . '}}';

    /* Random Key for replacing \" */
    $back_slashed_double_quote_replacement = '{{' . hash('xxh3', uniqid() . '"') . '}}';

    /* Random Key for replacing \' */
    $back_slashed_single_quote_replacement = '{{' . hash('xxh3', uniqid() . "'") . '}}';


    $processed_strings = [];

    if (file_exists($argv[2])) {
        // Add to the existing file
        $existing_translations = parse_ini_file($argv[2], false);
    }
    foreach ($dir as $file) {
        if ($file->isFile()) {
            $content = file_get_contents($file->getPathname());
            if (str_contains($content, '__(')) {
                $files_found[] = $file->getRealPath();

                if($echo_comments) {
                    $build_string .= '; From file: ' . $file->getRealPath() . _DOUBLE_EOL;
                }

                $content = str_replace('\"', $back_slashed_double_quote_replacement, $content);
                $content = str_replace("\'", $back_slashed_single_quote_replacement, $content);

                preg_match_all(
                    '#__\(([\'"])(.*?)\1.#ms',
                    $content,
                    $matches,
                    PREG_SET_ORDER
                );

                foreach ($matches as $match) {

                    list(,$used_sign, $string) = $match;

                    if ('"' === $used_sign) {
                        $sign = '"';
                        $sign_escape = '\"';
                    } elseif ("'" === $used_sign) {
                        $sign = "'";
                        $sign_escape = "\'";
                    } else {
                        $skip = true;
                    }


                    if (!$skip) {
                        $translations_counter++;

                        $hash_value = hash('xxh3', $string);
                        $new_string = str_replace($sign, $sign_escape, $string);

                        if($echo_comments) {
                            enl('Found in ' . $file->getRealPath(), "\t" . '==> ' . $new_string);
                        }


                        if(!isset($processed_strings[$hash_value])) {

                            if("'" === $sign) {
                                $processed_strings[$hash_value] = str_replace("'", "\'", $new_string);
                            }

                            /** Because always its inside doubles */
                            $processed_strings[$hash_value] = str_replace('"', '\\"', $processed_strings[$hash_value]);
                            $processed_strings[$hash_value] = str_replace(["\n\r", "\n", "\r"], '', $processed_strings[$hash_value]);

                            if(isset($existing_translations[$hash_value]) && $processed_strings[$hash_value] !== $existing_translations[$hash_value]) {
                                // Translation already exists
                                if($echo_comments) {
                                    $build_string .= '; Following translation from former translation! ' . PHP_EOL;
                                    $build_string .= '; Original: ' . str_replace("\n", "\n;", $new_string ) . PHP_EOL . '; ' . $hash_value . ' = "' . $processed_strings[$hash_value] . '"' . PHP_EOL;
                                }
                                $build_string .= $hash_value . ' = "' . $existing_translations[$hash_value] . '"' . _DOUBLE_EOL;
                            } else {
                                if($echo_comments) {
                                    $build_string .= '; Original: ' . str_replace("\n", "\n;", $new_string ) . PHP_EOL;
                                }
                                $build_string .= $hash_value . ' = "' . $processed_strings[$hash_value] . '"' .
                                    _DOUBLE_EOL;
                            }

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


        $build_string = str_replace($back_slashed_double_quote_replacement, '\"', $build_string);
        $build_string = str_replace($back_slashed_single_quote_replacement, "\'", $build_string);

        file_put_contents($argv[2], $build_string);
    }


}
