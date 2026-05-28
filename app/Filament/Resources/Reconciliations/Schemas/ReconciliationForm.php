<?php

namespace App\Filament\Resources\Reconciliations\Schemas;

use App\Enums\ReconciliationStatus;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ReconciliationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')
                    ->default(fn (): ?int => Auth::id()),

                Hidden::make('matching_mode')
                    ->default(fn (): string => Request::string('matching_mode')->toString() ?: 'standard'),

                Grid::make([
                    'default' => 1,
                    'xl' => 3,
                ])->schema([
                    Grid::make()->schema([
                        Section::make(__('resources.reconciliations.sections.files'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('resources.reconciliations.fields.name'))
                                    ->default(fn (): string => Request::string('name')->toString())
                                    ->required()
                                    ->maxLength(255),

                                FileUpload::make('bank_file_path')
                                    ->label(__('resources.reconciliations.fields.bank_file'))
                                    ->disk('local')
                                    ->directory('reconciliations/bank')
                                    ->acceptedFileTypes([
                                        'application/pdf',
                                        'application/json',
                                        'text/csv',
                                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                    ])
                                    ->getUploadedFileNameForStorageUsing(
                                        fn ($file): string => now()->format('Ymd_His') . '_bank_' . $file->getClientOriginalName()
                                    )
                                    ->storeFileNamesIn('bank_file_name')
                                    ->required(),

                                FileUpload::make('company_file_path')
                                    ->label(__('resources.reconciliations.fields.company_file'))
                                    ->disk('local')
                                    ->directory('reconciliations/company')
                                    ->acceptedFileTypes([
                                        'application/pdf',
                                        'application/json',
                                        'text/csv',
                                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                    ])
                                    ->getUploadedFileNameForStorageUsing(
                                        fn ($file): string => now()->format('Ymd_His') . '_company_' . $file->getClientOriginalName()
                                    )
                                    ->storeFileNamesIn('company_file_name')
                                    ->required(),

                                Select::make('status')
                                    ->label(__('resources.reconciliations.fields.status'))
                                    ->options(ReconciliationStatus::options())
                                    ->native(false)
                                    ->default(ReconciliationStatus::PENDING->value)
                                    ->disabled(),

                                Textarea::make('error_message')
                                    ->label(__('resources.reconciliations.fields.error_message'))
                                    ->rows(5)
                                    ->disabled()
                                    ->visible(fn (string $context): bool => $context !== 'create'),
                            ])
                            ->columnSpanFull(),
                    ])->columnSpan(2),

                    Grid::make()->schema([
                        Section::make(__('resources.reconciliations.sections.summary'))
                            ->schema([
                                TextInput::make('total_bank_records')
                                    ->label(__('resources.reconciliations.fields.total_bank_records'))
                                    ->numeric()
                                    ->disabled()
                                    ->default(0),

                                TextInput::make('total_company_records')
                                    ->label(__('resources.reconciliations.fields.total_company_records'))
                                    ->numeric()
                                    ->disabled()
                                    ->default(0),

                                TextInput::make('matched_records')
                                    ->label(__('resources.reconciliations.fields.matched_records'))
                                    ->numeric()
                                    ->disabled()
                                    ->default(0),

                                TextInput::make('bank_only_records')
                                    ->label(__('resources.reconciliations.fields.bank_only_records'))
                                    ->numeric()
                                    ->disabled()
                                    ->default(0),

                                TextInput::make('company_only_records')
                                    ->label(__('resources.reconciliations.fields.company_only_records'))
                                    ->numeric()
                                    ->disabled()
                                    ->default(0),

                                TextInput::make('possible_matches')
                                    ->label(__('resources.reconciliations.fields.possible_matches'))
                                    ->numeric()
                                    ->disabled()
                                    ->default(0),

                                TextInput::make('total_reconciled_bank')
                                    ->label(__('resources.reconciliations.fields.total_reconciled_bank'))
                                    ->numeric()
                                    ->prefix('Gs.')
                                    ->disabled(),

                                TextInput::make('total_reconciled_company')
                                    ->label(__('resources.reconciliations.fields.total_reconciled_company'))
                                    ->numeric()
                                    ->prefix('Gs.')
                                    ->disabled(),

                                TextInput::make('total_unreconciled_bank')
                                    ->label(__('resources.reconciliations.fields.total_unreconciled_bank'))
                                    ->numeric()
                                    ->prefix('Gs.')
                                    ->disabled(),

                                TextInput::make('total_unreconciled_company')
                                    ->label(__('resources.reconciliations.fields.total_unreconciled_company'))
                                    ->numeric()
                                    ->prefix('Gs.')
                                    ->disabled(),

                                TextInput::make('ledger_balance')
                                    ->label(__('resources.reconciliations.fields.ledger_balance'))
                                    ->numeric()
                                    ->prefix('Gs.')
                                    ->disabled()
                                    ->default(0),

                                TextInput::make('outstanding_checks')
                                    ->label(__('resources.reconciliations.fields.outstanding_checks'))
                                    ->numeric()
                                    ->prefix('Gs.')
                                    ->disabled()
                                    ->default(0),

                                TextInput::make('bank_unregistered_credits')
                                    ->label(__('resources.reconciliations.fields.bank_unregistered_credits'))
                                    ->numeric()
                                    ->prefix('Gs.')
                                    ->disabled()
                                    ->default(0),

                                TextInput::make('unbooked_debits')
                                    ->label(__('resources.reconciliations.fields.unbooked_debits'))
                                    ->numeric()
                                    ->prefix('Gs.')
                                    ->disabled()
                                    ->default(0),

                                TextInput::make('unbooked_credits')
                                    ->label(__('resources.reconciliations.fields.unbooked_credits'))
                                    ->numeric()
                                    ->prefix('Gs.')
                                    ->disabled()
                                    ->default(0),

                                TextInput::make('reconciled_balance')
                                    ->label(__('resources.reconciliations.fields.reconciled_balance'))
                                    ->numeric()
                                    ->prefix('Gs.')
                                    ->disabled()
                                    ->default(0),

                                TextInput::make('bank_statement_balance')
                                    ->label(__('resources.reconciliations.fields.bank_statement_balance'))
                                    ->numeric()
                                    ->prefix('Gs.')
                                    ->disabled()
                                    ->default(0),

                                TextInput::make('difference_amount')
                                    ->label(__('resources.reconciliations.fields.difference_amount'))
                                    ->numeric()
                                    ->prefix('Gs.')
                                    ->disabled()
                                    ->default(0),

                                TextInput::make('processed_at')
                                    ->label(__('resources.reconciliations.fields.processed_at'))
                                    ->disabled()
                                    ->visible(fn (string $context): bool => $context !== 'create'),
                            ]),
                    ])->columnSpan(1),
                ])->columnSpanFull(),
            ]);
    }
}
