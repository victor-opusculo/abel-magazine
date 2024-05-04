<?php
namespace VictorOpusculo\AbelMagazine\App\Page;

use Exception;
use VictorOpusculo\AbelMagazine\Components\Site\PageViewer;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Pages\Page;
use VictorOpusculo\PComp\Component;
use VictorOpusculo\PComp\Context;
use VictorOpusculo\PComp\HeadManager;

use function VictorOpusculo\PComp\Prelude\component;

final class PageId extends Component
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

            if (!$page->is_published->unwrapOr(false))
                throw new Exception(I18n::get("exceptions.pageNotFound"));

            HeadManager::$title = PageViewer::applyTranslationIfUsed($page->title->unwrapOrElse(fn() => I18n::get('pages.page')));
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
        return isset($this->page) ? component(PageViewer::class, page: $this->page) : null;
    }
}