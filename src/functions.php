<?php
declare(strict_types=1);
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

use DateTime;
use Exception;
use JetBrains\PhpStorm\NoReturn;

if (!function_exists('getDateTimeFormat')) {
    /**
     * Function for creating a correct formatted date-time-string (for using in databases e.g.)
     * @param DateTime|null $dateTime
     * @return string
     */
    function getDateTimeFormat(DateTime $dateTime = null): string
    {
        return ($dateTime ?? new DateTime('now'))->format('Y-m-d H:i:s');
    }
}

if (!function_exists('getNiceDateTimeFormat')) {
    /**
     * Get dateTime as a nice to read string.
     * @param DateTime|null $dateTime
     * @param string|null $locale
     * @return string
     */
    function getNiceDateTimeFormat(DateTime $dateTime = null, string $locale = null): string
    {
        if (!$dateTime) {
            $dateTime = new \DateTime('now');
        }

        if (!isset($locale)) {
            $locale = substr(\Locale::getDefault(), 0, 2);
        }

        return $dateTime->format(match ($locale) {
            'de' => 'd. F y, H:i',
            default => 'F d. y, h:i a'
        });
    }
}

if (!function_exists('remove_double_slashes')) {
    /**
     * Remove double slashed from a string
     * @param string $path
     * @return string
     */
    function remove_double_slashes(string $path): string
    {
        return str_replace('//', '/', $path);
    }
}

if (!function_exists('getRandomString')) {
    /**
     * Return a random key in the length of $length
     * @throws Exception
     */
    function getRandomString(int $length = 32) : string
    {
        return substr(bin2hex(random_bytes($length)), 0, $length);
    }
}

if (!function_exists('getRandomLowerAlphaString')) {
    /**
     * Return a random key in the length of $length
     * @throws Exception
     */
    function getRandomLowerAlphaString(int $length = 32) : string
    {
        return getRandomAlphaString($length, 'abcdefghijklmnopqrstuvwxyz');
    }
}

if (!function_exists('getRandomUpperAlphaString')) {
    /**
     * Return a random key in the length of $length
     * @throws Exception
     */
    function getRandomUpperAlphaString(int $length = 32) : string
    {
        return getRandomAlphaString($length, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
    }
}

if (!function_exists('getRandomAlphaString')) {
    /**
     * Return a random key in the length of $length
     * @throws Exception
     */
    function getRandomAlphaString(int $length = 32, string $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') : string
    {
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

if (!function_exists('getRequestIpAddress')) {
    /**
     * Return the current IP Address as a string
     * @return string
     * @see https://stackoverflow.com/a/13646735/2378618
     */
    function getRequestIpAddress(): string
    {
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
            $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        $client = $_SERVER['HTTP_CLIENT_IP'] ?? null;
        $forward = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? null;
        $remote = $_SERVER['REMOTE_ADDR'];

        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }

        return $ip;
    }
}

if (!function_exists('getSlugifiedText')) {
    /**
     * Convert a string into a webaddress optimzied version
     * @param string $text
     * @param string $divider
     * @return string
     */
    function getSlugifiedText(string $text, string $divider = '-', string $empty_default_text = 'n-a'): string
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

        return $text ?? $empty_default_text;
    }
}


if (!function_exists('slugify')) {
    /**
     * Convert a string into a webaddress optimzied version
     * @param string $text
     * @param string $divider
     * @return string
     * @deprecated Use getSlugifiedText
     */
    function slugify(string $text, string $divider = '-'): string
    {
        return getSlugifiedText($text, $divider);
    }
}

if (!function_exists('varDebug')) {
    /**
     * Dump all passed variables and exit the script. Used a styled output. Accordion inmspired by Raúl Barrera
     * @param ...$mixed
     * @see https://codepen.io/raubaca/pen/PZzpVe
     */
    #[NoReturn] function varDebug(...$mixed)
    {
        $cache_output = function ($item) {
            ob_clean();
            var_dump($item);

            $data = ob_get_contents();

            return is_string($data) ? (htmlentities($data)) : 'nodata' . $data;
        };

        $x = 0;
        $data_collection = '';
        foreach ($mixed as $item) {
            $x++;
            $xx = time() . $x;
            $data_collection .= '<div><input type="checkbox" id="_debug_' . $xx . '" checked /><label for="_debug_' . $xx . '">#' . $x .
                ' <span class="google" data-debug="debug_' . $xx . '">Google</span></label> <pre><code id="debug_' . $xx . '">';
            $data_collection .= $cache_output($item);
            $data_collection .= '</code></pre></div>';
        }

        $enviremental = ['SERVER' => $_SERVER, 'POST' => $_POST, 'GET' => $_GET, 'REQUEST' => $_REQUEST];

        foreach ($enviremental as $env => $values) {
            $data_collection .= '<div id="' . $env . '"><input type="checkbox" id="_' . $env . '"><label for="_' . $env . '">$_' . $env . '</label> <pre><code><table>';

            foreach ($enviremental[$env] as $name => $content) {
                $data_collection .= sprintf('<tr><td class="title"><span class="copyme">$_%s[\'%s\']</span> </td> <td>=&gt;</td>  <td><pre class="in-table"><code>%s</code></pre></td></tr>',
                    $env,
                    $name,
                    $cache_output
                    ($content));
            }

            // $data_collection .= $cache_output();
            $data_collection .= '</table></code></pre></div>' . PHP_EOL;
        }
        http_response_code(503);
        ob_clean();
        include __DIR__ . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR . 'varDebugTemplate.php';
        exit();
    }
}


if (!function_exists('__')) {
    /**
     * Translation Helper
     * @param string $string
     * @param ...$args
     * @return string
     */
    function __(string $string, ...$args): string
    {

        // Get the translation
        $string = i18n::getTranslation($string);

        if (count($args) == 0) {
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
    function write_ini_file(string $file, array $array = [], string $append_string = null, string $attach_string = null): bool
    {

        $parse_value = function (mixed $value) {

            if (is_bool($value)) {
                return $value ? 'yes' : 'no';
            }

            if (is_int($value) || is_float($value)) {
                return $value;
            }

            if (is_string($value)) {
                return ctype_upper($value) ? $value : '"' . str_replace('"', '\"', $value) . '"';
            }

        };

        $array_deconstruct = function (array|string $array_or_string) use ($parse_value, &$array_deconstruct) {
            $data = [];
            foreach ($array_or_string as $item => $value) {
                if (is_array($value)) {

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
