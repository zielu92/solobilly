<?php

use App\Models\Currency;
use Modules\ExchangeRates\Models\ExchangeRate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

// Set up a test suite for feature tests
uses(Tests\TestCase::class, RefreshDatabase::class)
    ->group('exchangerates', 'feature');

// Test exchange rate listing
test('can view list of exchange rates', function () {
    // Create some test data
    ExchangeRate::create([
        'type' => 'Auto',
        'date' => '2025-03-23',
        'value' => 4.32,
        'currency' => 'EUR',
        'base_currency' => 'PLN',
        'source' => 'NBP'
    ]);

    ExchangeRate::create([
        'type' => 'Auto',
        'date' => '2025-03-23',
        'value' => 3.81,
        'currency' => 'USD',
        'base_currency' => 'PLN',
        'source' => 'NBP'
    ]);

    // Assuming authentication might be required
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('exchangerates.index'));

    $response->assertStatus(200);
    $response->assertViewIs('exchangerates::index');
    // Further assertions would depend on the view implementation
});

// Test exchange rate creation flow
test('can create exchange rate through web interface', function () {
    // Assuming authentication might be required
    $user = User::factory()->create(['is_admin' => true]);
    $this->actingAs($user);

    // First visit create form
    $response = $this->get(route('exchangerates.create'));
    $response->assertStatus(200);

    // Then submit the form
    $response = $this->post(route('exchangerates.store'), [
        'date' => '2025-03-23',
        'value' => 4.32,
        'currency' => 'EUR',
        'base_currency' => 'PLN',
        'source' => 'User input'
    ]);

    $response->assertRedirect(route('exchangerates.index'));
    $response->assertSessionHas('success');

    // Verify record was created
    $this->assertDatabaseHas('exchange_rates', [
        'type' => 'Manual',
        'date' => '2025-03-23',
        'value' => 4.32,
        'currency' => 'EUR',
        'base_currency' => 'PLN',
        'source' => 'User input'
    ]);
});

// Test exchange rate edit flow
test('can edit exchange rate through web interface', function () {
    // Create test data
    $exchangeRate = ExchangeRate::create([
        'type' => 'Auto',
        'date' => '2025-03-23',
        'value' => 4.32,
        'currency' => 'EUR',
        'base_currency' => 'PLN',
        'source' => 'NBP'
    ]);

    // Assuming authentication might be required
    $user = User::factory()->create(['is_admin' => true]);
    $this->actingAs($user);

    // First visit edit form
    $response = $this->get(route('exchangerates.edit', $exchangeRate->id));
    $response->assertStatus(200);

    // Then submit the form
    $response = $this->put(route('exchangerates.update', $exchangeRate->id), [
        'date' => '2025-03-24',
        'value' => 4.35,
        'currency' => 'EUR',
        'base_currency' => 'PLN',
        'source' => 'Manual update'
    ]);

    $response->assertRedirect(route('exchangerates.index'));
    $response->assertSessionHas('success');

    // Verify record was updated
    $this->assertDatabaseHas('exchange_rates', [
        'id' => $exchangeRate->id,
        'date' => '2025-03-24',
        'value' => 4.35,
        'source' => 'Manual update'
    ]);
});

// Test API endpoints if implemented
test('api returns exchange rates', function () {
    // Create test data
    ExchangeRate::create([
        'type' => 'Auto',
        'date' => '2025-03-23',
        'value' => 4.32,
        'currency' => 'EUR',
        'base_currency' => 'PLN',
        'source' => 'NBP'
    ]);

    // Create user with sanctum token if required
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    // Test API response
    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->getJson('/api/v1/exchangerates');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id', 'type', 'date', 'value', 'currency', 'base_currency', 'source'
                ]
            ]
        ]);
});

// Test command execution through schedule
test('scheduled command creates exchange rates', function () {
    // Create required currencies
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

    // Mock the scheduler and run it
    $this->mock(\Illuminate\Console\Scheduling\Schedule::class, function ($mock) {
        $mock->shouldReceive('command')
            ->with('exchange-rates:check')
            ->andReturnSelf();
        $mock->shouldReceive('dailyAt')
            ->with('09:00')
            ->andReturnSelf();
    });

    $this->artisan('schedule:run');

    // Verify the command was executed and data was stored
    expect(ExchangeRate::count())->toBeGreaterThan(0);
});
