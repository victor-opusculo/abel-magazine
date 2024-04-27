<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Articles;

use Exception;
use VictorOpusculo\AbelMagazine\Lib\Helpers\LogEngine;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Settings\EmailToNotifyNewArticle;
use VictorOpusculo\MyOrm\Option;
use VictorOpusculo\PComp\Rpc\BaseFunctionsClass;
use VictorOpusculo\PComp\Rpc\IgnoreMethod;

require_once __DIR__ . '/../../../../lib/Middlewares/AdminLoginCheck.php';

final class Functions extends BaseFunctionsClass
{
    #[IgnoreMethod]
    public function __construct($params)
    {
        parent::__construct($params);
        $this->middlewares[] = '\VictorOpusculo\AbelMagazine\Lib\Middlewares\adminLoginCheck';
    }

    public function changeNotifyEmail(array $data) : array
    {
        $conn = Connection::get();
        try
        {
            [ 'email' => $email ] = $data;

            if (!$email || mb_strlen($email) < 1)
            throw new Exception(I18n::get('exceptions.invalidEmail'));

            $sett = new EmailToNotifyNewArticle();
            $sett->value = Option::some($email);

            $result = $sett->save($conn);
            if ($result['affectedRows'] > 0)
            {
                LogEngine::writeLog("E-mail de notificação de envios de artigos alterado para: $email");
                return [ 'success' => I18n::get('functions.notifyEmailChangeSuccess') ];
            }
            else
            {
                return [ 'info' => I18n::get('functions.noDataChanged') ];
            }
        }
        catch (Exception $e)
        {
            LogEngine::writeErrorLog($e->getMessage());
            return [ 'error' => $e->getMessage() ];
        }
    }
}