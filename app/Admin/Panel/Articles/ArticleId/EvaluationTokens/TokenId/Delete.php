<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Articles\ArticleId\EvaluationTokens\TokenId;

use Exception;
use VictorOpusculo\AbelMagazine\Components\Label;
use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
use VictorOpusculo\AbelMagazine\Lib\Helpers\URLGenerator;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Assessors\AssessorEvaluationToken;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\PComp\Component;
use VictorOpusculo\PComp\Context;
use VictorOpusculo\PComp\HeadManager;

use function VictorOpusculo\PComp\Prelude\component;
use function VictorOpusculo\PComp\Prelude\tag;
use function VictorOpusculo\PComp\Prelude\text;

final class Delete extends Component
{
    protected function setUp()
    {
        HeadManager::$title = I18n::get('pages.deleteToken');
        $conn = Connection::get();
        try
        {
            if (!Connection::isId($this->tokenId))
                throw new Exception(I18n::get('exceptions.invalidId'));

            $this->token = (new AssessorEvaluationToken([ 'id' => $this->tokenId ]))->getSingle($conn);
        }
        catch (Exception $e)
        {
            Context::getRef('page_messages')[] = $e->getMessage();
        }
    }

    protected $articleId, $tokenId;
    private ?AssessorEvaluationToken $token = null;

    protected function markup(): Component|array|null
    {
        return isset($this->token) ? component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text(I18n::get('pages.deleteToken'))),
            tag('delete-entity-form',
                delete_function_route_url: URLGenerator::generateFunctionUrl("/admin/panel/articles/{$this->articleId}/evaluation_tokens/{$this->token->id->unwrapOr(0)}"),
                go_back_to_url: "/admin/panel/articles/{$this->articleId}/evaluation_tokens/",
                langJson: Data::hscq(I18n::getFormsTranslationsAsJson()),
                children:
                [
                    component(Label::class, labelBold: true, label: I18n::get('pages.token'), children: text($this->token->token->unwrapOr(''))),
                    component(Label::class, labelBold: true, label: I18n::get('forms.reviewerName'), children: text($this->token->assessor_name->unwrapOr(''))),
                    component(Label::class, labelBold: true, label: I18n::get('forms.reviewerEmail'), children: text($this->token->assessor_email->unwrapOr('')))
                ]
            )
        ])
        : null;
    }
}