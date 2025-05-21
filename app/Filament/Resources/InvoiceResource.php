<?php

namespace App\Filament\Resources;

use App\Models\Cost;
use App\Models\Currency;
use Filament\Forms;
use Filament\Tables;
use App\Models\Invoice;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use App\Filament\Resources\InvoiceResource\Pages;
use Illuminate\Support\Facades\URL;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    public static function getNavigationLabel(): string
    {
        return __('invoices.invoices');
    }

    public static function getModelLabel(): string
    {
        return __('invoices.invoices');
    }

    public static function getPluralModelLabel(): string
    {
        return __('invoices.invoices');
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->columns(12)
            ->schema(Invoice::getForm());
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no')
                    ->label(__('invoices.invoice_no'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('invoiceBuyer.name')
                    ->label(__('invoices.buyer')),

                Tables\Columns\TextColumn::make('type')
                    ->label(__('invoices.type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __('invoices.types.' . $state))
                    ->sortable(),

                Tables\Columns\IconColumn::make('payment_status')
                    ->label(__('invoices.payment_status'))
                    ->alignCenter()
                    ->color(function($state){
                        return $state=="paid" ? 'success' : 'danger';
                    })
                    ->icon(function($state){
                        return $state=="paid" ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle';
                    }),

                Tables\Columns\TextColumn::make('grand_total_gross')
                    ->label(__('invoices.grand_total_gross'))
                    ->alignEnd()
                    ->numeric()
                    ->suffix(function($record) {
                        return ' ' . Currency::find($record->currency_id)->code;
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('invoices.created'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Action::make('Download PDF')
                    ->icon('heroicon-o-document-text')
                    ->label(__('invoices.download'))
                    ->url(fn ($record) => URL::route('invoices.show', $record->id))
                    ->openUrlInNewTab(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label(__('invoices.payment_status'))
                    ->options([
                        'paid' => __('invoices.paid'),
                        'notpaid' => __('invoices.notpaid'),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [

        ];
    }


    public static function getNavigationBadge(): ?string
    {
        $unpaid =  Invoice::where('payment_status', 'not_paid')->count();
        return $unpaid > 0 ? $unpaid : null;
    }

    public static function getNavigationBadgeColor(): string | array | null
    {
        return 'danger';
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
