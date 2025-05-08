<?php

namespace Modules\ExchangeRates\Console;

use App\Models\Currency;
use Carbon\Carbon;
use Date;
use Illuminate\Console\Command;
use Modules\ExchangeRates\Models\ExchangeRate;
use MaciejSz\Nbp\Service\CurrencyAverageRatesService;
use Yasumi\Yasumi;

class CheckExchangeRates extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'exchange-rates:check';

    /**
     * The console command description.
     */
    protected $description = 'Check and store exchange rates';

    /**
     * Executes the exchange rate command to fetch and store average rates from the NBP service.
     *
     * If the default currency is PLN, this method calculates the last workday's date and logs the retrieval date. It then iterates
     * through the configured currencies (excluding PLN), fetching the average exchange rate for each currency from the external service,
     * and updates or creates the corresponding exchange rate record in the database. Any errors encountered during the rate retrieval process are logged.
     */
    public function handle()
    {
        //NBP only for PLN
        if(Currency::select('code')->find(setting('general.default_currency'))->code=='PLN') {
            $endDate = Carbon::now()->startOfDay();
            $startDate = Carbon::now()->subDays(7);
            $currencyAverages = CurrencyAverageRatesService::new();
            $currencies = Currency::select('code', 'id')
-                ->whereNot('code', '=','PLN')
+                ->where('code', '!=', 'PLN')
                ->whereIn('id', setting('general.currencies'))
                ->get();
            $plnCurrency = Currency::whereCode('PLN')->firstOrFail();
            foreach ($currencies as $currency) {
                $dateIterator = clone $startDate;
                for(; $dateIterator <= $endDate; $dateIterator->addDay()) {
                    $iDate = $dateIterator->format('Y-m-d');
                    $this->info(sprintf('NBP averages rates for %s', $iDate));
                    try {
                        $rate = $currencyAverages
                            ->fromDay($iDate)
                            ->fromTable('A')
                            ->getRate($currency->code);

                        ExchangeRate::updateOrCreate([
                            'type' => 'Auto',
                            'currency_id' => $currency->id,
                            'base_currency_id' => $plnCurrency->id,
                            'date' => $iDate,
                        ], [
                            'value' => $rate->getValue(),
                            'source' => 'NBP'
                        ]);
                        $this->info(sprintf("%f %s/PLN", $rate->getValue(), $currency->code));
                    } catch (\Exception $e) {
                        $this->error($e->getMessage());
                        $this->error(sprintf("cannot fetch rates for %s, for date %s", $currency->code, $iDate));
                    }
                }
            }
        }
    }

    /**
     * Determines the most recent workday before today, excluding weekends and Polish public holidays.
     *
     * Calculates yesterday's date and iteratively subtracts days if the date falls on a weekend or matches a public holiday in Poland, ensuring the returned date is a valid workday.
     *
     * @return \Carbon\Carbon The last valid workday date.
     */
    private function getLastWorkday()
    {
        $date = Carbon::yesterday();
        $year = $date->year;

        $holidays = Yasumi::create('Poland', $year);
        $holidayDates = array_map(function ($holidayDate) {
            return ($holidayDate instanceof \DateTimeInterface)
                ? $holidayDate->format('Y-m-d')
                : (string) $holidayDate;
        }, $holidays->getHolidayDates());

        while (
            $date->isWeekend() ||
            in_array($date->format('Y-m-d'), $holidayDates, true)
        ) {
            $date->subDay();
        }

        return $date;
    }
}
