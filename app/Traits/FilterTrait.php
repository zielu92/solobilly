<?php

namespace App\Traits;

use Carbon\Carbon;

trait FilterTrait
{

    public function startDate(): \Illuminate\Support\Carbon|Carbon
    {
        return isset($this->filters['startDate'])
            ? Carbon::parse($this->filters['startDate'])->startOfDay()
            : now()->startOfYear();
    }

    public function endDate(): \Illuminate\Support\Carbon|Carbon
    {
        return isset($this->filters['endDate'])
            ? Carbon::parse($this->filters['endDate'])->endOfDay()
            : now();
    }

    public function type(): string
    {
        return (isset($this->filters['type']) && $this->filters['type'] === 'gross') ? 'gross' : 'net';
    }

    public function status(): string
    {
        return (isset($this->filters['status']) && $this->filters['status'] === 'not_paid') ? 'not_paid' : 'paid';
    }
}
