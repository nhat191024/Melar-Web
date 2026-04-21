<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class FormController extends Controller
{
    public function show(string $slug): View
    {
        $form = Form::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $version = $form->latestPublishedVersion();

        abort_if(! $version, 404);

        return view('forms.show', compact('form', 'version'));
    }

    public function submit(Request $request, string $slug): RedirectResponse
    {
        $form = Form::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $version = $form->latestPublishedVersion();

        abort_if(! $version, 404);

        $rules = $this->buildValidationRules($version->fields);

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [];
        foreach ($version->fields as $field) {
            $key = $field['key'] ?? null;
            $type = $field['type'] ?? 'text';

            if (! $key || in_array($type, ['heading', 'paragraph'])) {
                continue;
            }

            if ($type === 'file' && $request->hasFile($key)) {
                $data[$key] = $request->file($key)->store('form-submissions', 'private');
            } else {
                $data[$key] = $request->input($key);
            }
        }

        FormSubmission::create([
            'form_id' => $form->id,
            'form_version_id' => $version->id,
            'data' => $data,
            'submitted_by' => auth()->id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('forms.thank-you', $slug);
    }

    public function thankYou(string $slug): View
    {
        $form = Form::where('slug', $slug)->firstOrFail();

        return view('forms.thank-you', compact('form'));
    }

    private function buildValidationRules(array $fields): array
    {
        $rules = [];

        foreach ($fields as $field) {
            $key = $field['key'] ?? null;
            $type = $field['type'] ?? 'text';
            $required = $field['required'] ?? false;
            $validation = $field['validation'] ?? [];

            if (! $key || in_array($type, ['heading', 'paragraph'])) {
                continue;
            }

            $fieldRules = [];

            if ($required) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            match ($type) {
                'email' => $fieldRules[] = 'email',
                'number' => $this->addNumericRules($fieldRules, $validation),
                'file' => $this->addFileRules($fieldRules, $validation),
                'date' => $fieldRules[] = 'date',
                'datetime' => $fieldRules[] = 'date',
                default => null,
            };

            if (in_array($type, ['text', 'textarea', 'email', 'phone'])) {
                $this->addStringLengthRules($fieldRules, $validation);
            }

            $rules[$key] = $fieldRules;
        }

        return $rules;
    }

    private function addStringLengthRules(array &$rules, array $validation): void
    {
        if (! empty($validation['min_length'])) {
            $rules[] = 'min:' . $validation['min_length'];
        }

        if (! empty($validation['max_length'])) {
            $rules[] = 'max:' . $validation['max_length'];
        }
    }

    private function addNumericRules(array &$rules, array $validation): void
    {
        $rules[] = 'numeric';

        if (! empty($validation['min_value'])) {
            $rules[] = 'min:' . $validation['min_value'];
        }

        if (! empty($validation['max_value'])) {
            $rules[] = 'max:' . $validation['max_value'];
        }
    }

    private function addFileRules(array &$rules, array $validation): void
    {
        $rules[] = 'file';

        if (! empty($validation['max_size_kb'])) {
            $rules[] = 'max:' . $validation['max_size_kb'];
        }

        if (! empty($validation['accepted_types'])) {
            $mimes = implode(',', array_map('trim', explode(',', $validation['accepted_types'])));
            $rules[] = 'mimes:' . $mimes;
        }
    }
}
