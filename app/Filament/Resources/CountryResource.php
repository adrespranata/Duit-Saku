<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CountryResource\Pages;
use App\Filament\Resources\CountryResource\RelationManagers\StatesRelationManager;
use App\Models\Country;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CountryResource extends Resource
{
    protected static ?string $model = Country::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'System Management';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 5 ? 'warning' : 'success';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Country Details')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->maxLength(3),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phonecode')
                            ->tel()
                            ->maxLength(255),
                    ])->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phonecode')
                    ->label('Phone Code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn($query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn($query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['from'] ?? null) {
                            $indicators[] = Indicator::make('Created from ' . Carbon::parse($data['from'])->toFormattedDateString())
                                ->removeField('from');
                        }

                        if ($data['until'] ?? null) {
                            $indicators[] = Indicator::make('Created until ' . Carbon::parse($data['until'])->toFormattedDateString())
                                ->removeField('until');
                        }

                        return $indicators;
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Country Information')
                    ->schema([
                        TextEntry::make('code')
                            ->label('Code'),
                        TextEntry::make('name')
                            ->label('Name'),
                        TextEntry::make('phonecode')
                            ->label('Phone Code'),
                    ])
                    ->columns(3)
                    ->collapsed()
            ]);
    }

    public static function getRelations(): array
    {
        return [
            StatesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCountries::route('/'),
            'create' => Pages\CreateCountry::route('/create'),
            // 'view' => Pages\ViewCountry::route('/{record}'),
            'edit' => Pages\EditCountry::route('/{record}/edit'),
        ];
    }
}
