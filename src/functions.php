<?php declare(strict_types=1);

namespace basteyy\VariousPhpSnippets;

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


if (!function_exists('varDebug')) {
    /**
     * Dump all passed variables and exit the script
     * @param ...$date
     */
    function varDebug(...$date)
    {
        http_response_code(503);
        ob_end_clean();
        echo '<pre>';
        foreach ($date as $item) {
            echo '<code>';
            var_dump($item);
            echo '</code><hr />';
        }
        echo '</pre>';
        die();
    }
}
