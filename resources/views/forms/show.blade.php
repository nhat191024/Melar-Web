<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $form->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gray-50 py-12">
    <div class="mx-auto max-w-2xl px-4">
        <div class="rounded-lg bg-white p-8 shadow">
            <h1 class="mb-2 text-2xl font-bold text-gray-900">{{ $form->title }}</h1>
            @if ($form->description)
                <p class="mb-6 text-gray-600">{{ $form->description }}</p>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-md border border-red-200 bg-red-50 p-4">
                    <ul class="list-inside list-disc space-y-1 text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('forms.submit', $form->slug) }}" enctype="multipart/form-data">
                @csrf

                @foreach ($version->fields as $field)
                    @php
                        $type = $field['type'] ?? 'text';
                        $key = $field['key'] ?? null;
                        $label = $field['label'] ?? '';
                        $required = $field['required'] ?? false;
                        $placeholder = $field['placeholder'] ?? '';
                        $helpText = $field['help_text'] ?? '';
                        $options = $field['options'] ?? [];
                        $content = $field['content'] ?? '';
                        $width = $field['width'] ?? 'full';
                        $widthClass = match ($width) {
                            'half' => 'w-1/2',
                            'third' => 'w-1/3',
                            default => 'w-full',
                        };
                    @endphp

                    @if ($type === 'heading')
                        <h2 class="mb-2 mt-6 text-xl font-semibold text-gray-800">{{ $content }}</h2>
                    @elseif ($type === 'paragraph')
                        <p class="mb-4 text-gray-600">{{ $content }}</p>
                    @elseif ($key)
                        <div class="{{ $widthClass }} mb-5">
                            @if ($type !== 'boolean')
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="{{ $key }}">
                                    {{ $label }}
                                    @if ($required)
                                        <span class="text-red-500">*</span>
                                    @endif
                                </label>
                            @endif

                            @if (in_array($type, ['text', 'email', 'number', 'phone']))
                                <input id="{{ $key }}" class="@error($key) border-red-500 @enderror w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" name="{{ $key }}" type="{{ $type === 'phone' ? 'tel' : $type }}" value="{{ old($key) }}"
                                    placeholder="{{ $placeholder }}" @if ($required) required @endif>
                            @elseif ($type === 'textarea')
                                <textarea id="{{ $key }}" class="@error($key) border-red-500 @enderror w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" name="{{ $key }}" placeholder="{{ $placeholder }}" rows="4" @if ($required) required @endif>{{ old($key) }}</textarea>
                            @elseif ($type === 'select')
                                <select id="{{ $key }}" class="@error($key) border-red-500 @enderror w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" name="{{ $key }}" @if ($required) required @endif>
                                    <option value="">-- Select --</option>
                                    @foreach ($options as $option)
                                        <option value="{{ $option['value'] }}" {{ old($key) === $option['value'] ? 'selected' : '' }}>
                                            {{ $option['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            @elseif ($type === 'radio')
                                <div class="space-y-2">
                                    @foreach ($options as $option)
                                        <label class="flex items-center gap-2 text-sm text-gray-700">
                                            <input class="text-blue-600" name="{{ $key }}" type="radio" value="{{ $option['value'] }}" {{ old($key) === $option['value'] ? 'checked' : '' }}>
                                            {{ $option['label'] }}
                                        </label>
                                    @endforeach
                                </div>
                            @elseif ($type === 'checkbox')
                                <div class="space-y-2">
                                    @foreach ($options as $option)
                                        <label class="flex items-center gap-2 text-sm text-gray-700">
                                            <input class="text-blue-600" name="{{ $key }}[]" type="checkbox" value="{{ $option['value'] }}" {{ is_array(old($key)) && in_array($option['value'], old($key)) ? 'checked' : '' }}>
                                            {{ $option['label'] }}
                                        </label>
                                    @endforeach
                                </div>
                            @elseif ($type === 'boolean')
                                <label class="flex cursor-pointer items-center gap-2 text-sm text-gray-700">
                                    <input id="{{ $key }}" class="text-blue-600" name="{{ $key }}" type="checkbox" value="1" {{ old($key) ? 'checked' : '' }}>
                                    {{ $label }}
                                    @if ($required)
                                        <span class="text-red-500">*</span>
                                    @endif
                                </label>
                            @elseif (in_array($type, ['date', 'datetime']))
                                <input id="{{ $key }}" class="@error($key) border-red-500 @enderror w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" name="{{ $key }}" type="{{ $type === 'datetime' ? 'datetime-local' : 'date' }}"
                                    value="{{ old($key) }}" @if ($required) required @endif>
                            @elseif ($type === 'file')
                                <input id="{{ $key }}" class="@error($key) border-red-500 @enderror w-full text-sm text-gray-600 file:mr-4 file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-blue-700 hover:file:bg-blue-100" name="{{ $key }}" type="file"
                                    @if ($required) required @endif>
                            @endif

                            @if ($helpText)
                                <p class="mt-1 text-xs text-gray-500">{{ $helpText }}</p>
                            @endif

                            @error($key)
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                @endforeach

                <div class="mt-6">
                    <button class="w-full rounded-md bg-blue-600 px-4 py-2 font-medium text-white transition hover:bg-blue-700" type="submit">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
