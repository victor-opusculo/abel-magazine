<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Pages\PageId;

use Exception;
use VictorOpusculo\AbelMagazine\Components\Label;
use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Components\Panels\ConvenienceLinks;
use VictorOpusculo\AbelMagazine\Components\Site\PageViewer;
use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
use VictorOpusculo\AbelMagazine\Lib\Helpers\URLGenerator;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Pages\Page;
use VictorOpusculo\PComp\Component;
use VictorOpusculo\PComp\Context;
use VictorOpusculo\PComp\HeadManager;

use function VictorOpusculo\PComp\Prelude\component;
use function VictorOpusculo\PComp\Prelude\tag;
use function VictorOpusculo\PComp\Prelude\text;

final class Delete extends Component
{
    protected function setUp()
    {
        HeadManager::$title = I18n::get('pages.deletePage');
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
        return isset($this->page) ? component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text(I18n::get('pages.deletePage'))),
            tag('delete-entity-form',
                delete_function_route_url: URLGenerator::generateFunctionUrl("/admin/panel/pages/{$this->page->id->unwrapOr(0)}"),
                go_back_to_url: '/admin/panel/pages',
                langJson: Data::hscq(I18n::getFormsTranslationsAsJson()),
                children:
                [
                    component(Label::class, labelBold: true, label: I18n::get('forms.id'), children: text($this->page->id->unwrapOr(0))),
                    component(Label::class, labelBold: true, label: I18n::get('forms.title'), children: text($this->page->title->unwrapOr(''))),
                    component(Label::class, labelBold: true, label: I18n::get('forms.published'), children: 
                        $this->page->is_published->unwrapOr(false)
                        ?   text(I18n::get('alerts.yes'))
                        :   text(I18n::get('alerts.no'))
                    ),
                ]
            )
        ])
        : null;
    }
}