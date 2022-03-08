<?php declare(strict_types=1);
/**
 * basteyy/various-php-snippets
 *
 * A few PHP snippets which I have used many times in many projects. Contributions are appreciated.
 *
 * @author Sebastian Eiweleit <sebastian@eiweleit.de>
 * @website https://github.com/basteyy/various-php-snippets
 * @license MIT
 */

namespace basteyy\VariousPhpSnippets;

use Exception;
use JetBrains\PhpStorm\NoReturn;


if(!function_exists('remove_double_slashes')) {
    /**
     * Remove double slashed from a string
     * @param string $path
     * @return string
     */
    function remove_double_slashes(string $path) : string {
        return str_replace('//', '/', $path);
    }
}

if(!function_exists('getRandomString')) {
    /**
     * Return a random key in the length of $length
     * @throws Exception
     */
    function getRandomString(int $length = 32) : string {
        return substr(bin2hex(random_bytes($length)), 0, $length);
    }
}

if (!function_exists('slugify')) {
    function slugify(string $text, string $divider = '-'): string
    {
        // replace non letter or digits by divider
        $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, $divider);

        // remove duplicate divider
        $text = preg_replace('~-+~', $divider, $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }
}


if (!function_exists('vdarDebug')) {
    /**
     * Dump all passed variables and exit the script
     * @param ...$mixed
     */
    #[NoReturn] function varDebug(...$mixed)
    {
        http_response_code(503);
        ob_end_clean();
        echo '<html><head><title>varDebug</title><meta charset="utf-8"><style>body{ background-color: #2D4263; color: #EEEEEE; padding: 1em; font-family: "Ubuntu Light",serif; font-weight: lighter } a { color: #C84B31;} a:hover {color:#A13333} pre { overflow: auto; } h2 span { font-size: .7em; float: right; font-weight: lighter; background-color: #C84B31; padding: .2em; } h2 span:hover { font-size: .7em; float: right; font-weight: lighter; background-color: #A13333; cursor: pointer; } code { display: block; padding: 1em; border-radius: .4em; background-color: #191919; font-family: "Ubuntu Mono",monospace; font-size: 1.1em; line-height: 1.5em; }</style></head><body>';
        echo '<h1>Debugging ' . count($mixed) . ' Items</h1>';
        $x = 0;
        foreach ($mixed as $item) {
            $x++;
            echo '<h2>#'.$x.' <span class="google" data-debug="debug_'.$x.'">Google</span></h2><pre><code id="debug_'.$x.'">';
            var_dump($item);
            echo '</code></pre>';
        }

        echo'<h2>Backtrace</h2><pre><code>';
        debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        echo '</code></pre>';

        echo '<h2>Server-Data</h2><pre><code>';
        foreach ($_SERVER as $name => $value) {
            echo '$_SERVER["' . $name . '"]: ' . $value ."\n";
        }
        echo '</code></pre> <script> document.querySelectorAll("span.google").forEach(function (element, index, array){ element.addEventListener("click", function() { window.open("https://www.google.com/search?q=" + encodeURI("+php " + document.querySelector("#" + element.getAttribute("data-debug")).innerHTML), "_blank"); }) }); </script> <footer>Problems of suggestions? Submit it on <a href="https://github.com/basteyy/various-php-snippets">github/basteyy/various-php-snippets</a></footer></body></html>';


        die();
    }
}


if (!function_exists('__')) {
    /**
     * Translation Helper
     * @param string $string
     * @param ...$args
     * @return string
     */
    function __(string $string, ...$args) : string {

        // Get the translation
        $string = i18n::getTranslation($string);

        if(count($args) == 0 ) {
            return $string;
        }

        return sprintf($string, ...$args);
    }
}

if (!function_exists('write_ini_file')) {
    /**
     * Write an ini configuration file
     * @param string $file
     * @param array  $array
     * @see https://stackoverflow.com/a/48433241/2378618
     * @return bool
     */
    function write_ini_file($file, $array = []) {
        // check first argument is string
        if (!is_string($file)) {
            throw new \InvalidArgumentException('Function argument 1 must be a string.');
        }

        // check second argument is array
        if (!is_array($array)) {
            throw new \InvalidArgumentException('Function argument 2 must be an array.');
        }

        // process array
        $data = array();
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $data[] = "[$key]";
                foreach ($val as $skey => $sval) {
                    if (is_array($sval)) {
                        foreach ($sval as $_skey => $_sval) {
                            if (is_numeric($_skey)) {
                                $data[] = $skey.'[] = '.(is_numeric($_sval) ? $_sval : (ctype_upper($_sval) ? $_sval : '"'.$_sval.'"'));
                            } else {
                                $data[] = $skey.'['.$_skey.'] = '.(is_numeric($_sval) ? $_sval : (ctype_upper($_sval) ? $_sval : '"'.$_sval.'"'));
                            }
                        }
                    } else {
                        $data[] = $skey.' = '.(is_numeric($sval) ? $sval : (ctype_upper($sval) ? $sval : '"'.$sval.'"'));
                    }
                }
            } else {
                $data[] = $key.' = '.(is_numeric($val) ? $val : (ctype_upper($val) ? $val : '"'.$val.'"'));
            }
            // empty line
            $data[] = null;
        }

        // open file pointer, init flock options
        $fp = fopen($file, 'w');
        $retries = 0;
        $max_retries = 100;

        if (!$fp) {
            return false;
        }

        // loop until get lock, or reach max retries
        do {
            if ($retries > 0) {
                usleep(rand(1, 5000));
            }
            $retries += 1;
        } while (!flock($fp, LOCK_EX) && $retries <= $max_retries);

        // couldn't get the lock
        if ($retries == $max_retries) {
            return false;
        }

        // got lock, write data
        fwrite($fp, implode(PHP_EOL, $data).PHP_EOL);

        // release lock
        flock($fp, LOCK_UN);
        fclose($fp);

        return true;
    }
}
