<h2 style="font-size: 1.3em;"><?php

use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
use VictorOpusculo\AbelMagazine\Lib\Helpers\URLGenerator;

?>Olá!</h2>
<p>A versão com identificação de autores do artigo "<?php echo Data::hsc($article->title->unwrap()) ?>" foi submetida pelo portal de revistas. Veja no link abaixo:</p>
<span style="font-weight: bold;
			font-size: 1em;
			display: block;
			padding: 25px;
			margin: 10px;
			background-color: #eeeeee;
			text-align: center;">
			<a href="<?php echo URLGenerator::getHttpProtocolName() . "://" . $_SERVER["HTTP_HOST"] . URLGenerator::generatePageUrl("/admin/panel/articles/{$article->id->unwrapOr(0)}"); ?>">
				<?php echo URLGenerator::getHttpProtocolName() . "://" . $_SERVER["HTTP_HOST"] . URLGenerator::generatePageUrl("/admin/panel/articles/{$article->id->unwrapOr(0)}"); ?>
			</a>
		</span>