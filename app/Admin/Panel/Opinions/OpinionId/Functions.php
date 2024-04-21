<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Opinions\OpinionId;

use Exception;
use VictorOpusculo\AbelMagazine\Lib\Helpers\LogEngine;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Assessors\AssessorOpinion;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
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

    protected $opinionId;

    public function del(array $data) : array
    {
        $conn = Connection::get();
        try
        {
            $opinion = (new AssessorOpinion([ 'id' => $this->opinionId ]))->getSingle($conn);
            $result = $opinion->delete($conn);

            if ($result['affectedRows'] > 0)
            {
                LogEngine::writeLog("Parecer de avaliador excluído! ");
                return [ 'success' => I18n::get('functions.opinionDeleteSuccess') ];
            }
            else
            {
                LogEngine::writeErrorLog("Erro ao excluir o parecer! ID: {$this->opinionId}");
                return [ 'error' => I18n::get('functions.opinionDeleteError') ];
            }
        }
        catch (Exception $e)
        {
            LogEngine::writeErrorLog($e->getMessage());
            return [ 'error' => $e->getMessage() ];
        }
    }
}