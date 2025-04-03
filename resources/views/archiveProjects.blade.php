<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Archived Projects') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Success Message -->
                    @if(session('success'))
                        <div class="bg-green-500 text-white p-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Archived Project List -->
                    <div class="bg-white p-4 shadow rounded-lg">
                        <h3 class="text-lg font-semibold mb-3">Archived Projects</h3>
                        @if($archivedProjects->isEmpty())
                            <p class="text-gray-500">No archived projects.</p>
                        @else
                            <ul class="divide-y divide-gray-200">
                                @foreach($archivedProjects as $project)
                                    <li class="py-2 flex justify-between items-center">
                                        <span class="text-gray-500">{{ $project->proj_name }}</span>
                                        <form action="{{ route('projects.restore', $project->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white px-3 py-1 rounded">
                                                Restore
                                            </button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
