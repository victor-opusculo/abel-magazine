<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Magazines\MagazineId\Editions;

use Exception;
use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Magazine;
use VictorOpusculo\PComp\Component;
use VictorOpusculo\PComp\Context;
use VictorOpusculo\PComp\HeadManager;

use function VictorOpusculo\PComp\Prelude\component;
use function VictorOpusculo\PComp\Prelude\tag;
use function VictorOpusculo\PComp\Prelude\text;

final class Create extends Component
{
    protected function setUp()
    {
        HeadManager::$title = I18n::get('pages.newEdition');
        $conn = Connection::get();
        try
        {
            if (!Connection::isId($this->magazineId))
                throw new Exception(I18n::get("exceptions.invalidId"));

            $this->magazine = (new Magazine([ 'id' => $this->magazineId ]))->getSingle($conn);
        }
        catch (Exception $e)
        {
            Context::getRef('page_messages')[] = $e->getMessage();
        }
    }
    
    protected $magazineId;
    private ?Magazine $magazine = null;

    protected function markup(): Component|array|null
    {
        return isset($this->magazine) ? component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text(I18n::get('pages.newEdition'))),
            tag('edit-edition-form',
                magazine_id: $this->magazine->id->unwrapOr(0),
                langJson: Data::hscq(I18n::getFormsTranslationsAsJson())
            )
        ])
        : null;
    }
}