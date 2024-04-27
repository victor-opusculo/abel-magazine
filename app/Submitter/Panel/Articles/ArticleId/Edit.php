<?php
namespace VictorOpusculo\AbelMagazine\App\Submitter\Panel\Articles\ArticleId;

use Exception;
use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Assessors\AssessorEvaluationToken;
use VictorOpusculo\AbelMagazine\Lib\Model\Assessors\AssessorOpinion;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Article;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\ArticleStatus;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Edition;
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

            $aEditions = (new Edition)->getAllOpenForSubmittions($conn);
            $this->availableEditions = array_map(fn(Edition $e) =>
            [
                'id' => $e->id->unwrapOr(0),
                'title' => $e->title->unwrapOr('') . " ({$e->edition_label->unwrapOr('')} | "  . ($e->getOtherProperties()->magazineName ?? '') . ")"
            ], $aEditions);

            $article = (new Article([ 'id' => $this->articleId, 'submitter_id' => $_SESSION['user_id'] ?? 0 ]))->getSingleFromSubmitter($conn);
            $this->article = $article;

            $evTokensExist = (new AssessorEvaluationToken([ 'article_id' => $this->articleId ]))->existsForArticle($conn);
            $asOpinionsExist = (new AssessorOpinion([ 'article_id' => $this->articleId ]))->existsForArticle($conn);

            if ($evTokensExist || $asOpinionsExist)
                $this->canEdit = false;

            $langs = I18n::availableLangs() + [ 'es' => 'Español' ];
            $this->availableLangs = array_map(fn($lang, $alias) => [ 'code' => $lang, 'label' => $alias ], array_keys($langs), array_values($langs));
        }
        catch (Exception $e)
        {
            Context::getRef('page_messages')[] = $e->getMessage();
        }
    }

    protected $articleId;
    private ?Article $article = null;
    private bool $canEdit = true;
    private array $availableEditions = [];
    private array $availableLangs = [];

    protected function markup(): Component|array|null
    {
        return isset($this->article) && $this->canEdit ? component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text(I18n::get('pages.editArticle'))),
            tag('submitter-edit-article',
                ...$this->article->getValuesForHtmlForm([ 'is_approved', 'status', 'submitter_id' ]), 
                langJson: Data::hscq(I18n::getFormsTranslationsAsJson()),
                availableLanguages: Data::hscq(json_encode($this->availableLangs)),
                availableEditions: Data::hscq(json_encode($this->availableEditions)),
                allowed_mime_types: implode(',', NotIddedArticleUpload::ALLOWED_TYPES)
            )
        ])
        :   (!$this->canEdit 
            ?   tag('p', class: 'text-center font-bold mt-8', children: text(I18n::get('pages.cannotEditArticle')))
            :   null);
    }
}