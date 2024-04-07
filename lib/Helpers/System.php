<?php
namespace VictorOpusculo\AbelMagazine\Lib\Helpers;

final class System
{
    private function __construct() { }

    public static function systemBaseDir() : string
    {
        return __DIR__ . '/../..';
    } 

    public static function getMailConfigs()
    {
        $configs = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "/abel-magazine_config.ini", true);
        return $configs['regularmail'];
    }

    public static function siteName() : string
    {
        return "Revistas on-line da ABEL";
    }

    public static function baseDir() : string
    {
        return __DIR__ . '/../../';
    }

    public static function getHttpProtocolName()
    {
        $isHttps = $_SERVER['HTTPS'] ?? $_SERVER['REQUEST_SCHEME'] ?? $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? null;
        $isHttps = $isHttps && (strcasecmp('on', $isHttps) == 0 || strcasecmp('https', $isHttps) == 0);
        return $isHttps ? 'https' : 'http';
    }
}