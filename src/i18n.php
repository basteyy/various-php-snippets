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

class i18n
{
    /** @var array where the language is stored */
    private static array $translation_folder;

    /** @var string Trannslation File Name */
    private static string $translation_language;

    /** @var array Storage for the translations */
    private static array $translations = [];

    /** @var int $apcu_cache_ttl The ttl of the apcu cache in seconds */
    private static int $apcu_cache_ttl = 360;

    /**
     * Try to translate $string
     * @param string $string
     * @return string
     */
    public static function getTranslation(string $string) : string
    {
        if(
            !isset(self::$translation_folder) ||
            !isset(self::$translation_language) ||
            count(self::$translation_folder) === 0
        ) {
            return $string;
        }

        if(0 === count(self::$translations)) {

            $is_apc_installed = function_exists('apcu_enabled') && apcu_enabled();

            foreach(self::$translation_folder as $folder) {

                $cache_name = '_translation_' . self::$translation_language;

                if($is_apc_installed && apcu_exists($cache_name)) {
                    self::$translations += apcu_fetch($cache_name);
                } elseif(file_exists($folder . DIRECTORY_SEPARATOR . self::$translation_language . '.ini')) {
                    $parsed_ini = parse_ini_file(
                            $folder . DIRECTORY_SEPARATOR . self::$translation_language . '.ini',
                            false,
                            INI_SCANNER_RAW
                        );

                    if($is_apc_installed) {
                        apcu_store($cache_name, $parsed_ini, self::$apcu_cache_ttl);
                    }

                    self::$translations += $parsed_ini;
                }
            }
        }

        return self::$translations[hash('xxh3', $string)] ?? $string;

    }

    /**
     * Define the current language
     * @param string $language
     * @return void
     */
    public static function setTranslationLanguage(string $language) : void {
        self::$translation_language = $language;
    }

    /**
     * Define, where the language files are stored
     * @param string $folder
     * @return void
     */
    public static function addTranslationFolder(string $folder) : void {
        self::$translation_folder[] = str_ends_with($folder, '/') ? substr($folder, 0, -1) : $folder;
    }
}