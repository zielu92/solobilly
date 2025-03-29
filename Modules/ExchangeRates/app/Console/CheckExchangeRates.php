<?php

namespace Modules\ExchangeRates\Console;

use App\Models\Currency;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\ExchangeRates\Models\ExchangeRate;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use MaciejSz\Nbp\Service\CurrencyAverageRatesService;
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
            $rateDate = $this->getLastWorkday()->format('Y-m-d');
            $this->info('NBP averages rates for '.$rateDate);
            $currencyAverages = CurrencyAverageRatesService::new();
            $currencies = Currency::select('code')->whereIn('id', setting('general.currencies'))->get();
            foreach ($currencies as $currency) {
                if($currency->code=='PLN') continue;
                try {
                    $rate = $currencyAverages
                        ->fromDay($rateDate)
                        ->fromTable('A')
                        ->getRate($currency->code);
                    ExchangeRate::updateOrCreate([
                        'type'          => 'Auto',
                        'currency'      => $currency->code,
                        'base_currency' => 'PLN',
                        'date'          => $rateDate,
                    ],[
                        'value'         => $rate->getValue(),
                        'source'        => 'NBP'
                    ]);
                    $this->info($rate->getValue(). ' '.$currency->code.'/PLN');
                } catch (\Exception $e) {
                    $this->error("cannot fetch rates for ".$currency->code);
                }

            }
        }
    }

    /**
     * Returns the last workday date, adjusting for weekends.
     *
     * This method computes yesterday's date and, if it falls on Saturday or Sunday,
     * adjusts the result to the preceding Friday. Note: Holidays are not considered.
     *
     * @return \Carbon\Carbon The last workday date.
     */
    private function getLastWorkday()
    {
        //todo: consider also a holidays...
        $yesterday = Carbon::yesterday();

        // If yesterday was Saturday, get last Friday
        if ($yesterday->isSaturday()) {
            return $yesterday->subDays(1);
        }

        // If yesterday was Sunday, get last Friday
        if ($yesterday->isSunday()) {
            return $yesterday->subDays(2);
        }

        return $yesterday;
    }
}
