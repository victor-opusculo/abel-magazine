<?php
namespace VictorOpusculo\AbelMagazine\Lib\Model\Magazines;

use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;

enum ArticleStatus: string
{
    case EvaluationInProgress = '1_evaluation_in_progress';
    case Approved = '2_approved';
    case Disapproved = '3_disapproved';
    case ApprovedWithIddedFile = '4_approved_final';

    public static function translate(ArticleStatus|string $enumValue) : string
    {
        $enumValue2 = $enumValue;
        if (is_string($enumValue))
            $enumValue2 = self::tryFrom($enumValue);

        if ($enumValue2 instanceof ArticleStatus)
            return match($enumValue2)
            {
                self::EvaluationInProgress => I18n::get('pages.evaluationInProgress'),
                self::Approved => I18n::get('pages.approved'),
                self::Disapproved => I18n::get('pages.disapproved'),
                self::ApprovedWithIddedFile => I18n::get('pages.approvedWithIddedFile')
            };
        else 
            return '***';
    }
}