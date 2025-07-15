<?php

namespace App\Models;

use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    /** @use HasFactory<\Database\Factories\TaxFactory> */
    use HasFactory;

    protected $fillable = ['name', 'rate'];

    public static function getForm(): array {
        return [
            TextInput::make('name')
                ->label(__('taxes.name'))
                ->required()
                ->maxLength(255),
            TextInput::make('rate')
                ->label(__('taxes.rate'))
                ->required()
                ->minValue(0)
                ->numeric(),
        ];
    }
}
