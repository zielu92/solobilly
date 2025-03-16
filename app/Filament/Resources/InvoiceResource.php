<?php

namespace App\Filament\Resources;

use App\Models\Currency;
use Filament\Forms;
use Filament\Tables;
use App\Models\Invoice;
use Filament\Forms\Get;
use App\Models\InvoiceItem;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\InvoiceResource\Pages;
use Illuminate\Support\Facades\URL;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;
    protected static ?string $navigationGroup = 'Invoices';
    protected static ?string $navigationLabel = 'Invoices';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->columns(12)
            ->schema(Invoice::getForm());
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no')
                    ->label('Invoice No.')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('invoiceBuyer.name')
                    ->label('Buyer'),

                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->sortable(),

                Tables\Columns\IconColumn::make('payment_status')
                    ->label('Payment Status')
                    ->alignCenter()
                    ->color(function($state){
                        return $state=="paid" ? 'success' : 'danger';
                    })
                    ->icon(function($state){
                        return $state=="paid" ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle';
                    }),

                Tables\Columns\TextColumn::make('grand_total_gross')
                    ->label('Grand Total Gross')
                    ->alignEnd()
                    ->numeric()
                    ->suffix(function($record) {
                        return ' ' . Currency::find($record->currency_id)->code;
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Action::make('Download PDF')
                    ->icon('heroicon-o-document-text')
                    ->label('Download')
                    ->url(fn ($record) => URL::route('invoices.show', $record->id))
                    ->openUrlInNewTab(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'paid' => 'Paid',
                        'notpaid' => 'Not paid',
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [

        ];
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
