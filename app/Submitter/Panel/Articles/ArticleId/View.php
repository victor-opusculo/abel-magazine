<?php
namespace VictorOpusculo\AbelMagazine\App\Submitter\Panel\Articles\ArticleId;

use carry0987\I18n\Language\LanguageLoader;
use Exception;
use VictorOpusculo\AbelMagazine\Components\Data\DateTimeTranslator;
use VictorOpusculo\AbelMagazine\Components\Label;
use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Components\Panels\ConvenienceLinks;
use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
use VictorOpusculo\AbelMagazine\Lib\Helpers\URLGenerator;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Article;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\ArticleStatus;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Upload\IddedArticleUpload;
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
                tag('a', class: 'btn', href: URLGenerator::generateScriptUrl('/fetch_article_nid.php', [ 'id' => $this->article->id->unwrapOr(0) ]), children: text(I18n::get('forms.download')))    
            ),
            component(Label::class, labelBold: true, label: I18n::get('forms.iddedFile'), children:
                $this->article->idded_file_extension->unwrapOr(false)
                ?   tag('a', class: 'btn', href: URLGenerator::generateScriptUrl('/fetch_article.php', [ 'id' => $this->article->id->unwrapOr(0) ]), children: text(I18n::get('forms.download'))) 
                :   text('-')   
            ),

            component(Label::class, labelBold: true, label: I18n::get('pages.publicationStatus'), children:
                text($this->article->is_approved->unwrapOr(0) ? I18n::get('pages.published') : I18n::get('pages.notPublished'))
            ),

            component(Label::class, labelBold: true, label: I18n::get('pages.reviewersOpinions'), children:
                tag('a', class: 'btn', href: URLGenerator::generatePageUrl("/submitter/panel/articles/{$this->article->id->unwrapOr(0)}/reviews"), children: text(I18n::get('pages.view'))),
            ),

            $this->article->status->unwrapOr('') === ArticleStatus::Approved->value && ($this->article->idded_file_extension->unwrapOr(null) === null)
            ?   tag('fieldset', class: 'fieldset', children:
                [
                    tag('legend', children: text(I18n::get('pages.sendYourFinalArticleVersion'))),
                    tag('submitter-send-final-article', 
                        button_label: I18n::get('forms.send'), 
                        allowed_mime_types: Data::hscq(implode(',', IddedArticleUpload::ALLOWED_TYPES)),
                        error: I18n::get('forms.errorSubmittingArticle'),
                        article_id: $this->article->id->unwrapOr(0)
                    )
                ])
            :   null,

            component(ConvenienceLinks::class, editUrl: URLGenerator::generatePageUrl("/submitter/panel/articles/{$this->article->id->unwrapOr(0)}/edit"))
        ])
        : null;
    }
}