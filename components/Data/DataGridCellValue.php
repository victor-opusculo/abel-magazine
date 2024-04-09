<?php
namespace VictorOpusculo\AbelMagazine\Components\Data;

interface DataGridCellValue
{
    public function generateHtml() : string;
}