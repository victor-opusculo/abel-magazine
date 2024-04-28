<?php
namespace VictorOpusculo\AbelMagazine\App\Reviewer\Review\TokenStr;

use Exception;
use Ramsey\Uuid\Uuid;
use VictorOpusculo\AbelMagazine\App\Admin\Panel\Magazines\MagazineId\Editions\EditionId\Articles;
use VictorOpusculo\AbelMagazine\Lib\Helpers\LogEngine;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Assessors\AssessorEvaluationToken;
use VictorOpusculo\AbelMagazine\Lib\Model\Assessors\AssessorOpinion;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Article;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\ArticleStatus;
use VictorOpusculo\AbelMagazine\Lib\Model\Settings\NotifyAuthorArticleApproved;
use VictorOpusculo\MyOrm\Option;
use VictorOpusculo\PComp\Rpc\BaseFunctionsClass;

final class Functions extends BaseFunctionsClass
{
    protected $tokenStr;

    public function saveOpinion(array $data) : array
    {
        $conn = Connection::get();
        try
        {
            if (!Uuid::isValid($this->tokenStr))
                throw new Exception(I18n::get('exceptions.invalidToken'));

            $token = (new AssessorEvaluationToken([ 'token' => $this->tokenStr ]))
            ->getSingleFromToken($conn)
            ->fetchArticle($conn);

            $opinion = (new AssessorOpinion([ 'article_id' => $token->article_id->unwrap() ]))
            ->fillPropertiesFromFormInput($data);

            $result = $opinion->save($conn);
            if ($result['newId'])
            {
                $token->delete($conn);

                if ($token->existsForArticle($conn))
                {
                    $token->article->status = Option::some(ArticleStatus::EvaluationInProgress2->value);
                    $token->article->is_approved = Option::some(0);
                }
                else if ($opinion->isArticleApproved($conn))
                {
                    $token->article->status = Option::some(ArticleStatus::Approved->value);
                    $token->article->is_approved = Option::some(0);

                    try { $token->article->fetchSubmitter($conn); (new NotifyAuthorArticleApproved)->getSingle($conn)->sendEmail($token->article, $token->article->submitter); }
                    catch (Exception) {}
                }
                else
                {
                    $token->article->status = Option::some(ArticleStatus::Disapproved->value);
                    $token->article->is_approved = Option::some(0);
                }

                $token->article->save($conn);

                LogEngine::writeLog("Parecer de avaliador enviado! ID: $result[newId]");
                return [ 'success' => I18n::get('functions.opinionCreateSuccess'), 'newId' => $result['newId'] ];
            }
            else
            {
                LogEngine::writeErrorLog("Erro ao salvar parecer de avaliador. ");
                return [ 'error' => I18n::get('functions.opinionCreateError') ];
            }
        }
        catch (Exception $e)
        {
            LogEngine::writeErrorLog($e->getMessage());
            return [ 'error' => $e->getMessage() ];
        }
    }
}