<?php
namespace VictorOpusculo\AbelMagazine\App\Magazine\MagazineStrId;

use Exception;
use VictorOpusculo\AbelMagazine\Components\Data\BasicSearchInput;
use VictorOpusculo\AbelMagazine\Components\Data\Gallery;
use VictorOpusculo\AbelMagazine\Components\Data\OrderByLinks;
use VictorOpusculo\AbelMagazine\Components\Data\Paginator;
use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Lib\Helpers\URLGenerator;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Edition;
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
        HeadManager::$title = I18n::get('pages.magazine');
        $conn = Connection::get();
        try
        {
            if (!$this->magazineStrId)
            {
                header('location:' . URLGenerator::generatePageUrl('/'), true, 303);
                exit;
            }

            $this->magazine = (new Magazine([ 'string_identifier' => $this->magazineStrId ]))->getSingleByStringIdentifier($conn);
            HeadManager::$title = $this->magazine->name->unwrapOrElse(fn() => I18n::get('pages.magazine'));

            [ $edCount, $editions ] = $this->magazine->fetchMultipleEditions($conn, $_GET['q'] ?? '', $_GET['order_by'] ?? 'ref_date', $_GET['page_num'] ?? 1, self::NUM_EDITIONS_ON_PAGE, false);
            $this->editionCount = $edCount;
            $this->editions = $editions;
        }
        catch (Exception $e)
        {
            Context::getRef('page_messages')[] = $e->getMessage();
        }
    }

    protected $magazineStrId;
    private ?Magazine $magazine = null;
    private  int $editionCount = 0;
    private array $editions = [];
    public const NUM_EDITIONS_ON_PAGE = 9;

    protected function markup(): Component|array|null
    {
        return isset($this->magazine) ? component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text(I18n::get('pages.editions'))),
            tag('h2', children: text($this->magazine->name->unwrapOrElse(fn() => I18n::get('pages.magazine')))),

            tag('p', class: 'text-center', children: text(I18n::get('forms.issn') . ': ' . $this->magazine->issn->unwrapOr(''))),

            component(BasicSearchInput::class),
            component(OrderByLinks::class, linksDefinitions: [ I18n::get('forms.title') => 'title', I18n::get('forms.refDate')=> 'ref_date' ]),

            component(Gallery::class,
                dataRows: $this->editions,
                imageGetter: fn($e) => URLGenerator::generateFileUrl('assets/pics/page.png'),
                linkGetter: fn(Edition $e) => URLGenerator::generatePageUrl("/magazine/{$this->magazine->string_identifier->unwrapOr('')}/edition/{$e->id->unwrapOr(0)}"),
                overlayElementsGetters: 
                [
                    fn(Edition $e) => tag('span', class: 'font-bold', children: text($e->title->unwrapOr('Sem nome'))),
                    fn(Edition $e) => tag('span', children: text($e->edition_label->unwrapOr(''))),
                    fn(Edition $e) => tag('span', children: 
                        $e->is_published->unwrapOr(false)
                            ?   ( $e->is_open_for_submissions->unwrapOr(false)
                                    ?   text(I18n::get('pages.continuousFlow') . ' | ' . I18n::get('pages.currentEdition'))
                                    :   text(date_create($e->ref_date->unwrapOr('0001-01-01'))->format('m/Y'))
                                )
                            :   ($e->is_open_for_submissions->unwrapOr(false)
                                ?   tag('span', class: 'font-bold text-green-600', children: text(I18n::get('pages.submissionsOpen')))
                                :   tag('span', class: 'font-bold text-zinc-600', children: text(I18n::get('pages.publicationSoon')))
                            )
                    )
                ]
            ),
            component(Paginator::class,
                totalItems: $this->editionCount,
                numResultsOnPage: self::NUM_EDITIONS_ON_PAGE,
                pageNum: $_GET['page_num'] ?? 1
            )
        ])
        : null;
    }
}