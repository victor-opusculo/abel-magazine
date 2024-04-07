<?php
namespace VictorOpusculo\AbelMagazine\Lib\Internationalization;

use \carry0987\I18n\I18n as cI18n;

final class I18n
{
    private function __construct() {}

    public static cI18n $instance;

    public static function init(?string $initializeAs = null) : void
    {
        self::$instance = new cI18n(
            [
                'useAutoDetect' => false,
                'langFilePath' => __DIR__ . '/../../lang', 
                'cachePath' => __DIR__ . '/../../cache'
            ]);

        self::$instance->initialize($initializeAs);
        self::$instance->setLangAlias([ 'pt_BR' => 'Português (Brasil)', 'en_US' => 'English (US)' ]);
    }

    public static function get(string $identifier) : string
    {
        return self::$instance->fetch($identifier);
    }

    public static function availableLangs() : array
    {
        return self::$instance->fetchLangList();
    }
}