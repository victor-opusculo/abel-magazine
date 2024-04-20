<?php
namespace VictorOpusculo\AbelMagazine\App\Submitter\Panel;

use Exception;
use VictorOpusculo\AbelMagazine\Lib\Helpers\LogEngine;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Submitters\Submitter;
use VictorOpusculo\PComp\Rpc\BaseFunctionsClass;
use VictorOpusculo\PComp\Rpc\IgnoreMethod;

require_once __DIR__ . '/../../../lib//Middlewares/AuthorLoginCheck.php';

final class Functions extends BaseFunctionsClass
{
    #[IgnoreMethod]
    public function __construct($params)
    {
        parent::__construct($params);
        $this->middlewares[] = '\VictorOpusculo\AbelMagazine\Lib\Middlewares\authorLoginCheck';
    }

    public function editProfile(array $data) : array
    {
        $conn = Connection::get();
        try
        {
            $submitter = (new Submitter([ 'id' => $_SESSION['user_id'] ?? 0 ]))
            ->setCryptKey(Connection::getCryptoKey())
            ->getSingle($conn)
            ->setCryptKey(Connection::getCryptoKey())
            ->fillPropertiesFromFormInput($data);

            if ($data['submitters:currpassword'])
            {
                if ($submitter->verifyPassword($data['submitters:currpassword']))
                {
                    if (mb_strlen($data['submitters:password']) >= 5)
                        $submitter->hashPassword($data['submitters:password']);
                    else
                        throw new Exception(I18n::get('exceptions.newPasswordTooShort'));
                }
                else
                    throw new Exception(I18n::get('exceptions.incorrectCurrentPassword'));
            }

            $result = $submitter->save($conn);
            if ($result['affectedRows'] > 0)
            {
                LogEngine::writeLog("Perfil de autor editado! ID: {$submitter->id->unwrapOr(0)}");
                return [ 'success' => I18n::get('functions.submitterEditProfileSuccess') ];
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