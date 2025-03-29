<?php

use Modules\ExchangeRates\Models\ExchangeRate;

// Set up a test suite for the ExchangeRate model
uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->group('exchangerates', 'model');

// Test creating an exchange rate record
test('can create exchange rate', function () {
    $exchangeRate = ExchangeRate::create([
        'type' => 'Auto',
        'date' => '2025-03-23',
        'value' => 4.32,
        'currency' => 'EUR',
        'base_currency' => 'PLN',
        'source' => 'NBP'
    ]);

    expect($exchangeRate)->toBeInstanceOf(ExchangeRate::class)
        ->and($exchangeRate->type)->toBe('Auto')
        ->and($exchangeRate->date->format('Y-m-d'))->toBe('2025-03-23')
        ->and($exchangeRate->value)->toBe(4.32)
        ->and($exchangeRate->currency)->toBe('EUR')
        ->and($exchangeRate->base_currency)->toBe('PLN')
        ->and($exchangeRate->source)->toBe('NBP');

    $this->assertDatabaseHas('exchange_rates', [
        'id' => $exchangeRate->id,
        'type' => 'Auto',
        'value' => 4.32,
        'currency' => 'EUR',
        'base_currency' => 'PLN',
        'source' => 'NBP'
    ]);
});

// Test model fillable attributes
test('has correct fillable attributes', function () {
    $exchangeRate = new ExchangeRate();

    expect($exchangeRate->getFillable())->toBe([
        'type', 'date', 'value', 'currency', 'base_currency', 'source'
    ]);
});

// Test casting attributes
test('casts attributes correctly', function () {
    $exchangeRate = ExchangeRate::create([
        'type' => 'Auto',
        'date' => '2025-03-23',
        'value' => '4.32', // String value should be cast to float
        'currency' => 'EUR',
        'base_currency' => 'PLN',
        'source' => 'NBP'
    ]);

    expect($exchangeRate->date)->toBeInstanceOf(\Illuminate\Support\Carbon::class)
        ->and($exchangeRate->value)->toBeFloat()
        ->and($exchangeRate->value)->toBe(4.32);
});

// Test default source value
test('uses NBP as default source', function () {
    $exchangeRate = ExchangeRate::create([
        'type' => 'Auto',
        'date' => '2025-03-23',
        'value' => 4.32,
        'currency' => 'EUR',
        'base_currency' => 'PLN',
    ]);

    expect($exchangeRate->source)->toBe('NBP');
});
