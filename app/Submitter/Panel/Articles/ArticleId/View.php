<?php
namespace VictorOpusculo\AbelMagazine\App\Submitter\Panel\Articles\ArticleId;

use carry0987\I18n\Language\LanguageLoader;
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
        HeadManager::$title = I18n::get("pages.viewArticle");

        $conn = Connection::get();
        try
        {
            if (!Connection::isId($this->articleId))
                throw new Exception(I18n::get('exceptions.invalidId'));

            $article = (new Article([ 'id' => $this->articleId, 'submitter_id' => $_SESSION['user_id'] ?? 0 ]))->getSingleFromSubmitter($conn);
            $this->article = $article;
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

            component(Label::class, labelBold: true, label: I18n::get('forms.title'), children: text($this->article->title->unwrapOr(''))),
            component(Label::class, labelBold: true, label: I18n::get('forms.authors'), children: text(implode(', ', json_decode($this->article->authors->unwrapOr('[]') ?? '[]')))),
            component(Label::class, labelBold: true, label: I18n::get('forms.resume'), lineBreak: true, children:
                tag('div', class: 'whitespace-pre-line', children: text($this->article->resume->unwrapOr('')))    
            ),
            component(Label::class, labelBold: true, label: I18n::get('forms.keywords'), children: text($this->article->keywords->unwrapOr(''))),
            component(Label::class, labelBold: true, label: I18n::get('forms.status'), children: text(ArticleStatus::translate($this->article->status->unwrapOr('')))),
            component(Label::class, labelBold: true, label: I18n::get('forms.submissionDate'), children: 
                component(DateTimeTranslator::class, utcDateTime: $this->article->submission_datetime->unwrapOr('now'))    
            ),
            component(Label::class, labelBold: true, label: I18n::get('forms.language'), children: text(I18n::getAlias($this->article->language->unwrapOr('')))),
            component(Label::class, labelBold: true, label: I18n::get('forms.file'), children:
                tag('a', class: 'btn', href: URLGenerator::generateFileUrl($this->article->notIddedFilePathFromBaseDir()), children: text(I18n::get('forms.download')))    
            ),
            component(Label::class, labelBold: true, label: I18n::get('forms.iddedFile'), children:
                $this->article->idded_file_extension->unwrapOr(false)
                ?   tag('a', class: 'btn', href: URLGenerator::generateFileUrl($this->article->iddedFilePathFromBaseDir()), children: text(I18n::get('forms.download'))) 
                :   text('-')   
            ),

            component(ConvenienceLinks::class, editUrl: URLGenerator::generatePageUrl("/submitter/panel/articles/{$this->article->id->unwrapOr(0)}/edit"))
        ])
        : null;
    }
}