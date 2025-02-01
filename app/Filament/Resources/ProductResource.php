<?php

namespace App\Filament\Resources;

use App\Filament\Imports\ProductImporter;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('messages.name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('code')
                    ->label(__('messages.code'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('category_id')
                    ->label(__('messages.the_category'))
                    ->relationship('category', 'name')
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('messages.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->label(__('messages.code'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label(__('messages.the_category'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('messages.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('messages.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\ImportAction::make(__('messages.import_products'))
                    ->importer(ProductImporter::class)
                    ->color('info'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make(__('messages.change_category'))
                        ->label(__('messages.change_category'))
                        ->icon('heroicon-o-pencil')
                        ->color('primary')
                        ->form([
                            Forms\Components\Select::make('category_id')
                                ->label('New Category')
                                ->options(\App\Models\Category::pluck('name', 'id'))
                                ->required(),
                        ])
                        ->action(function (array $data, $records) {
                            foreach ($records as $record) {
                                $record->update(['category_id' => $data['category_id']]);
                            }

                            Notification::make()
                                ->title(__('messages.category_updated_successfully'))
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageProducts::route('/'),
        ];
    }
    public static function getLabel(): ?string
    {
        return __('messages.products');
    }
    public static function getModelLabel(): string
    {
        return __('messages.product');
    }
    public static function getPluralLabel(): string
    {
        return __('messages.products');
    }
    public static function getTitleCasePluralModelLabel(): string
    {
        return __('messages.the_products');
    }
}
