<?php
namespace VictorOpusculo\AbelMagazine\App\Submitter\Panel\Articles\ArticleId;

use Exception;
use VictorOpusculo\AbelMagazine\Lib\Helpers\LogEngine;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Assessors\AssessorEvaluationToken;
use VictorOpusculo\AbelMagazine\Lib\Model\Assessors\AssessorOpinion;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Article;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\ArticleStatus;
use VictorOpusculo\MyOrm\Option;
use VictorOpusculo\PComp\Rpc\BaseFunctionsClass;
use VictorOpusculo\PComp\Rpc\FormDataBody;
use VictorOpusculo\PComp\Rpc\IgnoreMethod;
use VictorOpusculo\PComp\Rpc\ReturnsContentType;

require_once __DIR__ . '/../../../../../lib/Middlewares/AuthorLoginCheck.php';

final class Functions extends BaseFunctionsClass
{
    #[IgnoreMethod]
    public function __construct($params)
    {
        parent::__construct($params);
        $this->middlewares[] = '\VictorOpusculo\AbelMagazine\Lib\Middlewares\authorLoginCheck';
    }

    protected $articleId;

    #[FormDataBody]
    public function edit(array $post, array $files) : array
    {
        $conn = Connection::get();
        try
        {
            if (!Connection::isId($this->articleId))
                throw new Exception(I18n::get('exceptions.invalidId'));

            $article = (new Article([ 'id' => $this->articleId, 'submitter_id' => $_SESSION['user_id'] ]))
            ->getSingleFromSubmitter($conn)
            ->fillPropertiesFromFormInput($post, $files);

            $evTokensExist = (new AssessorEvaluationToken([ 'article_id' => $this->articleId ]))->existsForArticle($conn);
            $asOpinionsExist = (new AssessorOpinion([ 'article_id' => $this->articleId ]))->existsForArticle($conn);

            if ($evTokensExist || $asOpinionsExist)
                throw new Exception(I18n::get('pages.cannotEditArticle'));

            $article->is_approved = Option::some(0);
            $article->status = Option::some(ArticleStatus::EvaluationInProgress->value);
            $article->submitter_id = Option::some($_SESSION['user_id']);

            $result = $article->save($conn);
            if ($result['affectedRows'] > 0)
            {
                LogEngine::writeLog("Artigo sem identificação editado. ID: {$article->id->unwrapOr(0)}");
                return [ 'success' => I18n::get('functions.articleSubmissionSuccess') ];
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

    #[FormDataBody]
    public function uploadFinal(array $post, array $files) : array
    {
        $conn = Connection::get();
        try
        {
            $article = (new Article([ 'id' => $this->articleId, 'submitter_id' => $_SESSION['user_id'] ?? 0 ]))->getSingleFromSubmitter($conn);

            if ($article->idded_file_extension->unwrapOr(false))
                throw new Exception(I18n::get('exceptions.finalArticleAlreadyUploaded'));

            $article->fillPropertiesFromFormInput($post, $files);

            $result = $article->save($conn);
            if ($result['affectedRows'] > 0)
            {
                LogEngine::writeLog("Artigo com identificação salvo. ID: {$article->id->unwrapOr(0)}");
                return [ 'success' => I18n::get('functions.articleSubmissionSuccess') ];
            }
            else
            {
                LogEngine::writeErrorLog("Erro ao salvar artigo com identificação!");
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