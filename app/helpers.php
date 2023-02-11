<?php
if (! function_exists('getBetween')) {
    function getBetween($content, $start, $end): string
    {
        $r = explode($start, $content);
        if (isset($r[1])) {
            $r = explode($end, $r[1]);
            return $r[0];
        }
        return '';
    }
}
