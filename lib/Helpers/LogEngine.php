<?php
namespace VictorOpusculo\AbelMagazine\Lib\Helpers;

define('LOGS_PATH', __DIR__ . "/../../log");

final class LogEngine
{
	public static function writeLog($actionMessage)
	{
		if (!empty($actionMessage))
		{
			$userEmail = match ($_SESSION['user_type'] ?? '')
			{
				UserTypes::administrator => "Admin: " . ($_SESSION['user_email'] ?? "Admin: Anônimo") . ". ID: " . ($_SESSION['user_id'] ?? 0),
				UserTypes::author => "Autor: " . ($_SESSION['user_email'] ?? "Autor: Anônimo") . ". ID: " . ($_SESSION['user_id'] ?? 0),
				default => 'Desconhecido'
			};
		
			$logData =
			[
				date("d/m/Y H:i:s"),
				"IP: " . $_SERVER['REMOTE_ADDR'],
				"Usuário: " . $userEmail,
				$actionMessage
			];
			
			$logStringEntry = implode(" | ", $logData) . PHP_EOL;
			
			file_put_contents(LOGS_PATH . "/abel-magazine_" . date("M-Y") . ".log", $logStringEntry, FILE_APPEND);
		}
	}

	public static function writeErrorLog($actionMessage)
	{
		if (!empty($actionMessage))
			self::writeLog("*** ERRO *** " . $actionMessage);
	}

}