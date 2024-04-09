<?php

use VictorOpusculo\AbelMagazine\Lib\Helpers\URLGenerator;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\PComp\Rpc\RpcInitializer;

require_once "vendor/autoload.php";

$i18n = I18n::init($_COOKIE['language'] ?? 'pt_BR');
$rpc = new RpcInitializer(require_once __DIR__ . '/app/router.php', "route", "call", URLGenerator::generateFunctionUrl("{route}", "{call}"));