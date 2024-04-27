<?php
namespace VictorOpusculo\AbelMagazine\App\Magazine\MagazineStrId\Article;

use Exception;
use VictorOpusculo\AbelMagazine\Components\Data\DateTimeTranslator;
use VictorOpusculo\AbelMagazine\Components\Label;
use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
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

final class ArticleId extends Component
{
    protected function setUp()
    {
        HeadManager::$title = I18n::get('pages.article');
        $conn = Connection::get();
        try
        {
            if (!Connection::isId($this->articleId))
                throw new Exception(I18n::get('exceptions.invalidId'));

            $article = (new Article([ 'id' => $this->articleId ]))
            ->getSingle($conn)
            ->fetchEdition($conn);
            
            if (!$article->is_approved->unwrapOr(false))
                throw new Exception(I18n::get('exceptions.articleNotFound'));

            $article->edition->fetchMagazine($conn);

            HeadManager::$title = $article->title->unwrapOrElse(fn() => I18n::get('pages.article'));
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
            tag('h1', children: text($this->article->title->unwrapOrElse(fn() => I18n::get('pages.article')))),

            component(Label::class, labelBold: true, label: I18n::get('forms.magazineEdition'), children: 
                text($this->article->edition->edition_label->unwrapOr('') . ' | ' . $this->article->edition->magazine->name->unwrapOr(''))
            ),

            component(Label::class, labelBold: true, label: I18n::get('forms.title'), children: text($this->article->title->unwrapOr(''))),
            component(Label::class, labelBold: true, label: I18n::get('forms.authors'), children: text(
                implode(', ', json_decode($this->article->authors->unwrapOr('[]') ?? '[]'))
            )),
            component(Label::class, labelBold: true, label: I18n::get('forms.language'), children: text(I18n::getAlias($this->article->language->unwrapOr('')))),
            component(Label::class, labelBold: true, label: I18n::get('forms.resume'), lineBreak: true, children: 
                tag('div', class: 'whitespace-pre-line', children: text($this->article->resume->unwrapOr('')))
            ),
            component(Label::class, labelBold: true, label: I18n::get('forms.keywords'), children: text($this->article->keywords->unwrapOr(''))),

            component(Label::class, labelBold: true, label: I18n::get('forms.submissionDate'), children: 
                component(DateTimeTranslator::class, utcDateTime: $this->article->submission_datetime->unwrapOr(null))
            ),

            component(Label::class, labelBold: true, label: I18n::get('pages.article'), children: 
                tag('a', class: 'btn', href: URLGenerator::generateScriptUrl('/fetch_article.php', [ 'id' => $this->article->id->unwrapOr(0) ]), children: text(I18n::get('pages.downloadFullArticle')))
            )
        ])
        : null;
    }
}