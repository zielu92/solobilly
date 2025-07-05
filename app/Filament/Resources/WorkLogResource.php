<?php

namespace App\Filament\Resources;

use App\Enum\TypeOfContract;
use App\Filament\Resources\WorkLogResource\Pages;
use App\Models\Buyer;
use App\Models\WorkLog;
use Filament\Forms;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;


class WorkLogResource extends Resource
{
    public static function getNavigationLabel(): string
    {
        return __('worklogs.worklog');
    }

    public static function getModelLabel(): string
    {
        return __('worklogs.worklogs');
    }

    public static function getPluralModelLabel(): string
    {
        return __('worklogs.worklogs');
    }

    protected static ?string $model = WorkLog::class;

    public static function form(Form $form): Form
    {
        // Determine if we are in edit mode
        $isEditMode = $form->getOperation() === 'edit';

        $formSchema = [
            Select::make('buyer_id')
                ->label(__('worklogs.buyer'))
                ->relationship('buyer', 'name')
                ->required()
                ->live()
                ->afterStateUpdated(fn () => null) // Trigger form updates
                ->columnSpanFull(),
            MarkdownEditor::make('description')
                ->label(__('worklogs.description'))
                ->columnSpanFull(),
        ];

        if ($isEditMode) {
            $formSchema[] = Forms\Components\DateTimePicker::make('start')
                ->seconds(false)
                ->label(__('worklogs.start'))
                ->required();

            $formSchema[] = Forms\Components\DateTimePicker::make('end')
                ->seconds(false)
                ->label(__('worklogs.end'))
                ->required()
                ->afterOrEqual('start')
                ->validationMessages([
                    'after_or_equal' => __('worklogs.end_after_start'),
                ])
                ->live()
                ->visible(fn (Get $get): bool => self::isTimeBased($get('buyer_id')));


            $formSchema[] = Forms\Components\TextInput::make('unit_amount')
                ->label(__('worklogs.units'))
                ->numeric()
                ->required()
                ->live()
                ->visible(fn (Get $get): bool => !self::isTimeBased($get('buyer_id')));
        }
        else {
            // Create mode - use repeater
            $formSchema[] = Repeater::make('items')
                ->label(__('worklogs.item'))
                ->schema([

                    Forms\Components\DateTimePicker::make('start')
                        ->seconds(false)
                        ->default(now()->subHours(8))
                        ->label(__('worklogs.start'))
                        ->required(),

                    Forms\Components\DateTimePicker::make('end')
                        ->seconds(false)
                        ->label(__('worklogs.end'))
                        ->required()
                        ->default(now())
                        ->afterOrEqual('start')
                        ->validationMessages([
                            'after_or_equal' => __('worklogs.end_after_start'),
                        ])
                        ->live()
                        ->visible(fn (Get $get): bool => self::isTimeBased($get('../../buyer_id'))),

                    Forms\Components\TextInput::make('unit_amount')
                        ->label(__('worklogs.units'))
                        ->numeric()
                        ->required()
                        ->live()
                        ->postfix(fn (Get $get): string => Buyer::find($get('../../buyer_id'))->unit_type)
                        ->visible(fn (Get $get): bool => !self::isTimeBased($get('../../buyer_id'))),
                ])
                ->columnSpanFull()
                ->defaultItems(1)
                ->minItems(1);
        }

        return $form->schema($formSchema);
    }

    /**
     * Check if the selected buyer has a time-based contract
     */
    private static function isTimeBased(?string $buyerId): bool
    {
        if (!$buyerId) {
            return true;
        }

        $buyer = \App\Models\Buyer::find($buyerId);
        if (!$buyer) {
            return true;
        }

        if (!$buyer->contract_type) {
            return true;
        }

        return $buyer->contract_type !== TypeOfContract::OTHER;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('buyer.name')
                    ->label(__('worklogs.buyer'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start')
                    ->label(__('worklogs.start'))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end')
                    ->label(__('worklogs.end'))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration')
                    ->label(__('worklogs.duration'))
                    ->formatStateUsing(function (string $state, $record): string {
                        // Check if unit_amount exists and is not null
                        if (!is_null($record->unit_amount)) {
                            $unitType = $record->buyer->unit_type;
                            return sprintf('%s %s', $record->unit_amount, $unitType);
                        }

                        // Fallback to duration in hours
                        return sprintf('%s h', $state);
                    })
                    ->alignRight()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('worklogs.description'))
                    ->limit(50)
                    ->searchable(),
            ])
            ->defaultSort('start')
            ->filters([
                Tables\Filters\SelectFilter::make('buyer')
                    ->relationship('buyer', 'name')
                ->label(__('worklogs.buyer')),
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('start_date')
                        ->label(__('worklogs.start')),
                        Forms\Components\DatePicker::make('end_date')
                        ->label(__('worklogs.end')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start', '>=', $date),
                            )
                            ->when(
                                $data['end_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('end', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                ReplicateAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkLogs::route('/'),
            'create' => Pages\CreateWorkLog::route('/create'),
            'edit' => Pages\EditWorkLog::route('/{record}/edit'),
        ];
    }
}
