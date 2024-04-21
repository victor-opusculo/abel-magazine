<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Articles\ArticleId\EvaluationTokens;

use Exception;
use VictorOpusculo\AbelMagazine\Lib\Helpers\LogEngine;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Assessors\AssessorEvaluationToken;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\PComp\Rpc\BaseFunctionsClass;
use VictorOpusculo\PComp\Rpc\IgnoreMethod;

require_once __DIR__ . '/../../../../../../lib/Middlewares/AdminLoginCheck.php';

final class Functions extends BaseFunctionsClass
{
    #[IgnoreMethod]
    public function __construct($params)
    {
        parent::__construct($params);
        $this->middlewares[] = '\VictorOpusculo\AbelMagazine\Lib\Middlewares\adminLoginCheck';
    }

    protected $articleId;

    public function create(array $data) : array
    {
        $conn = Connection::get();
        try
        {
            if (!Connection::isId($this->articleId))
                throw new Exception(I18n::get("exceptions.invalidId"));

            $token = (new AssessorEvaluationToken([ 'article_id' => $this->articleId ]))
            ->fillPropertiesFromFormInput($data)
            ->generateToken();

            $result = $token->save($conn);
            if ($result['newId'])
            {
                LogEngine::writeLog("Token de avaliação gerado. ID: $result[newId]");
                return [ 'success' => I18n::get('functions.tokenCreateSuccess'), 'newId' => $result['newId'] ];
            }
            else
            {
                LogEngine::writeErrorLog("Erro ao criar token de avaliação.");
                return [ 'error' => I18n::get('functions.tokenCreateError') ];
            }
        }
        catch (Exception $e)
        {
            LogEngine::writeErrorLog($e->getMessage());
            return [ 'error' => $e->getMessage() ];
        }
    }
}