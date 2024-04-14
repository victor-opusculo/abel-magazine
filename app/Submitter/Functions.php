<?php
namespace VictorOpusculo\AbelMagazine\App\Submitter;

use Exception;
use VictorOpusculo\AbelMagazine\Lib\Helpers\LogEngine;
use VictorOpusculo\AbelMagazine\Lib\Helpers\UserTypes;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Submitters\Submitter;
use VictorOpusculo\PComp\Rpc\BaseFunctionsClass;

final class Functions extends BaseFunctionsClass
{
    public function register(array $data) : array
    {
        $conn = Connection::get();
        try
        {
            $submitter = (new Submitter())
            ->setCryptKey(Connection::getCryptoKey())
            ->fillPropertiesFromFormInput($data);

            if ($submitter->existsEmailWithAnotherId($conn))
                throw new Exception(I18n::get('exceptions.emailAlreadyRegistered'));

            if (!$data['submitters:password'] || mb_strlen($data['submitters:password']) < 5)
                throw new Exception(I18n::get('exceptions.passwordNotBlankMin5Chars'));

            $submitter->hashPassword($data['submitters:password']);

            $result = $submitter->save($conn);
            if ($result['newId'])
            {
                LogEngine::writeLog("Novo autor registrado. ID: $result[newId]");
                return [ 'success' => I18n::get('functions.submitterRegistrationSuccess') ];
            }
            else
            {
                LogEngine::writeErrorLog("Erro ao registrar autor.");
                return [ 'error' => I18n::get('functions.submitterRegistrationError') ];
            }
        }
        catch (Exception $e)
        {
            LogEngine::writeErrorLog($e->getMessage());
            return [ 'error' => $e->getMessage() ];
        }
    }

    public function login(array $data) : array
    {
        $conn = Connection::get();
        try
        {
            $submitter = (new Submitter([ 'email' => $data['email'] ?? '***' ]))
            ->setCryptKey(Connection::getCryptoKey())
            ->getSingleFromEmail($conn);

            if (!$submitter->verifyPassword($data['password']))
                throw new Exception(I18n::get('functions.invalidPassword'));

            session_name('abel_magazine_author_user');
            session_start();
            $_SESSION['user_id'] = $submitter->id->unwrap();
            $_SESSION['user_type'] = UserTypes::author;
            $_SESSION['user_timezone'] = $submitter->timezone->unwrapOr('America/Sao_Paulo');

            LogEngine::writeLog("Log-in de autor efetuado. ID: {$submitter->id->unwrapOr(0)}");
            return [ 'success' => mb_ereg_replace('{name}', $submitter->full_name->unwrap(), I18n::get('functions.greetings')) ];
        }
        catch (Exception $e)
        {
            LogEngine::writeErrorLog($e->getMessage());
            return [ 'error' => $e->getMessage() ];
        }
    }

    public function logout(array $data) : array
    {
        session_name('abel_magazine_author_user');
        session_start();

        if (isset($_SESSION))
        {
            LogEngine::writeLog("Autor deslogado. ID: $_SESSION[user_id]");
            unset($_SESSION['user_id']);
            unset($_SESSION['user_type']);
            unset($_SESSION['user_timezone']);
            session_unset();
            session_destroy();
        }

        return [ 'success' => I18n::get('functions.logoutSuccess') ];
    }
}