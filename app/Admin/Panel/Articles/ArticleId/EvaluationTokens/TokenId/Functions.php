<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Articles\ArticleId\EvaluationTokens\TokenId;

use Exception;
use VictorOpusculo\AbelMagazine\Lib\Helpers\LogEngine;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Assessors\AssessorEvaluationToken;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\PComp\Rpc\BaseFunctionsClass;
use VictorOpusculo\PComp\Rpc\IgnoreMethod;

require_once __DIR__ . '/../../../../../../../lib/Middlewares/AdminLoginCheck.php';

final class Functions extends BaseFunctionsClass
{
    #[IgnoreMethod]
    public function __construct($params)
    {
        parent::__construct($params);
        $this->middlewares[] = '\VictorOpusculo\AbelMagazine\Lib\Middlewares\adminLoginCheck';
    }

    protected $articleId, $tokenId;

    public function del(array $data) : array
    {
        $conn = Connection::get();
        try
        {
            if (!Connection::isId($this->tokenId))
                throw new Exception(I18n::get('functions.invalidId'));

            $token = (new AssessorEvaluationToken([ 'id' => $this->tokenId ]))->getSingle($conn);

            $result = $token->delete($conn);
            if ($result['affectedRows'] > 0)
            {
                LogEngine::writeLog("Token de avaliação excluído!");
                return [ 'success' => I18n::get('functions.tokenDeleteSuccess') ];
            }
            else
            {
                LogEngine::writeErrorLog("Erro ao excluir token de avaliação. ID: {$this->tokenId}");
                return [ 'error' => I18n::get('functions.tokenDeleteError') ];
            }

        }
        catch (Exception $e)
        {
            LogEngine::writeErrorLog($e->getMessage());
            return [ 'error' => $e->getMessage() ];
        }
    }
}