<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\AccountTypes;
use App\Filament\Custom;
use App\Filament\Resources\AccountResource\Pages;
use App\Models\Account;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Custom\Components\Logo::make('logo_path')
                    ->label(__('accounts.labels.logo'))
                    ->directory('accounts'),
                Forms\Components\TextInput::make('name')
                    ->label(__('accounts.labels.name'))
                    ->required()
                    ->minLength(3)
                    ->maxLength(255),
                Custom\Components\SelectEnum::make('type')
                    ->label(__('accounts.labels.type'))
                    ->enum(AccountTypes::class)
                    ->required(),
                Custom\Components\Money::make('balance')
                    ->label(__('accounts.labels.balance'))
                    ->required(),
                Custom\Components\Currency::make('currency')
                    ->label(__('accounts.labels.currency'))
                    ->required(),
                Custom\Components\Iban::make('iban')
                    ->label(__('accounts.labels.iban')),
                Custom\Components\Swift::make('swift')
                    ->label(__('accounts.labels.swift')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Custom\Columns\Logo::make('logo_path')
                    ->label(__('accounts.labels.logo')),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('accounts.labels.name'))
                    ->sortable()
                    ->searchable(),
                Custom\Columns\BadgeEnum::make('type')
                    ->label(__('accounts.labels.type'))
                    ->enum(AccountTypes::class),
                Custom\Columns\Money::make('balance')
                    ->label(__('accounts.labels.balance')),
                Custom\Columns\Iban::make('iban')
                    ->label(__('accounts.labels.iban'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('swift')
                    ->label(__('accounts.labels.swift'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('accounts.labels.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('accounts.labels.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        ...collect(AccountTypes::cases())->mapWithKeys(
                            fn (AccountTypes $status): array => [$status->value => $status->getLabel()]
                        ),
                    ])
                    ->multiple()
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make(
                    [
                        Tables\Actions\EditAction::make(),
                        Tables\Actions\DeleteAction::make(),
                    ]
                ),
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
            // Preserve brace position.
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccounts::route('/'),
            'create' => Pages\CreateAccount::route('/create'),
            'edit' => Pages\EditAccount::route('/{record}/edit'),
        ];
    }
}
