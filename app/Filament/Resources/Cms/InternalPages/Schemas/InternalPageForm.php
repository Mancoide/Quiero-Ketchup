<?php

namespace App\Filament\Resources\Cms\InternalPages\Schemas;

use App\Enums\CmsStatus;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InternalPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('section_id')
                            ->label(__('resources.cms_internal_pages.fields.section'))
                            ->relationship('section', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false),

                        TextInput::make('title')
                            ->label(__('resources.cms_internal_pages.fields.title'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        RichEditor::make('description')
                            ->label(__('resources.cms_internal_pages.fields.description'))
                            ->required()
                            ->columnSpanFull(),

                        Select::make('status')
                            ->label(__('resources.cms_internal_pages.fields.status'))
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
