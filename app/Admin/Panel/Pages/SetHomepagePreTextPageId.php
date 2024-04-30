<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Pages;

use Exception;
use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Settings\HomePagePreTextPageId;
use VictorOpusculo\AbelMagazine\Lib\Model\Settings\HomePagePreTextPageIdEnglish;
use VictorOpusculo\PComp\Component;
use VictorOpusculo\PComp\HeadManager;

use function VictorOpusculo\PComp\Prelude\component;
use function VictorOpusculo\PComp\Prelude\tag;
use function VictorOpusculo\PComp\Prelude\text;

final class SetHomepagePreTextPageId extends Component
{
    protected function setUp()
    {
        HeadManager::$title = I18n::get('pages.defineHomePagePreTextPage');
        $conn = Connection::get();
        try
        {
            $this->currentId = (new HomePagePreTextPageId())->getSingle($conn)->value->unwrapOr(null);
            $this->currentIdEnglish = (new HomePagePreTextPageIdEnglish())->getSingle($conn)->value->unwrapOr(null);
        }
        catch (Exception $e) {}
    }

    private ?int $currentId = null;
    private ?int $currentIdEnglish = null;

    protected function markup(): Component|array|null
    {
        return component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text(I18n::get('pages.defineHomePagePreTextPage'))),
            tag('set-homepage-pre-text', 
                page_id: $this->currentId,
                page_id_en: $this->currentIdEnglish,
                langJson: Data::hscq(I18n::getFormsTranslationsAsJson())
            )
        ]);
    }
}