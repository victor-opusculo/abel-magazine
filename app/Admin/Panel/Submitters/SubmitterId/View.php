<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Submitters\SubmitterId;

use Exception;
use VictorOpusculo\AbelMagazine\Components\Data\DateTimeTranslator;
use VictorOpusculo\AbelMagazine\Components\Label;
use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Submitters\Submitter;
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
        HeadManager::$title = I18n::get('pages.viewAuthor');
        $conn = Connection::get();
        try
        {
            if (!Connection::isId($this->submitterId))
                throw new Exception(I18n::get('exceptions.invalidId'));

            $this->submitter = (new Submitter([ 'id' => $this->submitterId ]))
            ->setCryptKey(Connection::getCryptoKey())
            ->getSingle($conn)
            ->setCryptKey(Connection::getCryptoKey());

            HeadManager::$title = $this->submitter->full_name->unwrapOrElse(fn() => I18n::get('pages.viewAuthor'));
        }
        catch (Exception $e)
        {
            Context::getRef('page_messages')[] = $e->getMessage();
        }
    }

    protected $submitterId;
    private ?Submitter $submitter = null;

    protected function markup(): Component|array|null
    {
        return isset($this->submitter) ? component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text(I18n::get('pages.viewAuthor'))),

            component(Label::class, label: I18n::get('forms.id'), labelBold: true, children: text($this->submitter->id->unwrapOr(""))),
            component(Label::class, label: I18n::get('forms.fullName'), labelBold: true, children: text($this->submitter->full_name->unwrapOr(""))),
            component(Label::class, label: I18n::get('forms.email'), labelBold: true, children: text($this->submitter->email->unwrapOr(""))),
            component(Label::class, label: I18n::get('forms.telephone'), labelBold: true, children: text($this->submitter->other_infos->unwrap()->telephone->unwrapOr(""))),
            component(Label::class, label: I18n::get('pages.timezone'), labelBold: true, children: text($this->submitter->timezone->unwrapOr(""))),
            component(Label::class, label: I18n::get('pages.registrationDateTime'), labelBold: true, children: 
                component(DateTimeTranslator::class, utcDateTime: $this->submitter->registration_datetime->unwrapOr(""))    
            ),
        ])
        : null;
    }
}