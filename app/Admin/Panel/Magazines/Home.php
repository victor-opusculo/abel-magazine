<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Magazines;

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
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Magazine;
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
        HeadManager::$title = I18n::get('pages.journals');
        $conn = Connection::get();
        try
        {
            $showSoftDeleted = $this->showSoftDeleted = !empty($_GET['show_deleted']) ? true : false; 
            $getter = new Magazine();
            $this->magazineCount = $getter->getCount($conn, $_GET['q'] ?? '', $showSoftDeleted);
            $magazines = $getter->getMultiple($conn, $_GET['q'] ?? '', $_GET['order_by'] ?? '', $_GET['page_num'] ?? 1, self::NUM_RESULTS_ON_PAGE, $showSoftDeleted);
            $this->magazines = Data::transformDataRows($magazines,
            [
                I18n::get('forms.id') => fn(Magazine $m) => $m->id->unwrapOr(0),
                I18n::get('forms.name') => fn(Magazine $m) => $m->name->unwrapOr('***'),
                I18n::get('pages.stringIdentifier') => fn(Magazine $m) => $m->string_identifier->unwrapOr('')
            ]);
        }
        catch (Exception $e)
        {
            Context::getRef('page_messages')[] = $e->getMessage();
        }
    }

    private array $magazines = [];
    private int $magazineCount = 0;
    private bool $showSoftDeleted = false;
    public const NUM_RESULTS_ON_PAGE = 20;

    protected function markup(): Component|array|null
    {
        return component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text(I18n::get('pages.journals'))),
            
            component(BasicSearchInput::class),
            component(OrderByLinks::class, linksDefinitions: [ I18n::get('pages.id') => 'id', I18n::get('pages.name') => 'name', I18n::get('pages.stringIdentifier') => 'string_identifier' ]),
            tag('div', class: 'flex flex-row justify-between my-2', children: 
            [
                tag('span', children:
                [
                    tag('a', class: 'btn', href: URLGenerator::generatePageUrl('/admin/panel/magazines/create'), children: text(I18n::get('pages.newJournal'))),
                    tag('a', class: 'btn ml-2', href: URLGenerator::generatePageUrl('/admin/panel/magazines/set_default_magazine'), children: text(I18n::get('pages.defineDefaultMagazine'))),
                ]),
                tag('a', class: 'btn', href: URLGenerator::generatePageUrl($_GET['page'], $this->showSoftDeleted ? [] : [ 'show_deleted' => 1 ]), children:
                    text($this->showSoftDeleted ? I18n::get('pages.showNonDeletedOnly') : I18n::get('pages.showDeleted'))
                )
            ]),

            component(DataGrid::class,
                dataRows: $this->magazines,
                detailsButtonURL: URLGenerator::generatePageUrl('/admin/panel/magazines/{param}', $this->showSoftDeleted ? [ 'show_deleted' => 1 ] : []),
                editButtonURL: URLGenerator::generatePageUrl('/admin/panel/magazines/{param}/edit'),
                deleteButtonURL: URLGenerator::generatePageUrl('/admin/panel/magazines/{param}/delete'),
                rudButtonsFunctionParamName: I18n::get('forms.id')
            ),
            component(Paginator::class,
                totalItems: $this->magazineCount,
                numResultsOnPage: self::NUM_RESULTS_ON_PAGE,
                pageNum: $_GET['page_num'] ?? 1
            )
        ]);
    }
}