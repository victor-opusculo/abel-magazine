<?php
namespace VictorOpusculo\AbelMagazine\Components\Data;

use Closure;
use VictorOpusculo\AbelMagazine\Lib\Helpers\URLGenerator;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\PComp\Component;

use function VictorOpusculo\PComp\Prelude\scTag;
use function VictorOpusculo\PComp\Prelude\tag;
use function VictorOpusculo\PComp\Prelude\text;

class Gallery extends Component
{
    public array $dataRows = [];
    public ?Closure $imageGetter = null;
    public ?Closure $linkGetter = null;
    public array $overlayElementsGetters = [];

    protected function markup(): Component|array|null
    {
        return tag('div', class: 'flex flex-col md:flex-row p-4 justify-center items-center', children:
            count($this->dataRows) > 0 ?
                array_map(fn($dr) => tag('a', class: 'border-zinc-300 border relative block overflow-clip h-[300px] w-[300px] hover:brightness-75 bg-zinc-200 rounded-lg', 
                href: ($this->linkGetter)($dr),
                children:
                [
                    tag('div', class: 'absolute top-0 bottom-0 left-0 right-0 w-full', children:
                    [
                        ($this->imageGetter)($dr) 
                            ?   scTag('img', class: 'absolute top-0 bottom-0 left-0 right-0 m-auto', src: ($this->imageGetter)($dr))
                            :   scTag('img', class: 'absolute top-0 bottom-0 left-0 right-0 m-auto', src: URLGenerator::generateFileUrl('assets/pics/nopic.png'))

                    ]),
                    tag('div', class: 'absolute bottom-0 left-0 right-0 z-10 bg-neutral-300/80 p-2 text-center', children:
                        array_map(fn($elGetter) => tag('div', children: $elGetter($dr)), $this->overlayElementsGetters)
                    )
                ])
                , $this->dataRows)
            :
                text(I18n::get('forms.noDataAvailable'))
        );      
    }
}