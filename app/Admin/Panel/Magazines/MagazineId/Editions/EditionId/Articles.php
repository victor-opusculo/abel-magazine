<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Magazines\MagazineId\Editions\EditionId;

use DateTimeZone;
use Exception;
use VictorOpusculo\AbelMagazine\Components\Data\BasicSearchInput;
use VictorOpusculo\AbelMagazine\Components\Data\DataGrid;
use VictorOpusculo\AbelMagazine\Components\Data\DataGridIcon;
use VictorOpusculo\AbelMagazine\Components\Data\OrderByLinks;
use VictorOpusculo\AbelMagazine\Components\Data\Paginator;
use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
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

final class Articles extends Component
{
    protected function setUp()
    {
        HeadManager::$title = I18n::get('pages.articles');
        $conn = Connection::get();
        try
        {
            if (!Connection::isId($this->editionId))
                throw new Exception("exceptions.invalidId");

            $getter = (new Article([ 'edition_id' => $this->editionId ]));
            $approvedOnly = (bool)(int)($_GET['approved_only'] ?? 0);
            $this->articleCount = $getter->getCountFromEdition($conn, $_GET['q'] ?? '', $approvedOnly);
            $articles = $getter->getMultipleFromEdition($conn, $_GET['q'] ?? '', $_GET['order_by'] ?? '', $_GET['page_num'] ?? 1, self::NUM_RESULTS_ON_PAGE, $approvedOnly);
            $this->articles = array_map(fn(Article $a) =>
            [
                I18n::get('forms.id') => $a->id->unwrapOr(0),
                I18n::get('forms.title') => Data::truncateText($a->title->unwrapOr(''), 60),
                I18n::get('forms.status') => ArticleStatus::translate($a->status->unwrapOr('')),
                I18n::get('pages.publicationStatus') => $a->is_approved->unwrapOr(false)
                                                    ?   new DataGridIcon('assets/pics/check.png', I18n::get('pages.published'))
                                                    :   new DataGridIcon('assets/pics/wrong.png', I18n::get('pages.notPublished')),
                I18n::get('forms.submissionDate') => date_create($a->submission_datetime->unwrapOr('now'), new DateTimeZone('UTC'))
                                                        ->setTimezone(new DateTimeZone($_SESSION['user_timezone'] ?? 'America/Sao_Paulo'))
                                                        ->format(I18n::get('pages.dateTimeFormat')),
                I18n::get('forms.language') => I18n::getAlias($a->language->unwrapOr(''))

            ], $articles);
        }
        catch (Exception $e)
        {
            Context::getRef('page_messages')[] = $e->getMessage();
        }
    }

    public const NUM_RESULTS_ON_PAGE = 20;

    protected $magazineId;
    protected $editionId;

    /** @var Articles[] */
    private array $articles = [];
    private int $articleCount = 0;

    protected function markup(): Component|array|null
    {
        return component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text(I18n::get('pages.articles'))),
            component(BasicSearchInput::class),
            component(OrderByLinks::class, linksDefinitions:
            [
                I18n::get('forms.id') => 'id',
                I18n::get('forms.title') => 'title',
                I18n::get('forms.status') => 'status',
                I18n::get('pages.isApproved') => 'approved',
                I18n::get('forms.submissionDate') => 'datetime',
                I18n::get('forms.language') => 'language'
            ]),

            component(DataGrid::class,
                dataRows: $this->articles,
                detailsButtonURL: URLGenerator::generatePageUrl("/admin/panel/articles/{param}"),
                editButtonURL: URLGenerator::generatePageUrl("/admin/panel/articles/{param}/edit"),
                deleteButtonURL: URLGenerator::generatePageUrl("/admin/panel/articles/{param}/delete"),
                rudButtonsFunctionParamName: I18n::get('forms.id')
            ),

            component(Paginator::class,
                totalItems: $this->articleCount,
                numResultsOnPage: self::NUM_RESULTS_ON_PAGE,
                pageNum: $_GET['page_num'] ?? 1
            )
        ]);
    }
}