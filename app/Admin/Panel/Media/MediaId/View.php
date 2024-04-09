<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Media\MediaId;

use Exception;
use VictorOpusculo\AbelMagazine\Components\Label;
use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Components\Panels\ConvenienceLinks;
use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
use VictorOpusculo\AbelMagazine\Lib\Helpers\System;
use VictorOpusculo\AbelMagazine\Lib\Helpers\URLGenerator;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Media\Media;
use VictorOpusculo\PComp\Component;
use VictorOpusculo\PComp\Context;
use VictorOpusculo\PComp\HeadManager;

use function VictorOpusculo\PComp\Prelude\component;
use function VictorOpusculo\PComp\Prelude\rawText;
use function VictorOpusculo\PComp\Prelude\scTag;
use function VictorOpusculo\PComp\Prelude\tag;
use function VictorOpusculo\PComp\Prelude\text;

final class View extends Component
{
    protected function setUp()
    {
        HeadManager::$title = I18n::get('pages.viewMedia');
        $conn = Connection::get();
        try
        {
            if (!Connection::isId($this->mediaId))
                throw new Exception(I18n::get('exceptions.invalidId'));

            $this->media = (new Media([ 'id' => $this->mediaId ]))->getSingle($conn);
            $this->fileName = $this->media->fullFileName();
            $this->mime = mime_content_type($this->fileName);
            $this->isImage = strpos($this->mime, 'image') !== false;
        }
        catch (Exception $e)
        {
            Context::getRef('page_messages')[] = $e->getMessage();
        }
    }

    protected $mediaId;
    private ?Media $media = null;
    private bool $isImage;
    private string $mime;
    private string $fileName;

    protected function markup(): Component|array|null
    {
        return isset($this->media) ? component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text(I18n::get('pages.viewMedia'))),
            tag('div', class: 'text-center my-4', children:
            [
                $this->isImage ? 
                    scTag('img', 
                        class: 'inline-block',
                        src: URLGenerator::generateFileUrl($this->media->fileNameFromBaseDir()),
                        width: 256
                    )
                : 
                    tag('span', class: 'italic text-lg', children: text(I18n::get('pages.noPreview')))
            ]),

            component(Label::class, label: I18n::get('pages.id'), labelBold: true, children: text($this->media->id->unwrapOr(0))),
            component(Label::class, label: I18n::get('pages.name'), labelBold: true, children: text($this->media->name->unwrapOr(''))),
            component(Label::class, label: I18n::get('pages.description'), labelBold: true, lineBreak: true, children: rawText(nl2br(Data::hsc($this->media->description->unwrapOr(''))))),
            component(Label::class, label: I18n::get('pages.fileExtension'), labelBold: true, children: text($this->media->file_extension->unwrapOr('') . " ({$this->mime})")),
            component(Label::class, label: I18n::get('pages.directLink'), labelBold: true, children:
                tag('a', 
                    class: 'link', 
                    href: URLGenerator::generateFileUrl("uploads/media/{$this->media->id->unwrapOr(0)}.{$this->media->file_extension->unwrapOr('')}"),
                    children: text(URLGenerator::generateFileUrl("uploads/media/{$this->media->id->unwrapOr(0)}.{$this->media->file_extension->unwrapOr('')}"))
                )
            ),

            component(ConvenienceLinks::class, 
                editUrl: URLGenerator::generatePageUrl("/admin/panel/media/{$this->media->id->unwrapOr(0)}/edit"),
                deleteUrl: URLGenerator::generatePageUrl("/admin/panel/media/{$this->media->id->unwrapOr(0)}/delete")
            )
        ])
        : null;
    }
}