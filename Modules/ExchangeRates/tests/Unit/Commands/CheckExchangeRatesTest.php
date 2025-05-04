<?php

use Carbon\Carbon;
use App\Models\Currency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use MaciejSz\Nbp\Service\CurrencyAverageRatesService;
use MaciejSz\Nbp\ValueObject\Rate as NbpRate;
use Modules\ExchangeRates\Console\CheckExchangeRates;
use Modules\ExchangeRates\Models\ExchangeRate;



// Set up a test suite for the CheckExchangeRates command
uses(Tests\TestCase::class, RefreshDatabase::class)
    ->group('exchangerates', 'command')
    ->beforeEach(function () {
        // Mock settings
        $this->app->bind('setting', function () {
            $this->seed();
            return new class {
                public function __invoke($key)
                {
                    if ($key === 'general.default_currency') {
                        return 1; // PLN currency ID
                    } elseif ($key === 'general.currencies') {
                        return [1, 2, 3]; // IDs for PLN, EUR, USD
                    }
                    return null;
                }
            };
        });
    })
    ->afterEach(function () {
        Mockery::close();
    });

// Test getting the last workday
test('calculates correct last workday', function () {
    $command = new CheckExchangeRates();

    // Use reflection to access private method
    $reflector = new ReflectionClass($command);
    $method = $reflector->getMethod('getLastWorkday');
    $method->setAccessible(true);

    // Test Monday (yesterday was Sunday)
    Carbon::setTestNow(Carbon::parse('2025-03-24')); // Monday
    $lastWorkday = $method->invoke($command);
    expect($lastWorkday->format('Y-m-d'))->toBe('2025-03-21'); // Friday

    // Test Sunday (yesterday was Saturday)
    Carbon::setTestNow(Carbon::parse('2025-03-23')); // Sunday
    $lastWorkday = $method->invoke($command);
    expect($lastWorkday->format('Y-m-d'))->toBe('2025-03-21'); // Friday

    // Test Saturday (yesterday was Friday)
    Carbon::setTestNow(Carbon::parse('2025-03-22')); // Saturday
    $lastWorkday = $method->invoke($command);
    expect($lastWorkday->format('Y-m-d'))->toBe('2025-03-21'); // Friday

    // Test weekday (yesterday was weekday)
    Carbon::setTestNow(Carbon::parse('2025-03-21')); // Friday
    $lastWorkday = $method->invoke($command);
    expect($lastWorkday->format('Y-m-d'))->toBe('2025-03-20'); // Thursday
});

// Test fetching and storing exchange rates
test('fetches and stores exchange rates successfully', function () {
    // Create currency records
    Currency::create(['id' => 1, 'code' => 'PLN', 'name' => 'Polish Zloty']);
    Currency::create(['id' => 2, 'code' => 'EUR', 'name' => 'Euro']);

    // Mock the NBP service
    $mockRate = Mockery::mock(NbpRate::class);
    $mockRate->shouldReceive('getValue')->andReturn(4.32);

    $mockService = Mockery::mock('overload:' . CurrencyAverageRatesService::class);
    $mockService->shouldReceive('new')->andReturnSelf();
    $mockService->shouldReceive('fromDay')->with(Mockery::type('string'))->andReturnSelf();
    $mockService->shouldReceive('fromTable')->with('A')->andReturnSelf();
    $mockService->shouldReceive('getRate')->with('EUR')->andReturn($mockRate);

    // Execute the command
    $this->artisan('exchange-rates:check')
        ->expectsOutput(Mockery::pattern('/NBP averages rates for \d{4}-\d{2}-\d{2}/'))
        ->expectsOutput('4.32 EUR/PLN')
        ->assertExitCode(0);

    // Verify stored data
    expect(ExchangeRate::count())->toBe(1);

    $storedRate = ExchangeRate::first();
    expect($storedRate->type)->toBe('Auto')
        ->and($storedRate->currency)->toBe('EUR')
        ->and($storedRate->base_currency)->toBe('PLN')
        ->and($storedRate->value)->toBe(4.32)
        ->and($storedRate->source)->toBe('NBP');
});

// Test error handling
test('handles exceptions when fetching rates', function () {
    // Create currency records
    Currency::create(['id' => 1, 'code' => 'PLN', 'name' => 'Polish Zloty']);
    Currency::create(['id' => 3, 'code' => 'USD', 'name' => 'US Dollar']);

    // Mock the service to throw exception
    $mockService = Mockery::mock('overload:' . CurrencyAverageRatesService::class);
    $mockService->shouldReceive('new')->andReturnSelf();
    $mockService->shouldReceive('fromDay')->with(Mockery::type('string'))->andReturnSelf();
    $mockService->shouldReceive('fromTable')->with('A')->andReturnSelf();
    $mockService->shouldReceive('getRate')->with('USD')->andThrow(new \Exception('API Error'));

    // Execute the command
    $this->artisan('exchange-rates:check')
        ->expectsOutput(Mockery::pattern('/NBP averages rates for \d{4}-\d{2}-\d{2}/'))
        ->expectsOutput('cannot fetch rates for USD')
        ->assertExitCode(0);

    // Verify no data was stored
    expect(ExchangeRate::count())->toBe(0);
});

// Test skipping when default currency is not PLN
test('skips processing when default currency is not PLN', function () {
    // Override the setting mock
    $this->app->bind('setting', function () {
        return new class {
            public function __invoke($key)
            {
                if ($key === 'general.default_currency') {
                    return 2; // EUR currency ID
                }
                return null;
            }
        };
    });

    Currency::create(['id' => 2, 'code' => 'EUR', 'name' => 'Euro']);

    // Execute the command
    $this->artisan('exchange-rates:check')
        ->assertExitCode(0);

    // Verify no rates were stored
    expect(ExchangeRate::count())->toBe(0);
});

// Test update or create functionality
test('updates existing exchange rate if one exists for same date and currency', function () {
    // Create currency records
    Currency::create(['id' => 1, 'code' => 'PLN', 'name' => 'Polish Zloty']);
    Currency::create(['id' => 2, 'code' => 'EUR', 'name' => 'Euro']);

    // Create an existing exchange rate
    $date = Carbon::yesterday()->format('Y-m-d');
    ExchangeRate::create([
        'type' => 'Auto',
        'date' => $date,
        'value' => 4.30, // Old value
        'currency' => 'EUR',
        'base_currency' => 'PLN',
        'source' => 'NBP'
    ]);

    // Mock the NBP service with new value
    $mockRate = Mockery::mock(NbpRate::class);
    $mockRate->shouldReceive('getValue')->andReturn(4.32); // New value

    $mockService = Mockery::mock('overload:' . CurrencyAverageRatesService::class);
    $mockService->shouldReceive('new')->andReturnSelf();
    $mockService->shouldReceive('fromDay')->with(Mockery::type('string'))->andReturnSelf();
    $mockService->shouldReceive('fromTable')->with('A')->andReturnSelf();
    $mockService->shouldReceive('getRate')->with('EUR')->andReturn($mockRate);

    // Execute the command
    $this->artisan('exchange-rates:check')
        ->expectsOutput('4.32 EUR/PLN')
        ->assertExitCode(0);

    // Verify only one record exists and it's updated
    expect(ExchangeRate::count())->toBe(1);

    $updatedRate = ExchangeRate::first();
    expect($updatedRate->value)->toBe(4.32); // Value should be updated
});
