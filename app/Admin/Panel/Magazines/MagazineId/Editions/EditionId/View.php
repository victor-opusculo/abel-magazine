<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Magazines\MagazineId\Editions\EditionId;

use carry0987\I18n\Language\LanguageLoader;
use Exception;
use VictorOpusculo\AbelMagazine\Components\Label;
use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Components\Panels\ConvenienceLinks;
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

final class View extends Component
{
    protected function setUp()
    {
        HeadManager::$title = I18n::get("pages.viewEdition");
        $conn = Connection::get();
        try
        {
            if (!Connection::isId($this->editionId))
                throw new Exception(I18n::get("exceptions.invalidId"));

            $this->includeSoftDeleted = !empty($_GET['show_deleted']) ? true : false;

            $edition = new Edition([ 'id' => $this->editionId ]);
            $edition->includeSoftDeleted = !empty($_GET['show_deleted']) ? true : false;
            $this->edition = $edition->getSingle($conn)->fetchMagazine($conn);
        }
        catch (Exception $e)
        {
            Context::getRef('page_messages')[] = $e->getMessage();
        }
    }

    protected $magazineId, $editionId;
    private bool $includeSoftDeleted = false; 
    private ?Edition $edition = null;

    protected function markup(): Component|array|null
    {
        return isset($this->edition, $this->edition->magazine) ? component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text(I18n::get('pages.viewEdition'))),

            component(Label::class, labelBold: true, label: I18n::get('pages.magazine'), children: 
                tag('a', class:'link', href: URLGenerator::generatePageUrl("/admin/panel/magazines/{$this->edition->magazine->id->unwrapOr(0)}"), children:
                    text($this->edition->magazine->name->unwrapOr(''))
                )
            ),
            component(Label::class, labelBold: true, label: I18n::get('forms.id'), children: text($this->edition->id->unwrapOr(0))),
            component(Label::class, labelBold: true, label: I18n::get('forms.title'), children: text($this->edition->title->unwrapOr(''))),
            component(Label::class, labelBold: true, label: I18n::get('forms.refDate'), children: text(date_create($this->edition->ref_date->unwrapOr('now'))->format('d/m/Y'))),
            component(Label::class, labelBold: true, lineBreak: true, label: I18n::get('forms.description'), children: 
                tag('div', class: 'whitespace-pre-line', children:
                    text($this->edition->description->unwrapOr(''))
                )
            ),
            component(Label::class, labelBold: true, label: I18n::get('forms.editionLabel'), children: text($this->edition->edition_label->unwrapOr(''))),
            component(Label::class, labelBold: true, label: I18n::get('forms.isEditionPublished') . '?', children: text($this->edition->is_published->unwrapOr(false) ? I18n::get('alerts.yes') : I18n::get('alerts.no'))),
            component(Label::class, labelBold: true, label: I18n::get('forms.isEditionOpen') . '?', children: text($this->edition->is_open_for_submissions->unwrapOr(false) ? I18n::get('alerts.yes') : I18n::get('alerts.no'))),

            component(Label::class, labelBold: true, label: I18n::get('pages.articles'), children:
                tag('a', class: 'btn', href: URLGenerator::generatePageUrl("/admin/panel/magazines/{$this->magazineId}/editions/{$this->edition->id->unwrapOr(0)}/articles"), children: text(I18n::get('pages.view')))
            ),

            $this->edition->deleted_at->unwrapOr(null)
                ?   tag('restore-deleted-button', 
                        langJson: Data::hscq(I18n::getFormsTranslationsAsJson()),
                        function_url: URLGenerator::generateFunctionUrl("/admin/panel/magazines/{$this->edition->magazine->id->unwrapOr(0)}/editions/{$this->edition->id->unwrapOr(0)}")
                    )
                :   component(ConvenienceLinks::class,
                        editUrl: URLGenerator::generatePageUrl("/admin/panel/magazines/{$this->magazineId}/editions/{$this->edition->id->unwrapOr(0)}/edit"),
                        deleteUrl: URLGenerator::generatePageUrl("/admin/panel/magazines/{$this->magazineId}/editions/{$this->edition->id->unwrapOr(0)}/delete"),
                    ),

            tag('h2', children: text(I18n::get('pages.submissions')))
        ])
        : null;
    }
}