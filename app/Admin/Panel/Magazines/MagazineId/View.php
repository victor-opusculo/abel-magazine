<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Magazines\MagazineId;

use Exception;
use VictorOpusculo\AbelMagazine\Components\Data\BasicSearchInput;
use VictorOpusculo\AbelMagazine\Components\Data\DataGrid;
use VictorOpusculo\AbelMagazine\Components\Data\OrderByLinks;
use VictorOpusculo\AbelMagazine\Components\Data\Paginator;
use VictorOpusculo\AbelMagazine\Components\Label;
use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Components\Panels\ConvenienceLinks;
use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
use VictorOpusculo\AbelMagazine\Lib\Helpers\QueryString;
use VictorOpusculo\AbelMagazine\Lib\Helpers\URLGenerator;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Magazine;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Edition;
use VictorOpusculo\PComp\Component;
use VictorOpusculo\PComp\Context;
use VictorOpusculo\PComp\HeadManager;

use function VictorOpusculo\PComp\Prelude\component;
use function VictorOpusculo\PComp\Prelude\scTag;
use function VictorOpusculo\PComp\Prelude\tag;
use function VictorOpusculo\PComp\Prelude\text;

final class View extends Component
{
    protected function setUp()
    {
        HeadManager::$title = I18n::get('pages.viewMagazine');
        $conn = Connection::get();
        try
        {
            if (!Connection::isId($this->magazineId))
                throw new Exception('exceptions.invalidId');

            $showSoftDeleted = $this->showSoftDeleted = !empty($_GET['show_deleted']) ? true : false;
            $this->showSoftDeletedEditions = !empty($_GET['show_deleted_ed']) ? true : false;
            $this->magazine = (new Magazine([ 'id' => $this->magazineId ]));
            $this->magazine->includeSoftDeleted = $showSoftDeleted;
            $this->magazine = $this->magazine->getSingle($conn)
            ->fetchCoverImage($conn);

            [ $edCount, $editions ] = $this->magazine->fetchMultipleEditions($conn, $_GET['q'] ?? '', $_GET['order_by'] ?? '', $_GET['page_num'] ?? 1, self::NUM_EDITIONS_ON_PAGE, $showSoftDeleted);
            $this->edCount = $edCount;
            $this->editions = Data::transformDataRows($editions,
            [
                I18n::get('forms.id') => fn(Edition $e) => $e->id->unwrapOr(0),
                I18n::get('forms.title') => fn(Edition $e) => $e->title->unwrapOr(''),
                I18n::get('forms.refDate') => fn(Edition $e) => date_create($e->ref_date->unwrapOr('now'))->format('d/m/Y'),
                I18n::get('forms.editionLabel') => fn(Edition $e) => $e->edition_label->unwrapOr('')
            ]);
        }
        catch (Exception $e)
        {
            Context::getRef('page_messages')[] = $e->getMessage();
        }
    }

    protected $magazineId;
    private bool $showSoftDeleted = false;
    private bool $showSoftDeletedEditions = false;
    private ?Magazine $magazine = null;
    private int $edCount = 0;
    /** @var Edition[] */
    private array $editions = [];
    public const NUM_EDITIONS_ON_PAGE = 20;

    protected function markup(): Component|array|null
    {
        return isset($this->magazine) ? component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text(I18n::get('pages.viewMagazine'))),

            tag('div', class: 'text-center my-4', children:
                isset($this->magazine->coverImage)
                ?   scTag('img', class: 'inline-block', width: 256, src: URLGenerator::generateFileUrl($this->magazine->coverImage->fileNameFromBaseDir()))
                :   text(I18n::get('pages.noPicture'))
            ),

            component(Label::class, labelBold: true, label: I18n::get('pages.id'), children: text($this->magazine->id->unwrapOr(0))),
            component(Label::class, labelBold: true, label: I18n::get('pages.name'), children: text($this->magazine->name->unwrapOr(''))),
            component(Label::class, labelBold: true, label: I18n::get('pages.description'), lineBreak: true, children: 
                tag('div', class: 'whitespace-pre-line', children: text($this->magazine->description->unwrapOr('')))
            ),
            component(Label::class, labelBold: true, label: I18n::get('pages.stringIdentifier'), children: text($this->magazine->string_identifier->unwrapOr(''))),
            component(Label::class, labelBold: true, label: I18n::get('pages.directLink'), children:
                tag('a', class: 'link', href: URLGenerator::generatePageUrl("/magazine/{$this->magazine->string_identifier->unwrapOr('')}"), children:
                    text(URLGenerator::generatePageUrl("/magazine/{$this->magazine->string_identifier->unwrapOr('')}"))
                ) 
            ),

            $this->magazine->deleted_at->unwrapOr(false) 
                ?   tag('restore-deleted-button', langJson: Data::hscq(I18n::getFormsTranslationsAsJson()), function_url: URLGenerator::generateFunctionUrl("/admin/panel/magazines/{$this->magazine->id->unwrapOr(0)}"))
                :   component(ConvenienceLinks::class, 
                        editUrl: URLGenerator::generatePageUrl("/admin/panel/magazines/{$this->magazine->id->unwrapOr(0)}/edit"),
                        deleteUrl: URLGenerator::generatePageUrl("/admin/panel/magazines/{$this->magazine->id->unwrapOr(0)}/delete"),
                    ),
            
            tag('h2', children: text('Edições')),
            component(BasicSearchInput::class),
            component(OrderByLinks::class, linksDefinitions: [ I18n::get('forms.id') => 'id', I18n::get('forms.title') => 'title', I18n::get('forms.refDate')=> 'ref_date' ]),

            tag('div', class: 'my-4 flex flex-row justify-between', children:
            [
                tag('a', class: 'btn', href: URLGenerator::generatePageUrl("/admin/panel/magazines/{$this->magazine->id->unwrapOr(0)}/editions/create"), children: 
                    text(I18n::get('pages.newEdition'))
                ),
                tag('a', class: 'btn', href: URLGenerator::generatePageUrl($_GET['page'], [ 'q' => $_GET['q'] ?? '', 'show_deleted' => $this->showSoftDeleted ? 1 : 0, 'show_deleted_ed' => $this->showSoftDeletedEditions ? 0 : 1 ]), children:
                    text($this->showSoftDeletedEditions ? I18n::get('pages.showNonDeletedOnly') : I18n::get('pages.showDeleted'))
                )
            ]),

            component(DataGrid::class,
                dataRows: $this->editions,
                rudButtonsFunctionParamName: I18n::get('forms.id'),
                detailsButtonURL: URLGenerator::generatePageUrl("/admin/panel/magazines/{$this->magazine->id->unwrapOr(0)}/editions/{param}"),
                editButtonURL: URLGenerator::generatePageUrl("/admin/panel/magazines/{$this->magazine->id->unwrapOr(0)}/editions/{param}/edit"),
                deleteButtonURL: URLGenerator::generatePageUrl("/admin/panel/magazines/{$this->magazine->id->unwrapOr(0)}/editions/{param}/delete"),
            ),
            component(Paginator::class,
                totalItems: $this->edCount,
                numResultsOnPage: self::NUM_EDITIONS_ON_PAGE,
                pageNum: $_GET['page_num'] ?? 1
            )
        ])
        : null;
    }
}