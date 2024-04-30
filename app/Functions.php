<?php
namespace VictorOpusculo\AbelMagazine\App;

use Exception;
use VictorOpusculo\AbelMagazine\Lib\Helpers\LogEngine;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Settings\EmailToContact;
use VictorOpusculo\PComp\Rpc\BaseFunctionsClass;

final class Functions extends BaseFunctionsClass
{
    public function contactEmail(array $data) : array
    {
        $conn = Connection::get();
        try
        {
            [
                'full_name' => $name,
                'email' => $email,
                'telephone' => $telephone,
                'subject' => $subject,
                'message' => $message
            ] = $data;

            $sett = (new EmailToContact)->getSingle($conn);

            $sett->sendEmail($name, $email, $telephone, $subject, $message);

            return [ 'success' => I18n::get('functions.emailSentSuccess') ];
        }
        catch (Exception $e)
        {
            LogEngine::writeErrorLog($e->getMessage());
            return [ 'error' => $e->getMessage() ];
        }
    }
}