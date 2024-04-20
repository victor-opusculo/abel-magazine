<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Articles\ArticleId;

use Exception;
use VictorOpusculo\AbelMagazine\Components\Data\DateTimeTranslator;
use VictorOpusculo\AbelMagazine\Components\Label;
use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Components\Panels\ConvenienceLinks;
use VictorOpusculo\AbelMagazine\Lib\Helpers\URLGenerator;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Article;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\ArticleStatus;
use VictorOpusculo\PComp\Component;
use VictorOpusculo\PComp\Context;
use VictorOpusculo\PComp\HeadManager;

use function VictorOpusculo\PComp\Prelude\component;
use function VictorOpusculo\PComp\Prelude\tag;
use function VictorOpusculo\PComp\Prelude\text;

final class View extends Component
{
    protected function setUp()
    {
        HeadManager::$title = I18n::get('pages.viewArticle');
        $conn = Connection::get();
        try
        {
            if (!Connection::isId($this->articleId))
                throw new Exception(I18n::get('exceptions.invalidId'));

            $this->article = (new Article([ 'id' => $this->articleId ]))
            ->getSingle($conn)
            ->fetchSubmitter($conn)
            ->fetchEdition($conn);
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
            tag('h1', children: text(I18n::get('pages.viewArticle'))),

            component(Label::class, labelBold: true, label: I18n::get('pages.edition'), children: 
                tag('a', class: 'link', href: URLGenerator::generatePageUrl("/admin/panel/magazines/{$this->article->edition->magazine_id->unwrapOr(0)}/editions/{$this->article->edition->id->unwrapOr(0)}"), children:
                    text($this->article->edition->title->unwrapOr(''))
                )
            ),
            component(Label::class, labelBold: true, label: I18n::get('forms.id'), children: text($this->article->id->unwrapOr(0))),
            component(Label::class, labelBold: true, label: I18n::get('pages.submitterAuthor'), children: 
                tag('a', class: 'link', href: URLGenerator::generatePageUrl("/admin/panel/submitters/{$this->article->submitter->id->unwrapOr(0)}"), children:
                    text($this->article->submitter->full_name->unwrapOr(''))
                )    
            ),
            component(Label::class, labelBold: true, label: I18n::get('forms.title'), children: text($this->article->title->unwrapOr(''))),
            component(Label::class, labelBold: true, label: I18n::get('forms.resume'), lineBreak: true, children: 
                tag('div', class: 'whitespace-pre-line', children:
                    text($this->article->resume->unwrapOr(''))                
                )
            ),
            component(Label::class, labelBold: true, label: I18n::get('forms.keywords'), children: text($this->article->keywords->unwrapOr(''))),
            component(Label::class, labelBold: true, label: I18n::get('forms.language'), children: text(I18n::getAlias($this->article->language->unwrapOr('')))),

            component(Label::class, labelBold: true, label: I18n::get('forms.status'), children: text(ArticleStatus::translate($this->article->status->unwrapOr('')))),
            component(Label::class, labelBold: true, label: I18n::get('forms.submissionDate'), children: 
                component(DateTimeTranslator::class, utcDateTime: $this->article->submission_datetime->unwrapOr(null))
            ),

            component(Label::class, labelBold: true, label: I18n::get('forms.file'), children: 
                tag('a', class: 'btn', href: URLGenerator::generateFileUrl($this->article->notIddedFilePathFromBaseDir()), children: text(I18n::get('pages.view')))
            ),
            component(Label::class, labelBold: true, label: I18n::get('forms.iddedFile'), children: 
                $this->article->idded_file_extension->unwrapOr(null)
                ?   tag('a', class: 'btn', href: URLGenerator::generateFileUrl($this->article->iddedFilePathFromBaseDir()), children: text(I18n::get('pages.view')))
                :   text('-')
            ),

            component(ConvenienceLinks::class,
                editUrl: URLGenerator::generatePageUrl("/admin/panel/articles/{$this->article->id->unwrapOr(0)}/edit"),
                deleteUrl: URLGenerator::generatePageUrl("/admin/panel/articles/{$this->article->id->unwrapOr(0)}/delete")
            )

        ])
        : null;
    }
}