<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - {{ $form->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center py-12">
    <div class="max-w-md mx-auto px-4 text-center">
        <div class="bg-white rounded-lg shadow p-10">
            <div class="text-green-500 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Thank You!</h1>
            <p class="text-gray-600 mb-6">
                Your response to <strong>{{ $form->title }}</strong> has been submitted successfully.
            </p>
            <a href="{{ route('forms.show', $form->slug) }}" class="text-blue-600 hover:underline text-sm">
                Submit another response
            </a>
        </div>
    </div>
</body>
</html>
