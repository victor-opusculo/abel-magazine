<?php
namespace VictorOpusculo\AbelMagazine\App;

use Exception;
use VictorOpusculo\AbelMagazine\App\Page\PageId;
use VictorOpusculo\AbelMagazine\Components\Data\Gallery;
use VictorOpusculo\AbelMagazine\Components\Site\PageViewer;
use VictorOpusculo\AbelMagazine\Lib\Helpers\URLGenerator;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Edition;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Magazine;
use VictorOpusculo\AbelMagazine\Lib\Model\Pages\Page;
use VictorOpusculo\AbelMagazine\Lib\Model\Settings\HomePagePreTextPageId;
use VictorOpusculo\AbelMagazine\Lib\Model\Settings\HomePagePreTextPageIdEnglish;
use VictorOpusculo\PComp\Component;
use VictorOpusculo\PComp\Context;
use VictorOpusculo\PComp\HeadManager;

use function VictorOpusculo\PComp\Prelude\component;
use function VictorOpusculo\PComp\Prelude\tag;
use function VictorOpusculo\PComp\Prelude\text;

final class HomePage extends Component
{
    protected function setUp()
    {
        HeadManager::$title = "Revista de Educação Legislativa em Foco - RELF";
        $conn = Connection::get();
        try
        {
            $this->magazines = (new Magazine())->getAll($conn);
            foreach ($this->magazines as $mag)
                $mag
                ->fetchCoverImage($conn)
                ->fetchEditionCount($conn);
        }
        catch (Exception $e)
        {
            Context::getRef('page_messages')[] = $e->getMessage();
        }

        try
        {
            $lang = I18n::$instance->fetchCurrentLang();
            $pageId = match ($lang)
            {
                'en_US' => (new HomePagePreTextPageIdEnglish)->getSingle($conn)->value->unwrapOr(null),
                'pt_BR' => (new HomePagePreTextPageId)->getSingle($conn)->value->unwrapOr(null),
                default => (new HomePagePreTextPageId)->getSingle($conn)->value->unwrapOr(null)
            };

            $this->preText = (new Page([ 'id' => $pageId ]))->getSingle($conn);
        }
        catch (Exception)
        {
        }

    }

    /** @var Magazine[] */
    private array $magazines = [];

    private ?Page $preText = null;

    protected function markup(): Component|array|null
    {
        return 
        [
            tag('h1', children: text(I18n::get("layout.homePageTitle"))),
            isset($this->preText)
                ?   component(PageViewer::class, page: $this->preText, showTitle: false)
                :   null,
            component(Gallery::class, 
                dataRows: $this->magazines,
                imageGetter: fn(Magazine $m) => URLGenerator::generateFileUrl($m->coverImage->fileNameFromBaseDir()),
                linkGetter: fn(Magazine $m) => URLGenerator::generatePageUrl("/magazine/{$m->string_identifier->unwrapOr('')}"),
                overlayElementsGetters:
                [
                    fn(Magazine $m) => tag('span', class: 'font-bold', children: text($m->name->unwrapOr(''))),
                    fn(Magazine $m) => text(I18n::get('pages.editions'))
                    /*fn(Magazine $m) => text(
                        $m->editionCount > 1 
                            ? $m->editionCount . " " . mb_strtolower(I18n::get('pages.editions'))
                            : $m->editionCount . " " . mb_strtolower(I18n::get("pages.edition"))
                    )*/
                ]
            )
        ];
    }
}