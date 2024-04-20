<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Articles\ArticleId;

use Exception;
use VictorOpusculo\AbelMagazine\Components\Data\DateTimeTranslator;
use VictorOpusculo\AbelMagazine\Components\Label;
use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
use VictorOpusculo\AbelMagazine\Lib\Helpers\URLGenerator;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Article;
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
        HeadManager::$title = I18n::get('pages.deleteArticle');
        $conn = Connection::get();
        try
        {
            $this->article = (new Article([ 'id' => $this->articleId ]))
            ->getSingle($conn)
            ->fetchEdition($conn)
            ->fetchSubmitter($conn);
        }
        catch (Exception $e)
        {
            Context::getRef('page_messages')[] = $e->getMessage();
        }
    }

    protected $articleId;
    private ?Article $article = null;

    protected function markup(): Component|array|null
    {
        return isset($this->article) ? component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text(I18n::get('pages.deleteArticle'))),

            tag('delete-entity-form',
                delete_function_route_url: URLGenerator::generateFunctionUrl("/admin/panel/articles/{$this->article->id->unwrapOr(0)}"),
                go_back_to_url: "/admin/panel/magazines/{$this->article->edition->magazine_id->unwrapOr(0)}/editions/{$this->article->edition_id->unwrapOr(0)}",
                langJson: Data::hscq(I18n::getFormsTranslationsAsJson()),
                children:
                [
                    component(Label::class, labelBold: true, label: I18n::get('forms.id'), children: text($this->article->id->unwrapOr(0))),
                    component(Label::class, labelBold: true, label: I18n::get('forms.title'), children: text($this->article->title->unwrapOr(''))),
                    component(Label::class, labelBold: true, label: I18n::get('pages.edition'), children: text($this->article->edition->title->unwrapOr(''))),
                    component(Label::class, labelBold: true, label: I18n::get('pages.submitterAuthor'), children: text($this->article->submitter->full_name->unwrapOr(''))),
                    component(Label::class, labelBold: true, label: I18n::get('forms.submissionDate'), children: 
                        component(DateTimeTranslator::class, utcDateTime: $this->article->submission_datetime->unwrapOr(null))
                    ),
                ]
            )
        ])
        : null;
    }
}