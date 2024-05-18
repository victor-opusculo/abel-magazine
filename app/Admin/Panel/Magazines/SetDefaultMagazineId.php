<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Magazines;

use Exception;
use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Settings\DefaultMagazineId;
use VictorOpusculo\PComp\Component;
use VictorOpusculo\PComp\HeadManager;

use function VictorOpusculo\PComp\Prelude\component;
use function VictorOpusculo\PComp\Prelude\tag;
use function VictorOpusculo\PComp\Prelude\text;

final class SetDefaultMagazineId extends Component
{
    protected function setUp()
    {
        HeadManager::$title = I18n::get('pages.defineDefaultMagazine');
        $conn = Connection::get();
        try
        {
            $this->currentId = (new DefaultMagazineId())->getSingle($conn)->value->unwrapOr(null);
        }
        catch (Exception $e) {}
    }

    private ?int $currentId = null;

    protected function markup(): Component|array|null
    {
        return component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text(I18n::get('pages.defineDefaultMagazine'))),
            tag('set-default-magazine-form', 
                magazine_id: $this->currentId,
                langJson: Data::hscq(I18n::getFormsTranslationsAsJson())
            )
        ]);
    }
}