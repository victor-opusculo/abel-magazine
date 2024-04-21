<?php
namespace VictorOpusculo\AbelMagazine\Lib\Model\Magazines;

use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;

enum ArticleStatus: string
{
    case EvaluationInProgress = '1_evaluation_in_progress';
    case EvaluationInProgress2 = '2_evaluation_in_progress_more_reviewers';
    case Approved = '3_approved';
    case Disapproved = '4_disapproved';
    case ApprovedWithIddedFile = '5_approved_final';

    public static function translate(ArticleStatus|string $enumValue) : string
    {
        $enumValue2 = $enumValue;
        if (is_string($enumValue))
            $enumValue2 = self::tryFrom($enumValue);

        if ($enumValue2 instanceof ArticleStatus)
            return match($enumValue2)
            {
                self::EvaluationInProgress => I18n::get('pages.evaluationInProgress'),
                self::EvaluationInProgress2 => I18n::get('pages.evaluationInProgress2'),
                self::Approved => I18n::get('pages.approved'),
                self::Disapproved => I18n::get('pages.disapproved'),
                self::ApprovedWithIddedFile => I18n::get('pages.approvedWithIddedFile')
            };
        else 
            return '***';
    }
}