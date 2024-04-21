<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Articles\ArticleId\EvaluationTokens;

use Exception;
use VictorOpusculo\AbelMagazine\Components\Data\DataGrid;
use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
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

final class Home extends Component
{
    protected function setUp()
    {
        HeadManager::$title = I18n::get("pages.existentEvTokens");
        $conn = Connection::get();
        try
        {
            if (!Connection::isId($this->articleId))
                throw new Exception(I18n::get('exceptions.invalidId'));

            $tokens = (new AssessorEvaluationToken([ 'article_id' => $this->articleId ]))->getAllFromArticle($conn);
            $this->tokens = array_map(fn(AssessorEvaluationToken $t) =>
            [
                'id' => $t->id->unwrapOr(0),
                I18n::get('pages.token') => $t->token->unwrapOr(''),
                I18n::get('forms.fullName') => $t->assessor_name->unwrapOr(''),
                I18n::get('forms.email') => $t->assessor_email->unwrapOr('')
            ], $tokens);
        }
        catch (Exception $e)
        {
            Context::getRef('page_messages')[] = $e->getMessage();
        }
    }

    protected $articleId;

    private array $tokens = [];

    protected function markup(): Component|array|null
    {
        return component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text(I18n::get('pages.existentEvTokens'))),

            tag('div', class: 'my-2', children:
                tag('a', class: 'btn', href: URLGenerator::generatePageUrl("/admin/panel/articles/{$this->articleId}/evaluation_tokens/create"), children: text(I18n::get('pages.createEvTokens')))
            ),

            component(DataGrid::class,
                dataRows: $this->tokens,
                deleteButtonURL: URLGenerator::generatePageUrl("/admin/panel/articles/{$this->articleId}/evaluation_tokens/{param}/delete"),
                rudButtonsFunctionParamName: 'id',
                columnsToHide: ['id']
            )
        ]);
    }
}