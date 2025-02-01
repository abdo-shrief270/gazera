<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('invoice_number')
                    ->label(__('messages.invoice_number'))
                    ->disabled()
                    ->formatStateUsing(fn()=>sprintf("%06d", Invoice::orderBy('id', 'desc')->first()?->invoice_number+1))
                    ->required()
                    ->columnSpan([
                        'md' => 1,
                    ]),
                Forms\Components\Select::make('customer_id')
                    ->label(__('messages.the_customer'))
                    ->required()
                    ->reactive()
                    ->distinct()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        $set('tax_number', Customer::find($state)?->tax_number ?? '');
                    })
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                    ->relationship('customer','name')
                    ->columnSpan([
                        'md' => 1,
                    ]),

                Forms\Components\TextInput::make('tax_number')
                    ->label(__('messages.tax_number'))
                    ->required()
                    ->dehydrated()
                    ->disabled()
                    ->formatStateUsing(fn(Forms\Get $get) => Customer::find($get('customer_id'))?->tax_number ?? '')
                    ->columnSpan([
                        'md' => 1,
                    ]),

                Forms\Components\Repeater::make('details')
                    ->relationship()
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label(__('messages.the_product'))
                            ->options(Product::query()->pluck('name', 'id'))
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                $set('price', Product::find($state)?->code ?? '');
                            })
                            ->distinct()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->searchable()
                            ->columnSpan([
                                'md' => 3,
                            ]),

                        Forms\Components\TextInput::make('unit_price')
                            ->label(__('messages.unit_price'))
                            ->numeric()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                $quantity = $get('quantity') ?? 0;
                                $price = $get('unit_price') ?? 0;
                                $previousSubtotal = $get('subtotal') ?? 0; // Track previous subtotal
                                $newSubtotal = $quantity * $price;

                                // Update the repeater item's subtotal
                                $set('subtotal', $newSubtotal);

                                // Update the overall subtotal
                                $set('../../subtotal', ($get('../../subtotal') - $previousSubtotal) + $newSubtotal);
                                $set('../../total_fee', round($get('../../subtotal') * (env('TAX_FEE',14)/100),2));
                                $set('../../total_price', $get('../../subtotal') + $get('../../total_fee'));
                            })
                            ->columnSpan([
                                'md' => 1,
                            ])
                            ->disabled(fn (Forms\Get $get) => !$get('product_id')),

                        Forms\Components\TextInput::make('quantity')
                            ->label(__('messages.quantity'))
                            ->numeric()
                            ->reactive()
                            ->reactive()
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                $quantity = $get('quantity') ?? 0;
                                $price = $get('unit_price') ?? 0;
                                $previousSubtotal = $get('subtotal') ?? 0; // Track previous subtotal
                                $newSubtotal = $quantity * $price;

                                // Update the repeater item's subtotal
                                $set('subtotal', $newSubtotal);

                                // Update the overall subtotal
                                $set('../../subtotal', ($get('../../subtotal') - $previousSubtotal) + $newSubtotal);
                                $set('../../total_fee', round($get('../../subtotal') * (env('TAX_FEE',14)/100),2));
                                $set('../../total_price', $get('../../subtotal') + $get('../../total_fee'));
                            })
                            ->required()
                            ->columnSpan([
                                'md' => 1,
                            ])
                            ->disabled(fn (Forms\Get $get) => !$get('product_id')),

                        Forms\Components\TextInput::make('subtotal')
                            ->label(__('messages.sub_total'))
                            ->formatStateUsing(fn (Forms\Get $get) => ($get('price') * $get('quantity')))
                            ->reactive()
                            ->disabled()
                            ->numeric()
                            ->dehydrated()
                            ->default(0)
                            ->required()
                            ->columnSpan([
                                'md' => 3,
                            ]),
                    ])
                    ->columns(8)
                    ->default([])
                    ->defaultItems(1)
                    ->hiddenLabel()
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('subtotal')
                    ->label(__('messages.sub_total'))
                    ->formatStateUsing(fn (Forms\Get $get) => collect($get('details'))->sum(fn($item) => $item['price'] ?? 0))
                    ->reactive()
                    ->disabled()
                    ->numeric()
                    ->dehydrated()
                    ->default(0)
                    ->columnSpan([
                        'md' => 1,
                    ]),

                Forms\Components\TextInput::make('total_fee')
                    ->label(__('messages.total_fee'))
                    ->formatStateUsing(fn (Forms\Get $get) => round($get('subtotal')*(env('TAX_FEE',14)/100),2))
                    ->disabled()
                    ->numeric()
                    ->dehydrated()
                    ->default(0)
                    ->columnSpan([
                        'md' => 1,
                    ]),

                Forms\Components\TextInput::make('total_price')
                    ->label(__('messages.total_price'))
                    ->formatStateUsing(fn (Forms\Get $get) => ($get('subtotal') + $get('total_fee')))
                    ->disabled()
                    ->numeric()
                    ->dehydrated()
                    ->default(0)
                    ->columnSpan([
                        'md' => 1,
                    ]),
            ])->columns(3)
            ;

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subtotal')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('EGP')
                    ->copyableState(true)
                    ->color('success')
                    ->sortable(),
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
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }

    public static function getLabel(): ?string
    {
        return __('messages.invoices');
    }
    public static function getModelLabel(): string
    {
        return __('messages.invoice');
    }
    public static function getPluralLabel(): string
    {
        return __('messages.invoices');
    }
    public static function getTitleCasePluralModelLabel(): string
    {
        return __('messages.the_invoices');
    }
    public static function getNavigationGroup(): string
    {
        return __('messages.invoices_management');
    }
}
