<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccountResource\Pages;
use App\Filament\Resources\AccountResource\RelationManagers;
use App\Models\Account;
use App\Models\Company;
use App\Models\Department;
use App\Models\Bank;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static ?string $navigationGroup = 'Resource Management';
    protected static ?int $navigationSort = 5;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('company_id')
                ->label('Company')
                ->options(Company::all()->pluck('name', 'id')->toArray())
                ->reactive()
                ->afterStateUpdated(fn (callable $set) => $set('department_id', null))
                ->afterStateUpdated(fn (callable $set) => $set('bank_id', null)),

                Forms\Components\Select::make('department_id')
                ->label('Department')
                ->options(function (callable $get) {
                    $company = Company::find($get('company_id'));

                    if (! $company) {
                        return Department::all()->pluck('name', 'id');
                    }

                    return $company->departments->pluck('name', 'id');
                })
                ->reactive()
                ->afterStateUpdated(fn (callable $set) => $set('bank_id', null)),

                Forms\Components\Select::make('bank_id')
                ->label('Bank Name')
                ->options(function (callable $get) {
                    $department = Department::find($get('department_id'));

                    if (! $department) {
                        return Bank::all()->pluck('bank_name', 'id');
                    }

                    return $department->banks->pluck('bank_name', 'id');
                }),

                Forms\Components\TextInput::make('account_type')->maxLength(255)->label('Account Type'),
                Forms\Components\TextInput::make('account_name')->maxLength(255)->label('Account Name'),
                Forms\Components\TextInput::make('account_number')->maxLength(255)->label('Account Number'),
                Forms\Components\TextInput::make('routing_number_paperless_and_electronic')->maxLength(255)->label('Routing Number: Paperless & Electronic'),
                Forms\Components\TextInput::make('routing_number_wires')->maxLength(255)->label('Routing Number: Wire'),
                Forms\Components\TextInput::make('account_opened_date')->maxLength(255)->label('Account Opened Date'),
                Forms\Components\TextInput::make('currency')->maxLength(255),
                Forms\Components\TextInput::make('starting_balance')->maxLength(255)->label('Starting Balance'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company.name', 'name'),
                Tables\Columns\TextColumn::make('department.name', 'name'),
                Tables\Columns\TextColumn::make('bank.bank_name', 'bank_name')->label('Bank Name'),
                Tables\Columns\TextColumn::make('account_type')->label('Account Type'),
                Tables\Columns\TextColumn::make('account_name')->label('Account Name'),
                Tables\Columns\TextColumn::make('account_number')->label('Account Number'),
                Tables\Columns\TextColumn::make('routing_number_paperless_and_electronic')->label('Routing Number: P&E'),
                Tables\Columns\TextColumn::make('routing_number_wires')->label('Routing Number: Wires'),
                Tables\Columns\TextColumn::make('account_opened_date')->label('Account Opened Date'),
                Tables\Columns\TextColumn::make('currency'),
                Tables\Columns\TextColumn::make('starting_balance')->label('Starting Balance'),
                Tables\Columns\TextColumn::make('cards_count')->counts('cards')->label('Cards'),
                Tables\Columns\TextColumn::make('transactions_count')->counts('transactions')->label('Transactions'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListAccounts::route('/'),
            'create' => Pages\CreateAccount::route('/create'),
            'view' => Pages\ViewAccount::route('/{record}'),
            'edit' => Pages\EditAccount::route('/{record}/edit'),
        ];
    }    
}