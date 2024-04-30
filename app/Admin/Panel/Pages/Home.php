<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Pages;

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
use VictorOpusculo\AbelMagazine\Lib\Model\Pages\Page;
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
        HeadManager::$title = I18n::get('pages.pages');
        $conn = Connection::get();
        try
        {
            $getter = new Page;
            $this->pageCount = $getter->getCount($conn, $_GET['q'] ?? '');
            $pages = $getter->getMultiple($conn, $_GET['q'] ?? '', $_GET['order_by'] ?? '', $_GET['page_num'] ?? 1, self::NUM_RESULTS_ON_PAGE);
            $this->pages = array_map(fn(Page $p) =>
            [
                I18n::get('forms.id') => $p->id->unwrapOr(0),
                I18n::get('forms.title') => Data::truncateText($p->title->unwrapOr(''), 60),
                I18n::get('forms.published') => $p->is_published->unwrapOr(false)
                                                ?   new DataGridIcon('assets/pics/check.png', I18n::get('alerts.yes'))
                                                :   new DataGridIcon('assets/pics/wrong.png', I18n::get('alerts.no'))
            ], $pages);
        }
        catch (Exception $e)
        {
            Context::getRef('page_messages')[] = $e->getMessage();
        }
    }

    private int $pageCount = 0;
    /** @var Page[] */
    private array $pages = [];
    public const NUM_RESULTS_ON_PAGE = 20;

    protected function markup(): Component|array|null
    {
        return component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text(I18n::get('pages.pages'))),

            component(BasicSearchInput::class),
            component(OrderByLinks::class, linksDefinitions: 
            [
                I18n::get('forms.id') => 'id',
                I18n::get('forms.title') => 'title',
                I18n::get('forms.published') => 'is_published'
            ]),

            tag('div', class: 'my-2 text-left', children:
            [
                tag('a', class: 'btn mr-2', href: URLGenerator::generatePageUrl("/admin/panel/pages/create"), children: text(I18n::get('pages.newPage'))),
                tag('a', class: 'btn mr-2', href: URLGenerator::generatePageUrl("/admin/panel/pages/set_submission_rules_page"), children: text(I18n::get('pages.defineSubmissionRulesPage'))),
                tag('a', class: 'btn mr-2', href: URLGenerator::generatePageUrl("/admin/panel/pages/set_submission_template_page"), children: text(I18n::get('pages.defineArticleTemplatePage'))),
                tag('a', class: 'btn', href: URLGenerator::generatePageUrl("/admin/panel/pages/set_homepage_pre_text_page"), children: text(I18n::get('pages.defineHomePagePreTextPage')))
            ]),

            component(DataGrid::class,
                dataRows: $this->pages,
                detailsButtonURL: URLGenerator::generatePageUrl("/admin/panel/pages/{param}"),
                editButtonURL: URLGenerator::generatePageUrl("/admin/panel/pages/{param}/edit"),
                deleteButtonURL: URLGenerator::generatePageUrl("/admin/panel/pages/{param}/delete"),
                rudButtonsFunctionParamName: I18n::get('forms.id')
            ),

            component(Paginator::class,
                totalItems: $this->pageCount,
                numResultsOnPage: self::NUM_RESULTS_ON_PAGE
            )
        ]);
    }
}