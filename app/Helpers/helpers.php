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

