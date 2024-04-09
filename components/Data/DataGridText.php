<?php
namespace VictorOpusculo\AbelMagazine\Components\Data;

use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;

class DataGridText implements DataGridCellValue
{
	private string $string;
	public function __construct(string $string)
	{
		$this->string = $string;
	}
	public function generateHtml(): string
	{
		return nl2br(Data::hsc($this->string));
	}
}