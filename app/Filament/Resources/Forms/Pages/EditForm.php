<?php

namespace App\Filament\Resources\Forms\Pages;

use App\Filament\Resources\Forms\FormResource;
use App\Models\FormVersion;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditForm extends EditRecord
{
    protected static string $resource = FormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('publish')
                ->label('Publish')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Publish Form')
                ->modalDescription('This will create a new version snapshot and set the form status to published. Are you sure?')
                ->action(function (): void {
                    $record = $this->getRecord();

                    $lastVersion = $record->versions()->orderByDesc('version_number')->first();
                    $nextVersion = $lastVersion ? $lastVersion->version_number + 1 : 1;

                    FormVersion::create([
                        'form_id' => $record->id,
                        'version_number' => $nextVersion,
                        'schema' => $record->current_schema ?? [],
                        'published_at' => now(),
                    ]);

                    $record->update(['status' => 'published']);

                    Notification::make()
                        ->title('Form published successfully')
                        ->success()
                        ->send();
                }),
            Action::make('preview')
                ->label('Preview')
                ->url(fn () => route('forms.show', $this->getRecord()->slug))
                ->openUrlInNewTab()
                ->color('gray'),
            DeleteAction::make(),
        ];
    }
}
