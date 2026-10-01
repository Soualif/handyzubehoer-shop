<?php

namespace App\Support;

class Html
{
    /**
     * Keep only basic formatting tags, without any attributes (no links, scripts or event handlers).
     */
    public static function clean(?string $html): string
    {
        $html = strip_tags((string) $html, '<p><br><ul><ol><li><strong><b><em><i>');

        return preg_replace('/<(\/?)(p|br|ul|ol|li|strong|b|em|i)\b[^>]*>/i', '<$1$2>', $html);
    }
}
