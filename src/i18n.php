<?php

declare(strict_types=1);

namespace basteyy\VariousPhpSnippets;

class i18n
{
    /** @var string where the language is stored */
    private static string $translation_folder;

    /** @var string Trannslation File Name */
    private static string $translation_language;

    /** @var array Storage for the translations */
    private static array $translations;

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
            !file_exists(self::$translation_folder . DIRECTORY_SEPARATOR . self::$translation_language . '.ini')) {
            return $string;
        }

        if(!isset(self::$translations)) {
            // Try to load translation
            self::$translations = parse_ini_file(self::$translation_folder . DIRECTORY_SEPARATOR . self::$translation_language . '.ini');
        }

        return self::$translations[$string] ?? $string;

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
        self::$translation_folder = str_ends_with($folder, '/') ? substr($folder, 0, -1) : $folder;
    }
}