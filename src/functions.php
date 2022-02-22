<?php declare(strict_types=1);

namespace basteyy\VariousPhpSnippets;

use Exception;
use JetBrains\PhpStorm\NoReturn;

if(!function_exists('getRandomString')) {
    /**
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
