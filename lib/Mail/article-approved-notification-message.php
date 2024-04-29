<h2 style="font-size: 1.3em;"><?php

use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
use VictorOpusculo\AbelMagazine\Lib\Helpers\URLGenerator;

?>Olá, <?php echo Data::hsc($submitter->full_name->unwrapOr('autor(a)')) ?>!</h2>
<p>Seu artigo "<?php echo Data::hsc($article->title->unwrap()) ?>" foi aprovado por nossos avaliadores. Leia o feedback em seu painel de autor, pelo link abaixo:</p>
<span style="font-weight: bold;
			font-size: 1em;
			display: block;
			padding: 25px;
			margin: 10px;
			background-color: #eeeeee;
			text-align: center;">
			<a href="<?php echo URLGenerator::getHttpProtocolName() . "://" . $_SERVER["HTTP_HOST"] . URLGenerator::generatePageUrl("/submitter/panel/articles/{$article->id->unwrapOr(0)}"); ?>">
				<?php echo URLGenerator::getHttpProtocolName() . "://" . $_SERVER["HTTP_HOST"] . URLGenerator::generatePageUrl("/submitter/panel/articles/{$article->id->unwrapOr(0)}"); ?>
			</a>
		</span>