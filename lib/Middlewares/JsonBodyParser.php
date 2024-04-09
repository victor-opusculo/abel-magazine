<?php

namespace VictorOpusculo\AbelMagazine\Lib\Middlewares;

function jsonParser()
{
    $_POST = json_decode(file_get_contents('php://input'), true);
}