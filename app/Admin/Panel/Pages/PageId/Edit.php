<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Pages\PageId;

use Exception;
use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Pages\Page;
use VictorOpusculo\PComp\Component;
use VictorOpusculo\PComp\Context;
use VictorOpusculo\PComp\HeadManager;

use function VictorOpusculo\PComp\Prelude\component;
use function VictorOpusculo\PComp\Prelude\tag;
use function VictorOpusculo\PComp\Prelude\text;

final class Edit extends Component
{
    protected function setUp()
    {
        HeadManager::$title = I18n::get('pages.editPage');
        $conn = Connection::get();
        try
        {
            if (!Connection::isId($this->pageId))
                throw new Exception(I18n::get('exceptions.invalidId'));

            $this->page = (new Page([ 'id' => $this->pageId ]))->getSingle($conn);
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
        return isset($this->page) ? component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text(I18n::get('pages.editPage'))),
            tag('edit-page-form', 
            ...$this->page->getValuesForHtmlForm(),
            langJson: Data::hscq(I18n::getFormsTranslationsAsJson()))
        ])
        : null;
    }
}