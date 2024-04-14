<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Magazines\MagazineId\Editions\EditionId;

use Exception;
use VictorOpusculo\AbelMagazine\Components\Label;
use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
use VictorOpusculo\AbelMagazine\Lib\Helpers\URLGenerator;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Edition;
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
        HeadManager::$title = I18n::get('pages.editEdition');
        $conn = Connection::get();
        try
        {
            if (!Connection::isId($this->editionId) || !Connection::isId($this->magazineId))
                throw new Exception(I18n::get('exceptions.invalidId'));

            $this->edition = (new Edition([ 'id' => $this->editionId ]))
            ->getSingle($conn)
            ->fetchMagazine($conn);
        }
        catch (Exception $e)
        {
            Context::getRef('page_messages')[] = $e->getMessage();
        }
    }

    protected $magazineId, $editionId;
    private ?Edition $edition = null;

    protected function markup(): Component|array|null
    {
        return isset($this->edition) && isset($this->edition->magazine) ? component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text(I18n::get('pages.editEdition'))),
            component(Label::class, labelBold: true, label: I18n::get('pages.magazine'), children:
                tag('a', class:'link', href: URLGenerator::generatePageUrl("/admin/panel/magazines/{$this->edition->magazine->id->unwrapOr(0)}"), children:
                    text($this->edition->magazine->name->unwrapOr(''))
                )
            ),
            tag('edit-edition-form', ...$this->edition->getValuesForHtmlForm(), langJson: Data::hscq(I18n::getFormsTranslationsAsJson()))
        ])
        : null;
    }
}