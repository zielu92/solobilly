<?php

use Modules\ExchangeRates\Models\ExchangeRate;
use Illuminate\Foundation\Testing\RefreshDatabase;

// Set up a test suite for the ExchangeRatesController
uses(Tests\TestCase::class, RefreshDatabase::class)
    ->group('exchangerates', 'controller');

// Test index route
test('index route returns correct view', function () {
    $response = $this->get(route('exchangerates.index'));

    $response->assertStatus(200);
    $response->assertViewIs('exchangerates::index');
});

// Test create route
test('create route returns correct view', function () {
    $response = $this->get(route('exchangerates.create'));

    $response->assertStatus(200);
    $response->assertViewIs('exchangerates::create');
});

// Test storing a new exchange rate
test('can store new exchange rate', function () {
    $data = [
        'date' => '2025-03-23',
        'value' => 4.32,
        'currency' => 'EUR',
        'base_currency' => 'PLN',
        'source' => 'User input',
    ];

    $response = $this->post(route('exchangerates.store'), $data);

    $response->assertRedirect(route('exchangerates.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('exchange_rates', [
        'type' => 'Manual', // Controller sets this value
        'date' => '2025-03-23',
        'value' => 4.32,
        'currency' => 'EUR',
        'base_currency' => 'PLN',
        'source' => 'User input',
    ]);
});

// Test validation errors on store
test('validates required fields when storing', function () {
    $response = $this->post(route('exchangerates.store'), []);

    $response->assertSessionHasErrors(['date', 'value', 'currency', 'base_currency', 'source']);
});

// Test validation error when currency and base_currency are the same
test('validates currency and base_currency must be different', function () {
    $data = [
        'date' => '2025-03-23',
        'value' => 4.32,
        'currency' => 'EUR',
        'base_currency' => 'EUR', // Same as currency
        'source' => 'User input',
    ];

    $response = $this->post(route('exchangerates.store'), $data);

    $response->assertSessionHasErrors('base_currency');
});

// Test show route
test('show route returns correct view', function () {
    $response = $this->get(route('exchangerates.show', 1));

    $response->assertStatus(200);
    $response->assertViewIs('exchangerates::show');
});

// Test edit route
test('edit route returns correct view', function () {
    $response = $this->get(route('exchangerates.edit', 1));

    $response->assertStatus(200);
    $response->assertViewIs('exchangerates::edit');
});

// Test updating an exchange rate
test('can update exchange rate', function () {
    // Create an exchange rate to update
    $exchangeRate = ExchangeRate::create([
        'type' => 'Auto',
        'date' => '2025-03-23',
        'value' => 4.32,
        'currency' => 'EUR',
        'base_currency' => 'PLN',
        'source' => 'NBP',
    ]);

    $data = [
        'date' => '2025-03-24',
        'value' => 4.35,
        'currency' => 'EUR',
        'base_currency' => 'PLN',
        'source' => 'Manual update',
    ];

    $response = $this->put(route('exchangerates.update', $exchangeRate->id), $data);

    $response->assertRedirect(route('exchangerates.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('exchange_rates', [
        'id' => $exchangeRate->id,
        'date' => '2025-03-24',
        'value' => 4.35,
        'source' => 'Manual update',
    ]);
});

// Test validation errors on update
test('validates required fields when updating', function () {
    $exchangeRate = ExchangeRate::create([
        'type' => 'Auto',
        'date' => '2025-03-23',
        'value' => 4.32,
        'currency' => 'EUR',
        'base_currency' => 'PLN',
        'source' => 'NBP',
    ]);

    $response = $this->put(route('exchangerates.update', $exchangeRate->id), []);

    $response->assertSessionHasErrors(['date', 'value', 'currency', 'base_currency', 'source']);
});
