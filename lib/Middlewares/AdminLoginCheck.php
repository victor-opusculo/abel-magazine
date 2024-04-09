<?php

namespace VictorOpusculo\AbelMagazine\Lib\Middlewares;

use VictorOpusculo\AbelMagazine\Lib\Helpers\UserTypes;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;

function adminLoginCheck()
{
    session_name('abel_magazine_admin_user');
    session_start();

    if (($_SESSION['user_type'] ?? '') !== UserTypes::administrator)
    {
        session_unset();
        if (isset($_SESSION)) session_destroy();

        header('Content-Type: application/json', true, 401);
        echo json_encode([ 'error' => I18n::get('exceptions.wrongUserTypeReqAdmin') ]);
        exit;
    }
}