<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Livewire\Notifications;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\UserResource\Pages;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 10 ? 'warning' : 'success';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('username')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->required()
                            ->maxLength(255)
                            ->revealable(),
                        Forms\Components\TextInput::make('Confirm Password')
                            ->password()
                            ->required()
                            ->revealable()
                            ->same('password')
                            ->maxLength(255),
                        Forms\Components\Select::make('role_id')
                            ->preload()
                            ->required()
                            ->searchable()
                            ->label('Role')
                            ->placeholder('Select Role')
                            ->relationship('roles', 'name')
                    ])
                    ->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->color(fn(User $record): string => match ($record->is_active) {
                        1 => 'success',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn(User $record) => match ($record->is_active) {
                        1 => 'Active',
                        default => 'Inactive',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton(),
                Tables\Actions\DeleteAction::make()
                    ->iconButton(),
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->action(function (User $record) {
                        $record->is_active = true;
                        $record->save();

                        Notification::make()
                            ->success()
                            ->title('User has been approved')
                            ->body('User has been approved successfully.')
                            ->send();
                    })
                    ->color('success')
                    ->requiresConfirmation()
                    ->icon('heroicon-o-check-circle')
                    ->iconButton()
                    ->visible(fn(User $record) => $record->is_active === 0),
                Tables\Actions\Action::make('suspend')
                    ->label('Suspend')
                    ->action(function (User $record) {
                        $record->is_active = false;
                        $record->save();

                        Notification::make()
                            ->success()
                            ->title('User has been suspended')
                            ->body('User has been suspended successfully.')
                            ->send();
                    })->color('danger')
                    ->requiresConfirmation()
                    ->icon('heroicon-o-x-circle')
                    ->iconButton()
                    ->visible(fn(User $record) => $record->is_active === 1),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
