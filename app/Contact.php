<?php
namespace VictorOpusculo\AbelMagazine\App;

use Exception;
use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Settings\EmailToContact;
use VictorOpusculo\PComp\Component;
use VictorOpusculo\PComp\HeadManager;

use function VictorOpusculo\PComp\Prelude\component;
use function VictorOpusculo\PComp\Prelude\tag;
use function VictorOpusculo\PComp\Prelude\text;

final class Contact extends Component
{
    protected function setUp()
    {
        HeadManager::$title = I18n::get('layout.contact');

        $conn = Connection::get();
        try
        {
            $this->contactEmail = (new EmailToContact)->getSingle($conn)->value->unwrapOr('');
        }
        catch (Exception) {}
    }

    private string $contactEmail = '';

    protected function markup(): Component|array|null
    {
        return component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text(I18n::get('layout.contact'))),
            tag('contact-form', langJson: Data::hscq(I18n::getFormsTranslationsAsJson())),
            tag('div', class: 'mt-2', children: 
            [
                tag('p', class: 'font-bold', children: text("Associação Brasileira das Escolas do Legislativo e de Contas (ABEL)")),
                tag('p', class: 'font-bold', children: text("CNPJ: 05.801.353.0001-04")),
                tag('p', class: 'font-bold', children: text("Endereço: SHIS, QL 18, Conjunto 6, Casa 2, Lago SUL, CEP: 71650-065, Brasília/DF"))
            ]),
            tag('div', class: 'mt-2', children: tag('a', class: 'link', href: 'mailto:' . $this->contactEmail, children: text('📧 ' . $this->contactEmail)))
        ]);
    }
}