<?php

namespace App\Filament\Resources\Cms\LegalTexts\Schemas;

use App\Enums\LegalTextType;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LegalTextForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('type')
                            ->label(__('resources.legal_texts.fields.type'))
                            ->options(LegalTextType::options())
                            ->required()
                            ->native(false)
                            ->disabledOn('edit')
                            ->unique(ignoreRecord: true),

                        RichEditor::make('content')
                            ->label(__('resources.legal_texts.fields.content'))
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
