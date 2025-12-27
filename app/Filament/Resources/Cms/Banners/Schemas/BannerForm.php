<?php

namespace App\Filament\Resources\Cms\Banners\Schemas;

use App\Enums\CmsStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make([
                    'default' => 3,
                ])->schema([
                    Grid::make()
                        ->schema([
                            Section::make(__('resources.cms_banners.sections.media'))
                                ->schema([
                                    SpatieMediaLibraryFileUpload::make('image')
                                        ->collection('image')
                                        ->label(__('resources.cms_banners.fields.image'))
                                        ->image()
                                        ->imageEditor()
                                        ->maxSize(4096)
                                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                        ->disk('public')
                                        ->visibility('public')
                                        ->downloadable()
                                        ->openable()
                                        ->deletable()
                                        ->required(),
                                ])
                                ->compact()
                                ->columnSpanFull(),

                            Section::make(__('resources.cms_banners.sections.links'))
                                ->schema([
                                    TextInput::make('button_text')
                                        ->label(__('resources.cms_banners.fields.button_text'))
                                        ->maxLength(255)
                                        ->nullable(),

                                    TextInput::make('image_link')
                                        ->label(__('resources.cms_banners.fields.image_link'))
                                        ->url()
                                        ->maxLength(2048)
                                        ->nullable(),

                                    TextInput::make('button_link')
                                        ->label(__('resources.cms_banners.fields.button_link'))
                                        ->url()
                                        ->maxLength(2048)
                                        ->nullable(),
                                ])
                                ->columns([
                                    'default' => 1,
                                    'md' => 2,
                                ])
                                ->columnSpanFull(),
                        ])
                        ->columnSpan(2),

                    Grid::make()
                        ->schema([
                            Section::make(__('resources.cms_banners.sections.configuration'))
                                ->schema([
                                    Select::make('section_id')
                                        ->label(__('resources.cms_banners.fields.section'))
                                        ->relationship('section', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->native(false),

                                    Select::make('status')
                                        ->label(__('resources.cms_banners.fields.status'))
                                        ->options(CmsStatus::options())
                                        ->default(CmsStatus::ACTIVE->value)
                                        ->required()
                                        ->native(false),

                                    TextInput::make('sort_order')
                                        ->label(__('resources.cms_banners.fields.sort_order'))
                                        ->numeric()
                                        ->minValue(0)
                                        ->default(0)
                                        ->required(),
                                ])
                                ->columnSpanFull(),
                        ])
                        ->columnSpan(1),
                ])->columnSpanFull(),
            ]);
    }
}
