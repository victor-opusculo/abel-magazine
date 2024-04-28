<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel;

use Exception;
use VictorOpusculo\AbelMagazine\Lib\Helpers\LogEngine;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Administrators\Administrator;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\PComp\Rpc\BaseFunctionsClass;
use VictorOpusculo\PComp\Rpc\IgnoreMethod;

require_once __DIR__ . '/../../../lib/Middlewares/AdminLoginCheck.php';

final class Functions extends BaseFunctionsClass
{
    #[IgnoreMethod]
    public function __construct($params)
    {
        parent::__construct($params);
        $this->middlewares[] = '\VictorOpusculo\AbelMagazine\Lib\Middlewares\adminLoginCheck';
    }

    public function editProfile(array $data) : array
    {
        $conn = Connection::get();
        try
        {
            $admin = (new Administrator([ 'id' => $_SESSION['user_id'] ?? 0 ]))
            ->getSingle($conn)
            ->fillPropertiesFromFormInput($data);

            if ($admin->existsEmailOnAnotherAdmin($conn))
                throw new Exception(I18n::get('exceptions.emailAlreadyInUse'));

            if ($data['administrators:currpassword'])
            {
                if ($admin->verifyPasswords($data['administrators:currpassword']))
                {
                    if (mb_strlen($data['administrators:password']) >= 5)
                        $admin->hashPassword($data['administrators:password']);
                    else
                        throw new Exception(I18n::get('exceptions.newPasswordTooShort'));
                }
                else
                    throw new Exception(I18n::get('exceptions.incorrectCurrentPassword'));
            }

            $result = $admin->save($conn);
            if ($result['affectedRows'] > 0)
            {
                LogEngine::writeLog("Perfil de administrador editado! ID: {$admin->id->unwrapOr(0)}");
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