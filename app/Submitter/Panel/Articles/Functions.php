<?php
namespace VictorOpusculo\AbelMagazine\App\Submitter\Panel\Articles;

use Exception;
use VictorOpusculo\AbelMagazine\Lib\Helpers\LogEngine;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Article;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\ArticleStatus;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Edition;
use VictorOpusculo\MyOrm\Option;
use VictorOpusculo\PComp\Rpc\BaseFunctionsClass;
use VictorOpusculo\PComp\Rpc\FormDataBody;
use VictorOpusculo\PComp\Rpc\IgnoreMethod;

require_once __DIR__ . '/../../../../lib/Middlewares/AuthorLoginCheck.php';

final class Functions extends BaseFunctionsClass
{
    #[IgnoreMethod]
    public function __construct($params)
    {
        parent::__construct($params);
        $this->middlewares[] = '\VictorOpusculo\AbelMagazine\Lib\Middlewares\authorLoginCheck';
    }

    #[FormDataBody]
    public function create(array $post, array $files) : array
    {
        $conn = Connection::get();
        try
        {
            $article = (new Article)->fillPropertiesFromFormInput($post, $files);
            $article->is_approved = Option::some(0);
            $article->status = Option::some(ArticleStatus::EvaluationInProgress->value);
            $article->submitter_id = Option::some($_SESSION['user_id']);

            $result = $article->save($conn);
            if ($result['newId'])
            {
                LogEngine::writeLog("Artigo sem identificação enviado. ID: $result[newId]");
                return [ 'success' => I18n::get('functions.articleSubmissionSuccess'), 'newId' => $result['newId'] ];
            }
            else
            {
                LogEngine::writeErrorLog("Erro ao salvar artigo!");
                return [ 'error' => I18n::get('functions.articleSubmissionError') ];
            }
        }
        catch (Exception $e)
        {
            LogEngine::writeErrorLog($e->getMessage());
            return [ 'error' => $e->getMessage() ];
        }
    }
}