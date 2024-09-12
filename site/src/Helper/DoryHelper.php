<?php

namespace GiovanniMansillo\Component\Dory\Site\Helper;

\defined('_JEXEC') or die;

class DoryHelper
{
    public static function formatSizeUnits(int $bytes, int $roundPrecision = 2)
    {
        if ($bytes >= 1073741824) {
            return round($bytes / 1073741824, $roundPrecision) . " GB";
        } else if ($bytes >= 1048576) {
            return  round($bytes / 1048576, $roundPrecision) . " MB";
        } else {
            return  round($bytes / 1024, $roundPrecision) . " KB";
        }
    }
}