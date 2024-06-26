<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Magazines;

use Exception;
use VictorOpusculo\AbelMagazine\Lib\Helpers\LogEngine;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Magazine;
use VictorOpusculo\AbelMagazine\Lib\Model\Settings\DefaultMagazineId;
use VictorOpusculo\MyOrm\Option;
use VictorOpusculo\PComp\Rpc\BaseFunctionsClass;

require_once __DIR__ . '/../../../../lib/Middlewares/AdminLoginCheck.php';

final class Functions extends BaseFunctionsClass
{

    public function __construct(?array $params = [])
    {
        parent::__construct($params);
        $this->middlewares[] = '\VictorOpusculo\AbelMagazine\Lib\Middlewares\adminLoginCheck';
    }

    public function create(array $data) : array
    {
        $conn = Connection::get();
        try
        {
            $magazine = new Magazine();
            $magazine->fillPropertiesFromFormInput($data);

            if ($magazine->existsStringIdentifier($conn))
                throw new Exception(I18n::get('exceptions.stringIdentifierAlreadyExists'));

            $result = $magazine->save($conn);
            if ($result['newId'])
            {
                LogEngine::writeLog("Revista criada! ID: $result[newId]");
                return [ 'success' => I18n::get('functions.magazineCreatedSuccess'), 'newId' => $result['newId'] ];
            }
            else
            {
                LogEngine::writeErrorLog("Erro ao criar revista.");
                return [ 'error' => I18n::get('functions.magazineCreatedError') ];
            }
        }
        catch (Exception $e)
        {
            LogEngine::writeErrorLog($e->getMessage());
            return [ 'error' => $e->getMessage() ];
        }
    }

    public function setDefaultMagazine(array $data) : array
    {
        $conn = Connection::get();
        try
        {
            [ 'magazine_id' => $magId, 'remove' => $remove ] = $data;

            $sett = new DefaultMagazineId();
            if ($magId && !$remove)
                $sett->value = Option::some($magId);
            else
                $sett->value = Option::some(null);

            $result = $sett->save($conn);
            if ($result['affectedRows'] > 0)
            {
                LogEngine::writeLog("Revista padrão definida. ID: {$sett->value->unwrapOr(0)}");
                return [ 'success' => I18n::get('functions.notifyEmailChangeSuccess') ];
            }
            else
                return [ 'info' => I18n::get('functions.noDataChanged') ];
        }
        catch (Exception $e)
        {
            LogEngine::writeErrorLog($e->getMessage());
            return [ 'error' => $e->getMessage() ];
        }
    }
}