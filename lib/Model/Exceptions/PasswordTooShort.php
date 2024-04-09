<?php
namespace VictorOpusculo\AbelMagazine\Lib\Model\Exceptions;

use Exception;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;

class EmailNotFound extends Exception
{
    public function __construct()
    {
        parent::__construct(I18n::get('exceptions.newPasswordTooShort'));
    }
}