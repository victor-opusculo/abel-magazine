<?php
namespace VictorOpusculo\AbelMagazine\App\Submitter\Panel\Articles;

use DateTimeZone;
use Exception;
use VictorOpusculo\AbelMagazine\Components\Data\BasicSearchInput;
use VictorOpusculo\AbelMagazine\Components\Data\DataGrid;
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

final class Home extends Component
{
    protected function setUp()
    {
        HeadManager::$title = I18n::get('pages.myArticles');
        $conn = Connection::get();
        try
        {
            $getter = (new Article([ 'submitter_id' => $_SESSION['user_id'] ?? 0 ]));
            $this->artCount = $getter->getCountFromSubmitter($conn, $_GET['q'] ?? '');
            $articles = $getter->getMultipleFromSubmitter($conn, $_GET['q'] ?? '', $_GET['order_by'] ?? '', $_GET['page_num'] ?? 1, self::NUM_RESULTS_ON_PAGE);
            $this->articles = array_map(fn(Article $a) =>
            [
                'id' => $a->id->unwrapOr(0),
                I18n::get('forms.title') => Data::truncateText($a->title->unwrapOr(''), 40),
                I18n::get('forms.status') => ArticleStatus::translate($a->status->unwrapOr('')),
                I18n::get('forms.submissionDate') => date_create($a->submission_datetime->unwrapOr('now'), new DateTimeZone('UTC'))
                    ->setTimezone(new DateTimeZone($_SESSION['user_timezone'] ?? 'America/Sao_Paulo'))
                    ->format(I18n::get('pages.dateTimeFormat')),
                I18n::get('pages.magazine') => $a->getOtherProperties()->magazineName ?? '',
                I18n::get('pages.edition') => Data::truncateText($a->getOtherProperties()->editionTitle ?? '', 40)
            ], $articles);
        }
        catch (Exception $e)
        {
            Context::getRef('page_messages')[] = $e->getMessage();
        }
    }

    public const NUM_RESULTS_ON_PAGE = 20;
    private int $artCount = 0;
    /** @var Article[] */
    private array $articles = [];

    protected function markup(): Component|array|null
    {
        return component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text(I18n::get('pages.myArticles'))),

            component(BasicSearchInput::class),
            component(OrderByLinks::class, linksDefinitions: [ I18n::get('forms.title') => 'title', I18n::get('forms.submissionDate') => 'datetime', I18n::get('forms.approvedStatus') => 'approved' ]),

            tag('div', class: 'my-4', children:
                tag('a', class: 'btn', href: URLGenerator::generatePageUrl("/submitter/panel/articles/create"), children: text(I18n::get('pages.newArticle')))
            ),

            component(DataGrid::class,
                dataRows: $this->articles,
                detailsButtonURL: URLGenerator::generatePageUrl("/submitter/panel/articles/{param}"),
                editButtonURL: URLGenerator::generatePageUrl("/submitter/panel/articles/{param}/edit"),
                columnsToHide: [ 'id' ]
            ),
            component(Paginator::class,
                totalItems: $this->artCount,
                pageNum: $_GET['page_num'] ?? 1,
                numResultsOnPage: self::NUM_RESULTS_ON_PAGE
            )
        ]);
    }
}