<?php

namespace App\Models;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'color',
        'is_tax_related',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'is_tax_related' => 'boolean',
    ];

    public static function getForm(): array
    {
        return [
            TextInput::make('name')
                ->label(__('costs.name'))
                ->required()
                ->maxLength(255),
            ColorPicker::make('color')
                ->label(__('costs.color'))
                ->default(null),
            Textarea::make('description')
                ->label(__('costs.description'))
                ->columnSpanFull(),
            Toggle::make('is_tax_related')
                ->label(__('costs.tax_related'))
                ->required(),
        ];
    }
}
