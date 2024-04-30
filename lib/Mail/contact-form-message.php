<h2 style="font-size: 1.3em;"><?php

use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;

?>Mensagem via formulário de contato</h2>
<p>Remetente: <?php echo Data::hsc($senderName); ?> </p>
<p>E-mail: <?php echo Data::hsc($senderEmail); ?> </p>
<p>Telefone: <?php echo Data::hsc($senderTelephone); ?> </p>
<p>Mensagem: </p>
<span style="font-weight: bold;
			font-size: 1em;
			display: block;
			padding: 25px;
			margin: 10px;
			background-color: #eeeeee;
			text-align: left;">
			<p><?php echo nl2br(Data::hsc($senderMessage)); ?></p>
		</span>