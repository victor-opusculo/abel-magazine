<h2 style="font-size: 1.3em;"><?php

use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;

 echo mb_ereg_replace('{name}', $submitterName, I18n::get('mail.recoverLoginGreeting')); ?></h2>
<p><?php echo I18n::get('mail.recoverLoginMessage') ?></p>
<span style="font-weight: bold;
			font-size: 2em;
			display: block;
			padding: 25px;
			margin: 10px;
			background-color: #eeeeee;
			text-align: center;"><?php echo $oneTimePassword; ?></span>