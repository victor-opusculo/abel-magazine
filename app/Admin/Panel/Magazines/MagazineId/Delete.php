<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Magazines\MagazineId;

use Exception;
use VictorOpusculo\AbelMagazine\Components\Label;
use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Components\Panels\ConvenienceLinks;
use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
use VictorOpusculo\AbelMagazine\Lib\Helpers\URLGenerator;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Magazine;
use VictorOpusculo\PComp\Component;
use VictorOpusculo\PComp\Context;
use VictorOpusculo\PComp\HeadManager;

use function VictorOpusculo\PComp\Prelude\component;
use function VictorOpusculo\PComp\Prelude\scTag;
use function VictorOpusculo\PComp\Prelude\tag;
use function VictorOpusculo\PComp\Prelude\text;

final class Delete extends Component
{
    protected function setUp()
    {
        HeadManager::$title = I18n::get('pages.deleteMagazine');
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
            tag('h1', children: text(I18n::get('pages.deleteMagazine'))),

            tag('delete-entity-form', 
            delete_function_route_url: URLGenerator::generateFunctionUrl("/admin/panel/magazines/{$this->magazine->id->unwrapOr(0)}"),
            go_back_to_url: '/admin/panel/magazines',
            langJson: Data::hscq(I18n::getFormsTranslationsAsJson()),
            children: 
            [
                component(Label::class, labelBold: true, label: I18n::get('pages.id'), children: text($this->magazine->id->unwrapOr(0))),
                component(Label::class, labelBold: true, label: I18n::get('pages.name'), children: text($this->magazine->name->unwrapOr(''))),
                component(Label::class, labelBold: true, label: I18n::get('pages.stringIdentifier'), children: text($this->magazine->string_identifier->unwrapOr(''))),
                component(Label::class, labelBold: true, label: I18n::get('pages.directLink'), children:
                    tag('a', class: 'link', href: URLGenerator::generatePageUrl("/magazine/{$this->magazine->string_identifier->unwrapOr('')}"), children:
                        text(URLGenerator::generatePageUrl("/magazine/{$this->magazine->string_identifier->unwrapOr('')}"))
                    ) 
                ),
            ])
        ])
        : null;
    }
}