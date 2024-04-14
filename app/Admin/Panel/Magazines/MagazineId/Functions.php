<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Magazines\MagazineId;

use Exception;
use VictorOpusculo\AbelMagazine\Lib\Helpers\LogEngine;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Magazine;
use VictorOpusculo\PComp\Rpc\BaseFunctionsClass;

require_once __DIR__ . '/../../../../../lib/Middlewares/AdminLoginCheck.php';

final class Functions extends BaseFunctionsClass
{
    public function __construct(?array $params = [])
    {
        parent::__construct($params);
        $this->middlewares[] = '\VictorOpusculo\AbelMagazine\Lib\Middlewares\adminLoginCheck';
    }

    protected $magazineId;

    public function edit(array $data) : array
    {
        $conn = Connection::get();
        try
        {
            if (!Connection::isId($this->magazineId))
                throw new Exception(I18n::get('exceptions.invalidId'));

            $magazine = (new Magazine([ 'id' => $this->magazineId ]))->getSingle($conn);
            $magazine->fillPropertiesFromFormInput($data);

            if ($magazine->existsStringIdentifier($conn))
                throw new Exception(I18n::get('exceptions.stringIdentifierAlreadyExists'));

            $result = $magazine->save($conn);
            if ($result['affectedRows'] > 0)
            {
                LogEngine::writeLog("Revista editada! ID: {$magazine->id->unwrapOr(0)}");
                return [ 'success' => I18n::get('functions.magazineEditedSuccess')  ];
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

    public function del(array $data) : array
    {
        $conn = Connection::get();
        try
        {
            if (!Connection::isId($this->magazineId))
                throw new Exception(I18n::get('exceptions.invalidId'));

            $magazine = (new Magazine([ 'id' => $this->magazineId ]))->getSingle($conn);

            $result = $magazine->softDelete($conn);
            if ($result['affectedRows'] > 0)
            {
                LogEngine::writeLog("Revista marcada com excluída! ID: {$magazine->id->unwrapOr(0)}");
                return [ 'success' => I18n::get('functions.magazineDeletedSuccess')  ];
            }
            else
            {
                LogEngine::writeErrorLog("Erro ao marcar exclusão de revista! ID: {$magazine->id->unwrapOr(0)}");
                return [ 'error' => I18n::get('functions.magazineDeleteError') ];
            }
        }
        catch (Exception $e)
        {
            LogEngine::writeErrorLog($e->getMessage());
            return [ 'error' => $e->getMessage() ];
        }
    }

    public function restore(array $data) : array
    {
        $conn = Connection::get();
        try
        {
            if (!Connection::isId($this->magazineId))
                throw new Exception(I18n::get('exceptions.invalidId'));

            $magazine = (new Magazine([ 'id' => $this->magazineId ]));
            $magazine->includeSoftDeleted = true;
            $magazine = $magazine->getSingle($conn);
            $magazine->includeSoftDeleted = true;

            $result = $magazine->softRecover($conn);
            if ($result['affectedRows'] > 0)
            {
                LogEngine::writeLog("Revista restaurada! ID: {$magazine->id->unwrapOr(0)}");
                return [ 'success' => I18n::get('functions.magazineRestoreSuccess')  ];
            }
            else
            {
                LogEngine::writeErrorLog("Erro ao restaurar revista! ID: {$magazine->id->unwrapOr(0)}");
                return [ 'error' => I18n::get('functions.magazineRestoreError') ];
            }
        }
        catch (Exception $e)
        {
            LogEngine::writeErrorLog($e->getMessage());
            return [ 'error' => $e->getMessage() ];
        }
    }
}