<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Media\MediaId;

use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Media\Media;
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
        HeadManager::$title = I18n::get('pages.editMedia');
        $conn = Connection::get();
        try
        {
            if (!Connection::isId($this->mediaId))
                throw new \Exception(I18n::get('exceptions.invalidId'));

            $this->media = (new Media([ 'id' => $this->mediaId ]))->getSingle($conn);
        }
        catch (\Exception $e)
        {
            Context::getRef('page_messages')[] = $e->getMessage();
        }
    }

    protected $mediaId;
    private ?Media $media = null;

    protected function markup(): Component|array|null
    {
        return isset($this->media) ? component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text(I18n::get('pages.editMedia'))),

            tag('edit-media-form', 
                ...$this->media->getValuesForHtmlForm([ 'file_extension' ]),
                langJson: Data::hscq(I18n::getFormsTranslationsAsJson())
            )
        ])
        : null;
    }
}