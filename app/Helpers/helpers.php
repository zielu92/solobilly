<?php

if (!function_exists('showTaxRate')) {
    function showTaxRate($taxRate, $engExplanation = false): string
    {
        if (is_numeric($taxRate)) {
            return $taxRate . '%';
        }
        $taxLabels = [
            'zw' => $engExplanation ? 'ZW / exempt' : 'ZW',
            'np' => $engExplanation ? 'NP / not applicable' : 'NP'
        ];
        return $taxLabels[$taxRate] ?? $taxRate;
    }
}

if(!function_exists('randomColorHex')) {
    function randomColorHex() {
        return '#'.randomColorPart() . randomColorPart() . randomColorPart();
    }
}

if(!function_exists('randomColorPart')) {
    function randomColorPart()
    {
        return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('textColorContrast')) {
    function textColorContrast($hex){
        list($red, $green, $blue) = sscanf($hex, "#%02x%02x%02x");
        $luma = (0.299 * $red + 0.587 * $green + 0.114 * $blue);

        if ($luma < 128){
            return "white";
        }

        return "black";
    }
}

if(!function_exists('generateFileHash')) {
    function generateFileHash($path)
    {
        return hash_hmac('sha256', $path, config('app.key'));
    }
}
