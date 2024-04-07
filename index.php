<?php

use VictorOpusculo\AbelMagazine\Lib\Helpers\URLGenerator;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\PComp\{HeadManager, StyleManager, ScriptManager, AppInitializer};
use function VictorOpusculo\PComp\Prelude\render;

require_once "vendor/autoload.php";

if (!empty($_GET['change_lang']))
	setcookie('language', $_GET['change_lang']);

I18n::init($_GET['change_lang'] ?? $_COOKIE['language'] ?? 'pt_BR');
$app = new AppInitializer(require_once __DIR__ . '/app/router.php');

URLGenerator::loadConfigs();

?><!DOCTYPE HTML>
<html>
	<head>
		<!-- Desenvolvido por Victor Opusculo -->
		<meta charset="utf8"/>
		<meta content="width=device-width, initial-scale=1" name="viewport" />
		<meta name="description" content="Plataforma EAD da ABEL">
		<meta name="keywords" content="">
  		<meta name="author" content="Victor Opusculo Oliveira Ventura de Almeida">
		<link rel="stylesheet" href="<?= URLGenerator::generateFileUrl('assets/twoutput.css') ?>"/>
		<script>
			const AbelMagazine = {};
			const useFriendlyUrls = <?= URLGenerator::$useFriendlyUrls ? 'true' : 'false' ?>;
			const baseUrl = '<?= URLGenerator::$baseUrl ?>';
		</script>
		<script src="<?= URLGenerator::generateFileUrl('assets/script/URLGenerator.js') ?>"></script>
		<?= HeadManager::getHeadText() ?>
		<?= StyleManager::getStylesText() ?>
	</head>
	<body>
		<?php render($app->mainFrameComponents); ?>
	</body>
	<?= ScriptManager::getScriptText() ?>
</html>