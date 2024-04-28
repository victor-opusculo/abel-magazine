<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Articles;

use Exception;
use VictorOpusculo\AbelMagazine\Lib\Helpers\LogEngine;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Settings\EmailToNotifyNewArticle;
use VictorOpusculo\AbelMagazine\Lib\Model\Settings\NotifyAdminFinalArticleUploaded;
use VictorOpusculo\AbelMagazine\Lib\Model\Settings\NotifyAuthorArticleApproved;
use VictorOpusculo\MyOrm\Option;
use VictorOpusculo\PComp\Rpc\BaseFunctionsClass;
use VictorOpusculo\PComp\Rpc\IgnoreMethod;
use VictorOpusculo\PComp\Rpc\ReturnsContentType;

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
            [ 
                'newArticleEmail' => $newArticleEmail, 
                'notifyAuthorArticleApproved' => $notifyAuthorArticleApproved,
                'notifyAdminFinalArticleUploaded' => $notifyAdminFinalArticleUploaded
            ] = $data;

            $sett = new EmailToNotifyNewArticle();
            $sett->value = Option::some($newArticleEmail);

            $sett2 = new NotifyAuthorArticleApproved();
            $sett2->value = Option::some($notifyAuthorArticleApproved);

            $sett3 = new NotifyAdminFinalArticleUploaded();
            $sett3->value = Option::some($newArticleEmail && mb_strlen($newArticleEmail) > 0 ? $notifyAdminFinalArticleUploaded : 0);

            $result = $sett->save($conn);
            $result['affectedRows'] += $sett2->save($conn)['affectedRows'];
            $result['affectedRows'] += $sett3->save($conn)['affectedRows'];

            if ($result['affectedRows'] > 0)
            {
                LogEngine::writeLog("Configurações de envio de e-mail alteradas!");
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