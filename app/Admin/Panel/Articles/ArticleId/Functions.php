<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Articles\ArticleId;

use Exception;
use VictorOpusculo\AbelMagazine\Lib\Helpers\LogEngine;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Article;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\ArticleStatus;
use VictorOpusculo\MyOrm\Option;
use VictorOpusculo\PComp\Rpc\BaseFunctionsClass;
use VictorOpusculo\PComp\Rpc\FormDataBody;
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

    protected $articleId;

    #[FormDataBody]
    public function edit(array $post, array $files) : array
    {
        $conn = Connection::get();
        try
        {
            if (!Connection::isId($this->articleId))
                throw new Exception(I18n::get('exceptions.invalidId'));

            $article = (new Article([ 'id' => $this->articleId ]))
            ->getSingle($conn)
            ->fillPropertiesFromFormInput($post, $files);

            $article->adminEdit = true;

            $result = $article->save($conn);
            if ($result['affectedRows'] > 0)
            {
                LogEngine::writeLog("Artigo editado pela administração. ID: {$article->id->unwrapOr(0)}");
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

    public function del(array $data) : array
    {
        $conn = Connection::get();
        try
        {
            if (!Connection::isId($this->articleId))
                throw new Exception(I18n::get('exceptions.invalidId'));

            $article = (new Article([ 'id' => $this->articleId ]))->getSingle($conn);
            
            $result = $article->delete($conn);
            if ($result['affectedRows'] > 0)
            {
                LogEngine::writeLog("Artigo excluído pela administração. ID: {$article->id->unwrapOr(0)}");
                return [ 'success' => I18n::get('functions.articleDeleteSuccess') ];
            }
            else
            {
                LogEngine::writeErrorLog("Erro ao excluir artigo. ID: {$article->id->unwrapOr(0)}");
                return [ 'error' => I18n::get('functions.articleDeleteError') ];
            }
        }
        catch (Exception $e)
        {
            LogEngine::writeErrorLog($e->getMessage());
            return [ 'error' => $e->getMessage() ];
        }
    }

    public function changeStatus(array $data) : array
    {
        $conn = Connection::get();
        try
        {
            if (!Connection::isId($this->articleId) || !Connection::isId($data['articleId'] ?? 0))
                throw new Exception(I18n::get('exceptions.invalidId'));

            [ 'articleId' => $id, 'toStatus' => $toStatus ] = $data;

            $article = (new Article([ 'id' => $id ]))->getSingle($conn);

            $newStatus = ArticleStatus::tryFrom($toStatus) ?? ArticleStatus::EvaluationInProgress;
            $article->status = Option::some($newStatus->value);

            if ($newStatus !== ArticleStatus::Approved && $newStatus !== ArticleStatus::ApprovedWithIddedFile)
                $article->is_approved = Option::some(0);
            else
                $article->is_approved = Option::some(1);

            $result = $article->save($conn);
            if ($result['affectedRows'])
            {
                LogEngine::writeLog("Status de artigo alterado manualmente pela administração. Artigo ID: {$article->id->unwrapOr(0)}");
                return [ 'success' => I18n::get('functions.articleStatusChangeSuccess') ];
            }
            else
            {
                LogEngine::writeErrorLog("Erro ao alterar status de artigo manualmente. Artigo ID: {$article->id->unwrapOr(0)}");
                return [ 'error' => I18n::get('functions.articleStatusChangeError') ];
            }
        }
        catch (Exception $e)
        {
            LogEngine::writeErrorLog($e->getMessage());
            return [ 'error' => $e->getMessage() ];
        }
    }
}