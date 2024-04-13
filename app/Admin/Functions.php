<?php
namespace VictorOpusculo\AbelMagazine\App\Admin;

use Exception;
use VictorOpusculo\AbelMagazine\Lib\Helpers\LogEngine;
use VictorOpusculo\AbelMagazine\Lib\Helpers\UserTypes;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Administrators\Administrator;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\PComp\Rpc\BaseFunctionsClass;
use VictorOpusculo\PComp\Rpc\ReturnsContentType;

final class Functions extends BaseFunctionsClass
{
    public function login(array $data) : array
    {
        $email = $data['data']['email'] ?? 'n@a';
        $password = $data['data']['password'] ?? '***';

        $conn = Connection::get();
        try
        {
            $admin = (new Administrator([ 'email' => $email ]))->getSingleByEmail($conn);

            session_name('abel_magazine_admin_user');
            if ($admin->verifyPasswords($password))
            {
                session_start();
                $_SESSION['user_type'] = UserTypes::administrator;
                $_SESSION['user_id'] = $admin->id->unwrapOr(-1);
                $_SESSION['user_email'] = $admin->email->unwrapOr('n@a');
                $_SESSION['user_timezone'] = $admin->timezone->unwrapOr('America/Sao_Paulo');

                LogEngine::writeLog("Administrador logado! ID: " . $admin->id->unwrapOr(-1));

                return [ 'success' => mb_ereg_replace('{name}', $admin->full_name->unwrapOr('***'), I18n::get('functions.greetings')) ];
            }
            else
            {
                if (isset($_SESSION))
                {
                    session_unset();
                    session_destroy();
                }
                LogEngine::writeErrorLog("Senha incorreta no login de administrador. ID: " . $admin->id->unwrapOr(-1));
                return [ 'error' => I18n::get('functions.invalidPassword') ];
            }
        }
        catch (Exception $e)
        {
            LogEngine::writeErrorLog($e->getMessage());
            return [ 'error' => $e->getMessage() ];
        }
    }

    public function logout(?array $data = null) : array
    {
        session_name('abel_magazine_admin_user');
        session_start();
        if (isset($_SESSION))
        {
            LogEngine::writeLog("Administrador deslogado! ID: " . $_SESSION['user_id'] ?? -1);
            unset($_SESSION['user_type']);
            unset($_SESSION['user_id']);
            unset($_SESSION['user_email']);
            unset($_SESSION['user_timezone']);
            session_unset();
            session_destroy();
        }

        return [ 'success' => I18n::get('functions.logoutSuccess') ];
    }
}