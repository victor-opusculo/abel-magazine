<?php
namespace VictorOpusculo\AbelMagazine\Components\Data;

use DateTimeZone;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\PComp\Component;

use function VictorOpusculo\PComp\Prelude\text;

class DateTimeTranslator extends Component
{
    protected ?string $utcDateTime = null;
    protected ?string $isoDateTime = null;

    protected function markup(): Component|array|null
    {
        return text($this->utcDateTime 
            ?   date_create($this->utcDateTime, new DateTimeZone('UTC'))
                ->setTimezone(new DateTimeZone($_SESSION['user_timezone'] ?? 'America/Sao_Paulo'))
                ->format(I18n::get('pages.dateTimeFormat'))
                . ' (' . ($_SESSION['user_timezone'] ?? 'America/Sao_Paulo') . ')'
            : ($this->isoDateTime
                ?   date_create($this->isoDateTime)
                    ->setTimezone(new DateTimeZone($_SESSION['user_timezone'] ?? 'America/Sao_Paulo'))
                    ->format(I18n::get('pages.dateTimeFormat'))
                    . ' (' . ($_SESSION['user_timezone'] ?? 'America/Sao_Paulo') . ')'
                :
                    '***'
            )
        );
    }
}