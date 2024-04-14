<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Magazines\MagazineId\Editions;

use Exception;
use VictorOpusculo\AbelMagazine\Lib\Helpers\LogEngine;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Edition;
use VictorOpusculo\MyOrm\Option;
use VictorOpusculo\PComp\Rpc\BaseFunctionsClass;
use VictorOpusculo\PComp\Rpc\IgnoreMethod;

require_once __DIR__ . '/../../../../../../lib/Middlewares/AdminLoginCheck.php';

final class Functions extends BaseFunctionsClass
{
    #[IgnoreMethod]
    public function __construct(?array $params = [])
    {
        parent::__construct($params);
        $this->middlewares[] = '\VictorOpusculo\AbelMagazine\Lib\Middlewares\adminLoginCheck';
    }

    protected $magazineId;

    public function create(array $data) : array
    {
        $conn = Connection::get();
        try
        {
            if (!Connection::isId($this->magazineId))
                throw new Exception(I18n::get('exceptions.invalidId'));

            $edition = new Edition();
            $edition->fillPropertiesFromFormInput($data);
            $edition->magazine_id = Option::some($this->magazineId);

            $result = $edition->save($conn);
            
            if ($result['newId'])
            {
                LogEngine::writeLog("Edição criada! ID: $result[newId], Revista ID: {$this->magazineId}");
                return [ 'success' => I18n::get('functions.editionCreatedSuccess'), 'newId' => $result['newId'] ];
            }
            else
            {
                LogEngine::writeErrorLog("Erro ao criar edição! Revista ID: {$this->magazineId}");
                return [ 'error' => I18n::get('functions.editionCreatedError') ];
            }
        }
        catch (Exception $e)
        {
            LogEngine::writeErrorLog($e->getMessage());
            return [ 'error' => $e->getMessage() ];
        }
    }
}