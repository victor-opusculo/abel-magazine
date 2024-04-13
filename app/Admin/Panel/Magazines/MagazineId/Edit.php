<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Magazines\MagazineId;

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

final class Edit extends Component
{
    protected function setUp()
    {
        HeadManager::$title = I18n::get('pages.editMagazine');
        $conn = Connection::get();
        try
        {
            if (!Connection::isId($this->magazineId))
                throw new Exception('exceptions.invalidId');

            $this->magazine = (new Magazine([ 'id' => $this->magazineId ]))
            ->getSingle($conn);
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
            tag('h1', children: text(I18n::get('pages.editMagazine'))),
            tag('edit-magazine-form', ...$this->magazine->getValuesForHtmlForm(), langJson: Data::hscq(I18n::getFormsTranslationsAsJson()))
        ])
        : null;
    }
}