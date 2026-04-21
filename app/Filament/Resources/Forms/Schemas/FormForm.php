<?php

namespace App\Filament\Resources\Forms\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class FormForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->tabs([
                        Tab::make('Details')
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $state, callable $set) => $set('slug', Str::slug($state))),
                                TextInput::make('slug')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->rules(['alpha_dash']),
                                Textarea::make('description')
                                    ->rows(3),
                                Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'published' => 'Published',
                                        'closed' => 'Closed',
                                    ])
                                    ->default('draft')
                                    ->required(),
                                SpatieTagsInput::make('tags'),
                            ]),
                        Tab::make('Form Builder')
                            ->schema([
                                Repeater::make('current_schema')
                                    ->label('Fields')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Select::make('type')
                                                    ->options([
                                                        'text' => 'Text',
                                                        'textarea' => 'Textarea',
                                                        'email' => 'Email',
                                                        'number' => 'Number',
                                                        'phone' => 'Phone',
                                                        'select' => 'Select',
                                                        'radio' => 'Radio',
                                                        'checkbox' => 'Checkbox',
                                                        'boolean' => 'Boolean (Yes/No)',
                                                        'date' => 'Date',
                                                        'datetime' => 'Datetime',
                                                        'file' => 'File Upload',
                                                        'heading' => 'Heading',
                                                        'paragraph' => 'Paragraph',
                                                    ])
                                                    ->required()
                                                    ->live(),
                                                TextInput::make('label')
                                                    ->required()
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(fn (string $state, callable $set) => $set('key', Str::slug($state, '_'))),
                                            ]),
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('key')
                                                    ->required()
                                                    ->rules(['alpha_dash']),
                                                Select::make('width')
                                                    ->options([
                                                        'full' => 'Full Width',
                                                        'half' => 'Half Width',
                                                        'third' => 'One Third',
                                                    ])
                                                    ->default('full'),
                                            ]),
                                        Grid::make(2)
                                            ->schema([
                                                Toggle::make('required')
                                                    ->default(false),
                                            ]),
                                        TextInput::make('placeholder')
                                            ->visible(fn (Get $get): bool => ! in_array($get('type'), ['heading', 'paragraph', 'boolean', 'file', 'date', 'datetime', 'select', 'radio', 'checkbox'])),
                                        TextInput::make('help_text')
                                            ->label('Help Text'),
                                        Textarea::make('content')
                                            ->label('Content')
                                            ->rows(2)
                                            ->visible(fn (Get $get): bool => in_array($get('type'), ['heading', 'paragraph'])),
                                        Repeater::make('options')
                                            ->label('Options')
                                            ->schema([
                                                Grid::make(2)
                                                    ->schema([
                                                        TextInput::make('label')
                                                            ->required()
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(fn (string $state, callable $set) => $set('value', Str::slug($state, '_'))),
                                                        TextInput::make('value')
                                                            ->required(),
                                                    ]),
                                            ])
                                            ->visible(fn (Get $get): bool => in_array($get('type'), ['select', 'radio', 'checkbox'])),
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('validation.min_length')
                                                    ->label('Min Length')
                                                    ->numeric()
                                                    ->visible(fn (Get $get): bool => in_array($get('type'), ['text', 'textarea', 'email', 'phone'])),
                                                TextInput::make('validation.max_length')
                                                    ->label('Max Length')
                                                    ->numeric()
                                                    ->visible(fn (Get $get): bool => in_array($get('type'), ['text', 'textarea', 'email', 'phone'])),
                                                TextInput::make('validation.min_value')
                                                    ->label('Min Value')
                                                    ->numeric()
                                                    ->visible(fn (Get $get): bool => $get('type') === 'number'),
                                                TextInput::make('validation.max_value')
                                                    ->label('Max Value')
                                                    ->numeric()
                                                    ->visible(fn (Get $get): bool => $get('type') === 'number'),
                                                TextInput::make('validation.max_size_kb')
                                                    ->label('Max Size (KB)')
                                                    ->numeric()
                                                    ->visible(fn (Get $get): bool => $get('type') === 'file'),
                                                TextInput::make('validation.accepted_types')
                                                    ->label('Accepted Types (e.g. jpg,pdf)')
                                                    ->visible(fn (Get $get): bool => $get('type') === 'file'),
                                            ]),
                                    ])
                                    ->addActionLabel('Add Field')
                                    ->reorderable()
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['label'] ?? null),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
