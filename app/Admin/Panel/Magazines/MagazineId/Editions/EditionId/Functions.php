<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Magazines\MagazineId\Editions\EditionId;

use Exception;
use VictorOpusculo\AbelMagazine\Lib\Helpers\LogEngine;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Edition;
use VictorOpusculo\PComp\Rpc\BaseFunctionsClass;
use VictorOpusculo\PComp\Rpc\IgnoreMethod;

require_once __DIR__ . '/../../../../../../../lib/Middlewares/AdminLoginCheck.php';

final class Functions extends BaseFunctionsClass
{
    #[IgnoreMethod]
    public function __construct(?array $params = [])
    {
        parent::__construct($params);
        $this->middlewares[] = '\VictorOpusculo\AbelMagazine\Lib\Middlewares\adminLoginCheck';
    }

    protected $magazineId, $editionId;

    public function edit(array $data) : array
    {

        $conn = Connection::get();
        try
        {
            if (!Connection::isId($this->editionId))
                throw new Exception(I18n::get('exceptions.invalidId'));

            $edition = (new Edition([ 'id' => $this->editionId ]))
            ->getSingle($conn)
            ->fillPropertiesFromFormInput($data);

            $result = $edition->save($conn);
            if ($result['affectedRows'] > 0)
            {
                LogEngine::writeLog("Edição editada! ID: {$edition->id->unwrapOr(0)}, Revista ID: {$this->magazineId}");
                return [ 'success' => I18n::get('functions.editionEditedSuccess') ];
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
            if (!Connection::isId($this->editionId))
                throw new Exception(I18n::get('exceptions.invalidId'));

            $edition = (new Edition([ 'id' => $this->editionId ]))
            ->getSingle($conn)
            ->fillPropertiesFromFormInput($data);

            $result = $edition->softDelete($conn);
            if ($result['affectedRows'] > 0)
            {
                LogEngine::writeLog("Edição marcada como excluída! ID: {$edition->id->unwrapOr(0)}, Revista ID: {$this->magazineId}");
                return [ 'success' => I18n::get('functions.editionDeletedSuccess') ];
            }
            else
            {
                LogEngine::writeErrorLog("Erro ao marcar exclusão de edição. ID: {$edition->id->unwrapOr(0)}, Revista ID: {$this->magazineId}");
                return [ 'error' => I18n::get('functions.editionDeletedError') ];
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
            if (!Connection::isId($this->editionId))
                throw new Exception(I18n::get('exceptions.invalidId'));

            $edition = new Edition();
            $edition->includeSoftDeleted = true;
            $edition->id = $this->editionId;
            $edition = $edition->getSingle($conn);
            $edition->includeSoftDeleted = true;

            $result = $edition->softRecover($conn);
            if ($result['affectedRows'] > 0)
            {
                LogEngine::writeLog("Edição restaurada! ID: {$edition->id->unwrapOr(0)}, Revista ID: {$this->magazineId}");
                return [ 'success' => I18n::get('functions.editionRestoreSuccess') ];
            }
            else
            {
                LogEngine::writeErrorLog("Erro ao restaurar edição. ID: {$edition->id->unwrapOr(0)}, Revista ID: {$this->magazineId}");
                return [ 'error' => I18n::get('functions.editionRestoreError') ];
            }
        }
        catch (Exception $e)
        {
            LogEngine::writeErrorLog($e->getMessage());
            return [ 'error' => $e->getMessage() ];
        }
    }
}