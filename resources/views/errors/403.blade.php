{{-- filepath: resources/views/errors/403.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>403 | Forbidden</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 flex items-center justify-center min-h-screen">
    <div class="text-center">
        <div class="text-gray-400 text-xl mb-6">403 | USER DOES NOT HAVE THE RIGHT PERMISSIONS.</div>
        <button
            onclick="window.history.back()"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg shadow transition"
        >
            Go Back
        </button>
    </div>
</body>
</html>