<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Media;

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
use VictorOpusculo\AbelMagazine\Lib\Model\Media\Media;
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
        HeadManager::$title = I18n::get('pages.media');
        $conn = Connection::get();
        try
        {
            $getter = new Media();
            $this->mediaCount = $getter->getCount($conn, $_GET['q'] ?? '');
            $media = $getter->getMultiple($conn, $_GET['q'] ?? '', $_GET['order_by'] ?? '', $_GET['page_num'] ?? 1, self::NUM_RESULTS_ON_PAGE);
            $this->media = Data::transformDataRows($media,
            [
                I18n::get('forms.id') => fn(Media $m) => $m->id->unwrapOr(0),
                I18n::get('forms.name') => fn(Media $m) => $m->name->unwrapOr('***'),
                I18n::get('forms.fileExtension') => fn(Media $m) => $m->file_extension->unwrapOr('')
            ]);
        }
        catch (Exception $e)
        {
            Context::getRef('page_messages')[] = $e->getMessage();
        }
    }

    private array $media = [];
    private int $mediaCount = 0;
    public const NUM_RESULTS_ON_PAGE = 20;

    protected function markup(): Component|array|null
    {
        return component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text(I18n::get('pages.media'))),
            
            component(BasicSearchInput::class),
            component(OrderByLinks::class, linksDefinitions: [ I18n::get('pages.id') => 'id', I18n::get('pages.name') => 'name', I18n::get('pages.fileExtension') => 'file_extension' ]),
            tag('div', class: 'my-2', children: tag('a', class: 'btn', href: URLGenerator::generatePageUrl('/admin/panel/media/create'), children: text(I18n::get('pages.newMedia')))),

            component(DataGrid::class,
                dataRows: $this->media,
                detailsButtonURL: URLGenerator::generatePageUrl('/admin/panel/media/{param}'),
                editButtonURL: URLGenerator::generatePageUrl('/admin/panel/media/{param}/edit'),
                deleteButtonURL: URLGenerator::generatePageUrl('/admin/panel/media/{param}/delete'),
                rudButtonsFunctionParamName: I18n::get('forms.id')
            ),
            component(Paginator::class,
                totalItems: $this->mediaCount,
                numResultsOnPage: self::NUM_RESULTS_ON_PAGE,
                pageNum: $_GET['page_num'] ?? 1
            )
        ]);
    }
}