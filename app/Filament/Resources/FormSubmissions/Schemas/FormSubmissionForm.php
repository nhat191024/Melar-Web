<?php

namespace App\Filament\Resources\FormSubmissions\Schemas;

use App\Models\FormSubmission;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class FormSubmissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Meta')
                    ->schema([
                        TextEntry::make('form.title')
                            ->label('Form'),
                        TextEntry::make('formVersion.version_number')
                            ->label('Version'),
                        TextEntry::make('created_at')
                            ->label('Submitted At')
                            ->dateTime(),
                        TextEntry::make('ip_address')
                            ->label('IP Address'),
                        TextEntry::make('user_agent')
                            ->label('User Agent'),
                        TextEntry::make('submitted_by')
                            ->label('Submitted By')
                            ->formatStateUsing(fn ($state) => $state ? $state : 'Guest'),
                    ])
                    ->columns(2),
                Section::make('Submitted Data')
                    ->schema(function (FormSubmission $record): array {
                        $fields = $record->formVersion?->fields ?? [];
                        $data = $record->data ?? [];
                        $entries = [];

                        foreach ($fields as $field) {
                            $key = $field['key'] ?? null;
                            $label = $field['label'] ?? $key;
                            $type = $field['type'] ?? 'text';

                            if (! $key || in_array($type, ['heading', 'paragraph'])) {
                                continue;
                            }

                            $value = $data[$key] ?? null;

                            if ($type === 'file' && $value) {
                                $entries[] = TextEntry::make("data.{$key}")
                                    ->label($label)
                                    ->formatStateUsing(fn () => $value)
                                    ->url(fn () => asset('storage/' . $value));
                            } else {
                                $entries[] = TextEntry::make("data.{$key}")
                                    ->label($label)
                                    ->formatStateUsing(fn () => is_array($value) ? implode(', ', $value) : ($value ?? '-'));
                            }
                        }

                        return $entries ?: [
                            TextEntry::make('data')
                                ->label('Raw Data')
                                ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT)),
                        ];
                    }),
            ]);
    }
}
