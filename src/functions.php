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
use function DI\value;


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
     * @see https://stackoverflow.com/a/48433241/2378618
     * @param string $file Filepath where the ini should be stored
     * @param array $array The Data for the ini
     * @param string|null $append_string In case, leading text for the ini (be aware, that this can broke the syntax)
     * @param string|null $attach_string In case, attached text for the ini (be aware, that this can broke the syntax)
     * @return bool
     */
    function write_ini_file(string $file, array $array = [], string $append_string = null, string $attach_string = null ) {

        // check first argument is string
        if (!is_string($file)) {
            throw new \InvalidArgumentException('Function argument 1 must be a string.');
        }

        // check second argument is array
        if (!is_array($array)) {
            throw new \InvalidArgumentException('Function argument 2 must be an array.');
        }

        $parse_value = function(mixed $value) {

            if(is_bool($value)) {
                return $value ? 'yes' : 'no';
            }

            if(is_int($value) || is_float($value)) {
                return $value;
            }

            if(is_string($value)) {
                return '"' . str_replace('"', '\"', $value) . '"';
            }

        };

        $array_deconstruct = function(array|string $array_or_string) use($parse_value, &$array_deconstruct) {
            $data = [];
            foreach($array_or_string as $item => $value) {
                if(is_array($value)) {

                    $data[] = PHP_EOL . "[$item]";
                    $data[$item] = $array_deconstruct($value);
                } else {
                    $data[] = $item . ' = ' . $parse_value($value);
                }
            }

            return implode(PHP_EOL, $data);
        };

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
                usleep(rand(1, 25000));
            }
            $retries += 1;
        } while (!flock($fp, LOCK_EX) && $retries <= $max_retries);

        // couldn't get the lock
        if ($retries == $max_retries) {
            return false;
        }

        // got lock, write data
        fwrite($fp, (isset($append_string) ? $append_string . PHP_EOL : '') . $array_deconstruct($array) . (isset($attach_string) ? PHP_EOL . $attach_string : ''));

        // release lock
        flock($fp, LOCK_UN);
        fclose($fp);

        return true;
    }
}
