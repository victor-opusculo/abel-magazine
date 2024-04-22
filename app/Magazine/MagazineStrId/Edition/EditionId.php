<?php
namespace VictorOpusculo\AbelMagazine\App\Magazine\MagazineStrId\Edition;

use DateTimeZone;
use Exception;
use VictorOpusculo\AbelMagazine\Components\Data\BasicSearchInput;
use VictorOpusculo\AbelMagazine\Components\Data\Gallery;
use VictorOpusculo\AbelMagazine\Components\Data\OrderByLinks;
use VictorOpusculo\AbelMagazine\Components\Data\Paginator;
use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Lib\Helpers\URLGenerator;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Article;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Edition;
use VictorOpusculo\PComp\Component;
use VictorOpusculo\PComp\Context;
use VictorOpusculo\PComp\HeadManager;

use function VictorOpusculo\PComp\Prelude\component;
use function VictorOpusculo\PComp\Prelude\tag;
use function VictorOpusculo\PComp\Prelude\text;

final class EditionId extends Component
{
    protected function setUp()
    {
        HeadManager::$title = I18n::get('pages.edition');
        $conn = Connection::get();
        try
        {
            if (!Connection::isId($this->editionId))
                throw new Exception(I18n::get('exceptions.invalidId'));

            $this->edition = (new Edition([ 'id' => $this->editionId ]))
            ->getSingle($conn)
            ->fetchMagazine($conn);

            $articlesGetter = (new Article([ 'edition_id' => $this->edition->id->unwrapOr(0) ]));
            $this->artCount = $articlesGetter->getCountFromEdition($conn, $_GET['q'] ?? '', true);
            $this->articles = $articlesGetter->getMultipleFromEdition($conn, $_GET['q'] ?? '', $_GET['order_by'] ?? 'title', $_GET['page_num'] ?? 1, self::NUM_RESULTS_ON_PAGE, true);

            HeadManager::$title = $this->edition->title->unwrapOrElse(fn() => I18n::get('pages.edition'));
        }
        catch (Exception $e)
        {
            Context::getRef('page_messages')[] = $e->getMessage();
        }
    }

    protected $magazineStrId, $editionId;
    private ?Edition $edition = null;

    public const NUM_RESULTS_ON_PAGE = 9;
    private int $artCount = 0;
    /** @var Article[] */
    private array $articles = [];

    protected function markup(): Component|array|null
    {
        return isset($this->edition, $this->edition->magazine) ? component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text($this->edition->title->unwrapOr(''))),
            tag('div', class: 'flex flex-row justify-between font-bold text-lg', children: 
            [
                tag('span', children: text($this->edition->magazine->name->unwrapOr(''))),
                tag('span', children: text($this->edition->edition_label->unwrapOr('')))
            ]),

            $this->edition->is_open_for_submissions->unwrapOr(false)
            ?   tag('div', class: 'text-center my-8', children: 
                [
                    text(I18n::get('pages.submissionsOpen') . ' '),
                    tag('a', class: 'btn', href: URLGenerator::generatePageUrl("/submitter/panel/articles/create"), children: text(I18n::get('pages.sendYourArticle')))
                ])
            : null,

            $this->edition->is_published->unwrapOr(false)
            ?   
            [
                component(BasicSearchInput::class),
                component(OrderByLinks::class, linksDefinitions: 
                [ 
                    I18n::get('forms.title') => 'title',
                    I18n::get('forms.language') => 'language',
                    I18n::get('forms.submissionDate') => 'datetime'
                ]),

                component(Gallery::class,
                    dataRows: $this->articles,
                    imageGetter: fn($a) => URLGenerator::generateFileUrl('assets/pics/page.png'),
                    linkGetter: fn(Article $a) => URLGenerator::generatePageUrl("/magazine/{$this->magazineStrId}/article/{$a->id->unwrapOr(0)}"),
                    overlayElementsGetters:
                    [
                        fn(Article $a) => tag('span', class: 'font-bold', children: text($a->title->unwrapOr('***'))),
                        fn(Article $a) => tag('span', children: text(I18n::getAlias($a->language->unwrapOr('***')))),
                        fn(Article $a) => tag('span', children: text(
                            date_create($a->submission_datetime->unwrapOr('now'), new DateTimeZone('UTC'))
                            ->setTimezone(new DateTimeZone('America/Sao_Paulo'))
                            ->format(I18n::get('pages.dateFormat'))
                        ))
                    ]
                ),
                component(Paginator::class,
                    totalItems: $this->artCount,
                    numResultsOnPage: self::NUM_RESULTS_ON_PAGE,
                    pageNum: $_GET['page_num'] ?? 1
                )
            ]
            :
            [
                tag('p', class: 'text-center', children: text(I18n::get('pages.notPublishedYet')))
            ]
        ])
        : null;
    }
}