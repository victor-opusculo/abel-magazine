<?php

use VictorOpusculo\AbelMagazine\Lib\Helpers\URLGenerator;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\PComp\{HeadManager, StyleManager, ScriptManager, AppInitializer};
use function VictorOpusculo\PComp\Prelude\render;

require_once "vendor/autoload.php";

if (!empty($_GET['change_lang']))
	setcookie('language', $_GET['change_lang'], 0, '/');

I18n::init($_GET['change_lang'] ?? $_COOKIE['language'] ?? 'pt_BR');
$app = new AppInitializer(require_once __DIR__ . '/app/router.php');

URLGenerator::loadConfigs();

?><!DOCTYPE HTML>
<html>
	<head>
		<!-- Desenvolvido por Victor Opusculo -->
		<meta charset="utf8"/>
		<meta content="width=device-width, initial-scale=1" name="viewport" />
		<meta name="description" content="<?= \VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n::get("layout.topBarSiteDescription") ?>">
		<meta name="keywords" content="revista, científica, ciência, escolas de governo, estado, governo, legislativo, contas públicas, artigos">
  		<meta name="author" content="Victor Opusculo Oliveira Ventura de Almeida">
		<link rel="stylesheet" href="<?= URLGenerator::generateFileUrl('assets/twoutput.css') ?>"/>
		<link rel="shortcut icon" type="image/x-icon" href="<?= URLGenerator::generateFileUrl("assets/favicon.ico") ?>" />
		<script>
			const AbelMagazine = {};
			AbelMagazine.Lang ??= { ...(() => (<?= I18n::getAlertsTranslationsAsJson() ?>))() };
			const useFriendlyUrls = <?= URLGenerator::$useFriendlyUrls ? 'true' : 'false' ?>;
			const baseUrl = '<?= URLGenerator::$baseUrl ?>';
		</script>
		<script src="<?= URLGenerator::generateFileUrl('assets/script/URLGenerator.js') ?>"></script>
		<script src="<?= URLGenerator::generateFileUrl('assets/script/AlertManager.js') ?>"></script>
		<script src="<?= URLGenerator::generateFileUrl('client-components/dist/index.js') ?>" type="module"></script>
		<?= HeadManager::getHeadText() ?>
		<?= StyleManager::getStylesText() ?>
	</head>
	<body>
		<?php render($app->mainFrameComponents); ?>
	</body>
	<?= ScriptManager::getScriptText() ?>
</html>