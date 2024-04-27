<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Pages\PageId;

use Exception;
use VictorOpusculo\AbelMagazine\Lib\Helpers\LogEngine;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Pages\Page;
use VictorOpusculo\PComp\Rpc\BaseFunctionsClass;
use VictorOpusculo\PComp\Rpc\IgnoreMethod;

require_once __DIR__ . '/../../../../../lib/Middlewares/AdminLoginCheck.php';

final class Functions extends BaseFunctionsClass
{
    #[IgnoreMethod]
    public function __construct($params)
    {
        parent::__construct($params);
        $this->middlewares[] = '\VictorOpusculo\AbelMagazine\Lib\Middlewares\adminLoginCheck';
    }

    protected $pageId;

    public function edit(array $data) : array
    {
        $conn = Connection::get();
        try
        {
            if (!Connection::isId($this->pageId))
                throw new Exception(I18n::get('exceptions.invalidId'));

            $page = (new Page([ 'id' => $this->pageId ]))
            ->getSingle($conn)
            ->fillPropertiesFromFormInput($data);

            $result = $page->save($conn);

            if ($result['affectedRows'] > 0)
            {
                LogEngine::writeLog("Página editada! ID: {$page->id->unwrapOr(0)}");
                return [ 'success' => I18n::get('functions.pageEditSuccess') ];
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
            if (!Connection::isId($this->pageId))
                throw new Exception(I18n::get('exceptions.invalidId'));

            $page = (new Page([ 'id' => $this->pageId ]))
            ->getSingle($conn);

            $result = $page->delete($conn);

            if ($result['affectedRows'] > 0)
            {
                LogEngine::writeLog("Página excluída! ID: {$page->id->unwrapOr(0)}");
                return [ 'success' => I18n::get('functions.pageDeleteSuccess') ];
            }
            else
            {
                LogEngine::writeErrorLog('Ao excluir página! ');
                return [ 'error' => I18n::get('functions.pageDeleteError') ];
            }
        }
        catch (Exception $e)
        {
            LogEngine::writeErrorLog($e->getMessage());
            return [ 'error' => $e->getMessage() ];
        }
    }
}