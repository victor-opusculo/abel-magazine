<?php
namespace VictorOpusculo\AbelMagazine\App\Info;

use Exception;
use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Components\Site\PageViewer;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Pages\Page;
use VictorOpusculo\AbelMagazine\Lib\Model\Settings\EditorialTeamPageId;
use VictorOpusculo\AbelMagazine\Lib\Model\Settings\EditorialTeamPageIdEnglish;
use VictorOpusculo\PComp\Component;
use VictorOpusculo\PComp\Context;
use VictorOpusculo\PComp\HeadManager;

use function VictorOpusculo\PComp\Prelude\component;

final class EditorialTeam extends Component
{
    protected function setUp()
    {
        HeadManager::$title = I18n::get('pages.page');
        $conn = Connection::get();
        try
        {
            $lang = I18n::$instance->fetchCurrentLang();
            $id = match ($lang)
            {
                'en_US' => (new EditorialTeamPageIdEnglish)->getSingle($conn)->value->unwrapOr(null),
                'pt_BR' => (new EditorialTeamPageId)->getSingle($conn)->value->unwrapOr(null),
                default => (new EditorialTeamPageId)->getSingle($conn)->value->unwrapOr(null)
            };  

            if (is_null($id))
                throw new Exception('exceptions.pageNotFound');

            $page = (new Page([ 'id' => $id ]))->getSingle($conn);

            if (!$page->is_published->unwrapOr(false))
                throw new Exception('exceptions.pageNotFound');

            HeadManager::$title = $page->title->unwrapOrElse(fn() => I18n::get('pages.page'));
            $this->page = $page;
        }
        catch (Exception $e)
        {
            Context::getRef('page_messages')[] = $e->getMessage();
        }
    }

    private ?Page $page = null;

    protected function markup(): Component|array|null
    {
        return isset($this->page) ? component(DefaultPageFrame::class, children:
            component(PageViewer::class, page: $this->page)
        )
        : null;
    }
}