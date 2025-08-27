<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        Hello {{ $user->name }},
    </div>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        You have been assigned to an activity: <strong>{{ $project->name }}</strong>.
    </div>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        Please log in to your account for more details.
    </div>
</x-guest-layout>