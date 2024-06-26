<?php
namespace VictorOpusculo\AbelMagazine\Lib\Helpers;

use DateTimeZone;
use VictorOpusculo\MyOrm\{Option, Some};

final class Data
{
    public function __construct() { }

    public static function getMailConfigs()
    {
        $configs = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "/abel-magazine_config.ini", true);
        return $configs['regularmail'];
    }

    public static function truncateText(?string $string, int $maxLength) : string
    {
        if (!$string) return '';
        return mb_strlen($string) > $maxLength ? mb_substr($string, 0, $maxLength) . '...' : $string;
    }

    public static function hsc(?string $string) : string
    {
        return htmlspecialchars($string ?? '', ENT_NOQUOTES);
    }

    public static function hscq(?string $string) : string
    {
        return htmlspecialchars($string ?? '', ENT_QUOTES);
    }

    public static function flattenArray(array $demo_array) : array
    {
        $new_array = array();
        array_walk_recursive($demo_array, function($array) use (&$new_array) { $new_array[] = $array; });
        return $new_array;
    }

    /** 
     * @template T
     * @param T[] $input
     * @param array<string,pure-callable> $rules
     * @return mixed[]
     */
    public static function transformDataRows(array $input, array $rules) : array
    {
        $output = [];
        
        if ($input)
            foreach ($input as $row)
            {
                $newRow = [];
                foreach ($rules as $newKeyName => $ruleFunction)
                    $newRow[$newKeyName] = $ruleFunction($row);

                $output[] = $newRow;
            }
            
        return $output;
    }

    public static function formatNameCase(?string $string) : string
    {
        if (!$string)
            return '';

        return mb_convert_case($string, MB_CASE_TITLE, 'UTF-8');
    }

    public static function booleanTransformer(Option $val) : Some
    {
        $checked = $val->unwrapOr(0) ? 1 : 0;
        return Option::some($checked);
    }

    public static function getTimeZonesToJavascript() : string
    {
        return "
            AbelMagazine.Time ??= {};
            AbelMagazine.Time.TimeZones = [" . array_reduce(DateTimeZone::listIdentifiers(), fn($prev, $dtz) => ($prev ? $prev . ',' : '') . "\"$dtz\"" ) . "];
        ";
    }
} 