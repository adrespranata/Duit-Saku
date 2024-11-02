<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'All' => Tab::make()
                ->badge(User::count())
                ->modifyQueryUsing(fn(Builder $query): Builder => $query),
            'Verified' => Tab::make()
                ->badge(User::where('email_verified_at', '!=', null)->count())
                ->modifyQueryUsing(fn(Builder $query): Builder => $query->whereNotNull('email_verified_at')),
            'Unverified' => Tab::make()
                ->badge(User::where('email_verified_at', null)->count())
                ->modifyQueryUsing(fn(Builder $query): Builder => $query->whereNull('email_verified_at')),
        ];
    }
}
