<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Pages\PageId;

use Exception;
use VictorOpusculo\AbelMagazine\Components\Panels\ConvenienceLinks;
use VictorOpusculo\AbelMagazine\Components\Site\PageViewer;
use VictorOpusculo\AbelMagazine\Lib\Helpers\URLGenerator;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Pages\Page;
use VictorOpusculo\PComp\Component;
use VictorOpusculo\PComp\Context;
use VictorOpusculo\PComp\HeadManager;

use function VictorOpusculo\PComp\Prelude\component;

final class View extends Component
{
    protected function setUp()
    {
        HeadManager::$title = I18n::get('pages.page');
        $conn = Connection::get();
        try
        {
            if (!Connection::isId($this->pageId))
                throw new Exception(I18n::get('exceptions.invalidId'));

            $page = (new Page([ 'id' => $this->pageId ]))->getSingle($conn);
            $this->page = $page;
        }
        catch (Exception $e)
        {
            Context::getRef('page_messages')[] = $e->getMessage();
        }
    }

    protected $pageId;
    private ?Page $page = null;

    protected function markup(): Component|array|null
    {
        return isset($this->page) ?
        [
            component(PageViewer::class, page: $this->page),
            component(ConvenienceLinks::class,
                editUrl: URLGenerator::generatePageUrl("/admin/panel/pages/{$this->page->id->unwrapOr(0)}/edit"),
                deleteUrl: URLGenerator::generatePageUrl("/admin/panel/pages/{$this->page->id->unwrapOr(0)}/delete"),
            )
        ] : null;
    }
}