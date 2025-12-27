<?php

namespace App\Filament\Resources\Cms\Sections\Schemas;

use App\Enums\CmsStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('resources.cms_sections.fields.name'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label(__('resources.cms_sections.fields.description'))
                            ->rows(4)
                            ->nullable()
                            ->columnSpanFull(),

                        Select::make('status')
                            ->label(__('resources.cms_sections.fields.status'))
                            ->options(CmsStatus::options())
                            ->default(CmsStatus::ACTIVE->value)
                            ->required()
                            ->native(false),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
