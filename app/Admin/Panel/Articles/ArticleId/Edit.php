<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Articles\ArticleId;

use Exception;
use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Article;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Edition;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Upload\IddedArticleUpload;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Upload\NotIddedArticleUpload;
use VictorOpusculo\PComp\Component;
use VictorOpusculo\PComp\Context;
use VictorOpusculo\PComp\HeadManager;

use function VictorOpusculo\PComp\Prelude\component;
use function VictorOpusculo\PComp\Prelude\tag;
use function VictorOpusculo\PComp\Prelude\text;

final class Edit extends Component
{
    protected function setUp()
    {
        HeadManager::$title = I18n::get('pages.editArticle');
        $conn = Connection::get();
        try
        {
            if (!Connection::isId($this->articleId))
                throw new Exception(I18n::get('exceptions.invalidId'));

            $this->article = (new Article([ 'id' => $this->articleId ]))->getSingle($conn);

            $aEditions = (new Edition())->getAllOpenForSubmittions($conn);
            $this->availableEditions = array_map(fn(Edition $e) =>
            [
                'id' => $e->id->unwrapOr(0),
                'title' => $e->title->unwrapOr('') . " ({$e->edition_label->unwrapOr('')} | "  . ($e->getOtherProperties()->magazineName ?? '') . ")"
            ], $aEditions);
        }
        catch (Exception $e)
        {
            Context::getRef('page_messages')[] = $e->getMessage();
        }
    }

    protected $articleId;
    private ?Article $article = null;
    private array $availableEditions = [];

    protected function markup(): Component|array|null
    {
        return isset($this->article) ? component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text(I18n::get('pages.editArticle'))),
            tag('admin-edit-article',
                ...$this->article->getValuesForHtmlForm(),
                langJson: Data::hscq(I18n::getFormsTranslationsAsJson()),
                availableLanguages: Data::hscq(json_encode(array_map(fn($lang, $alias) => [ 'code' => $lang, 'label' => $alias ], array_keys(I18n::availableLangs()), array_values(I18n::availableLangs())))),
                availableEditions: Data::hscq(json_encode($this->availableEditions)),
                allowed_mime_types: implode(',', NotIddedArticleUpload::ALLOWED_TYPES),
                allowed_mime_types_final: implode(',', IddedArticleUpload::ALLOWED_TYPES)
            )
        ])
        : null;
    }
}