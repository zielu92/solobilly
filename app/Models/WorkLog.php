<?php

namespace App\Models;

use App\Enum\TypeOfContract;
use Carbon\Carbon;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'buyer_id',
        'start',
        'end',
        'description',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'buyer_id' => 'integer',
        'start' => 'datetime',
        'end' => 'datetime',
    ];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class);
    }

    /**
     * Calculate the total duration in hours.
     */
    public function getDurationAttribute(): string
    {
        if (!$this->start || !$this->end) {
            return '0.00';
        }

        return number_format($this->start->diffInSeconds($this->end) / 3600, 2, '.', '');
    }

    public static function calculateTotalHoursBetweenDates($startDate, $endDate): float
    {
        if (!$startDate || !$endDate) {
            return '0.00';
        }
        $endDate = Carbon::parse($endDate)->endOfDay();
        $worklogs = self::whereBetween('start', [$startDate, $endDate])
            ->orWhereBetween('end', [$startDate, $endDate])
            ->get();

        $totalSeconds = 0;
        foreach ($worklogs as $worklog) {
            if ($worklog->start && $worklog->end) {
                $totalSeconds += $worklog->start->diffInSeconds($worklog->end);
            }
        }

        return number_format($totalSeconds / 3600, 2, '.', '');
    }

    public static function calculatePriceBasedOnTime($startDate, $endDate, $contractType, $rate): float
    {
        if ($contractType === TypeOfContract::DAILY) {
            $days = Worklog::calculateWorkingDaysBetweenDates($startDate, $endDate);
            return $days*$rate;
        } else {
            $hours = Worklog::calculateTotalHoursBetweenDates($startDate, $endDate);
            return $hours*$rate;
        }
    }

    public static function calculateWorkingDaysBetweenDates($startDate, $endDate): int
    {
        if (!$startDate || !$endDate) {
            return 0;
        }
        $endDate = Carbon::parse($endDate)->endOfDay();
        $worklogs = self::whereBetween('start', [$startDate, $endDate])
            ->orWhereBetween('end', [$startDate, $endDate])
            ->get();

        $workingDates = [];
        foreach ($worklogs as $worklog) {
            if ($worklog->start) {
                $workingDates[] = $worklog->start->format('Y-m-d');
            }
        }

        return count(array_unique($workingDates));
    }

}
