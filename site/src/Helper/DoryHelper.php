<?php

namespace GiovanniMansillo\Component\Dory\Site\Helper;

\defined('_JEXEC') or die;

class DoryHelper
{
    public static function formatSizeUnits(int $bytes, int $roundPrecision = 2)
    {
        if ($bytes >= 1073741824) {
            $bytes = round($bytes / 1073741824, $roundPrecision) . " GB";
        } else if ($bytes >= 1048576) {
            $bytes =  round($bytes / 1048576, $roundPrecision) . " MB";
        } else if ($bytes >= 1024) {
            $bytes =  round($bytes / 1024, $roundPrecision) . " KB";
        } else if ($bytes > 1) {
            $bytes = $bytes . " bytes";
        } else if ($bytes == 1) {
            $bytes = $bytes . " byte";
        } else {
            $bytes = "0 bytes";
        }
        return $bytes;
    }
}
