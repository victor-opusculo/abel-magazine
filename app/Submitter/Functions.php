<?php
namespace VictorOpusculo\AbelMagazine\App\Submitter;

use Exception;
use VictorOpusculo\AbelMagazine\Lib\Helpers\LogEngine;
use VictorOpusculo\AbelMagazine\Lib\Helpers\UserTypes;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Submitters\SubmitterOtp;
use VictorOpusculo\AbelMagazine\Lib\Model\Submitters\Submitter;
use VictorOpusculo\PComp\Rpc\BaseFunctionsClass;
use VictorOpusculo\PComp\Rpc\ReturnsContentType;

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
            $_SESSION['user_email'] = $submitter->email->unwrapOr('n@d');
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
            unset($_SESSION['user_email']);
            unset($_SESSION['user_timezone']);
            session_unset();
            session_destroy();
        }

        return [ 'success' => I18n::get('functions.logoutSuccess') ];
    }

    public function createOtp(array $data) : array
    {
        $conn = Connection::get();
        try
        {
            [ 'email' => $email ] = $data;

            $submitter = (new Submitter([ 'email' => $email ]))
            ->setCryptKey(Connection::getCryptoKey())
            ->getSingleFromEmail($conn);

            [ $otpObj, $otpStr ] = SubmitterOtp::createNow($submitter->id->unwrap());
            $otpObj->clearAllOtpsFromSubmitter($conn);
            $result = $otpObj->save($conn);

            if ($result['newId'])
            {
                LogEngine::writeLog("OTP de autor criada! ID: $result[newId]");
                SubmitterOtp::sendEmail($otpStr, $submitter->email->unwrap(), $submitter->full_name->unwrap());
                return [ 'success' => I18n::get('functions.otpCreated'), 'otpId' => $result['newId'] ];
            }
            else
            {
                LogEngine::writeErrorLog("Ao gerar OTP para recuperação de senha de autor.");
                return [ 'error' => I18n::get('functions.otpCreateError') ];
            }
        }
        catch (Exception $e)
        {
            LogEngine::writeErrorLog($e->getMessage());
            return [ 'error' => $e->getMessage() ];
        }
    }

    public function changePassword(array $data) : array
    {
        $conn = Connection::get();
        try
        {
            [ 'otpId' => $otpId, 'givenOtp' => $givenOtp, 'newPassword' => $newPassword ] = $data;

            $otp = (new SubmitterOtp([ 'id' => $otpId ]))->getSingle($conn);

            $submitter = (new Submitter([ 'id' => $otp->submitter_id->unwrap() ]))
            ->setCryptKey(Connection::getCryptoKey())
            ->getSingle($conn)
            ->setCryptKey(Connection::getCryptoKey());

            if (!$otp->verifyDatetime())
                return [ 'error' => I18n::get('exceptions.expiredOtp'), 'reset' => true ];

            if (!$otp->verifyOtp($givenOtp))
                return [ 'error' => I18n::get('exceptions.wrongOtp'), 'reset' => false ];

            if (!$newPassword || mb_strlen($newPassword) < 5)
                return [ 'error' => I18n::get('exceptions.passwordNotBlankMin5Chars'), 'reset' => false ];

            $submitter->hashPassword($newPassword);
            $result = $submitter->save($conn);

            if ($result['affectedRows'] > 0)
            {
                $otp->delete($conn);
                LogEngine::writeLog("Senha de autor alterada pela recuperação de acesso perdido. Autor ID: {$submitter->id->unwrapOr(0)}");
                return [ 'success' => I18n::get('functions.passwordChangedSuccess') ];
            }
            else
            {
                LogEngine::writeErrorLog("Ao alterar senha de autor pela recuperação de acesso perdido. Autor ID: {$submitter->id->unwrapOr(0)}");
                return [ 'error' => I18n::get('functions.passwordChangedError'), 'reset' => true ];
            }
        }
        catch (Exception $e)
        {
            LogEngine::writeErrorLog($e->getMessage());
            return [ 'error' => $e->getMessage(), 'reset' => true ];
        }
    }
}