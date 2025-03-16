<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Buyer extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'company_name',
        'email',
        'phone',
        'address',
        'city',
        'postal_code',
        'country',
        'nip',
        'regon',
        'krs',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    public static function getForm(): array
    {
        return [
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            TextInput::make('company_name')
                ->maxLength(255)
                ->default(null),
            TextInput::make('email')
                ->email()
                ->maxLength(255)
                ->default(null),
            TextInput::make('phone')
                ->tel()
                ->maxLength(255)
                ->default(null),
            TextInput::make('address')
                ->maxLength(255)
                ->default(null),
            TextInput::make('city')
                ->maxLength(255)
                ->default(null),
            TextInput::make('postal_code')
                ->maxLength(255)
                ->default(null),
            TextInput::make('country')
                ->maxLength(255)
                ->default(null),
            TextInput::make('nip')
                ->maxLength(255)
                ->default(null),
            TextInput::make('regon')
                ->maxLength(255)
                ->default(null),
            TextInput::make('krs')
                ->maxLength(255)
                ->default(null),
        ];
    }
}
