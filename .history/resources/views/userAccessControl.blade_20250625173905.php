<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('User Access Control') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Users Table --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-900 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-900 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Current Role</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-900 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($users as $user)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @foreach($user->roles as $role)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    {{ $role->name }}
                                                </span>
                                            @endforeach
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @can('manage user access')
                                                <form action="{{ route('user-access.assign-role', $user) }}" method="POST" class="inline">
                                                    @csrf
                                                    <select name="role" class="custom-select rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2">
                                                        <option value="">Select Role</option>
                                                        @foreach($roles as $role)
                                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <button type="submit" class="ml-2 text-indigo-600 hover:text-indigo-900">Assign Role</button>
                                                </form>

                                                <!-- Edit User Button -->
                                                <button type="button"
                                                    class="ml-4 text-yellow-500 hover:text-yellow-700 inline-block"
                                                    onclick="openEditUserModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->position }}')">
                                                    Edit
                                                </button>

                                                <!-- Delete User Button -->
                                                <form action="{{ route('user.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="ml-2 text-red-500 hover:text-red-700">Delete</button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
            {{-- Permission-Role Matrix --}}
            <div class="mt-12">
                <h3 class="text-lg font-bold mb-4 text-white">Role & Permission Matrix</h3>
                <form action="{{ route('user-access.update-role-permissions') }}" method="POST">
                    @csrf
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-900 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Permission</th>
                                            @foreach($roles as $role)
                                                <th class="px-6 py-3 bg-gray-50 dark:bg-gray-900 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $role->name }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($permissions as $index => $permission)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $permission->name }}
                                                </td>
                                                @foreach($roles as $role)
                                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                                        <input type="checkbox"
                                                            name="permissions[{{ $role->id }}][{{ $permission->id }}]"
                                                            value="1"
                                                            class="form-checkbox h-4 w-4 text-indigo-600 transition"
                                                            {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <button type="submit" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Update Permissions</button>
                            <button type="submit" name="restore_default" value="1" class="mt-4 ml-2 px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">Restore to Default</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">Edit User</h2>
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')
                <!-- Name -->
                <div>
                    <x-input-label for="edit_name" :value="__('Name')" />
                    <x-text-input id="edit_name" class="block mt-1 w-full" type="text" name="name" required autofocus />
                </div>
                <!-- Email -->
                <div class="mt-4">
                    <x-input-label for="edit_email" :value="__('Email')" />
                    <x-text-input id="edit_email" class="block mt-1 w-full" type="email" name="email" required />
                </div>
                <!-- Position -->
                <div class="mt-4">
                    <x-input-label for="edit_position" :value="__('Position')" />
                    <select id="edit_position" name="position" class="custom-select block mt-1 w-full bg-gray-800 text-white border-gray-700 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                        <option value="" disabled>{{ __('Select a position') }}</option>
                        <optgroup label="Administrative Positions">
                            <option value="City Engineer">City Engineer</option>
                            <option value="Assistant City Engineer">Assistant City Engineer</option>
                            <option value="Supervising Administrative Officer">Supervising Administrative Officer</option>
                            <option value="Division Head">Division Head</option>
                        </optgroup>
                        <optgroup label="Managerial Position">
                            <option value="Group Leaders">Group Leaders</option>
                        </optgroup>
                        <optgroup label="Operational Position">
                            <option value="Technical Personnel">Technical Personnel</option>
                        </optgroup>
                    </select>
                </div>
                <!-- New Password -->
                <div class="mt-4">
                    <x-input-label for="edit_password" :value="__('New Password (leave blank to keep current)')" />
                    <x-text-input id="edit_password" class="block mt-1 w-full" type="password" name="password" autocomplete="new-password" />
                </div>
                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-input-label for="edit_password_confirmation" :value="__('Confirm New Password')" />
                    <x-text-input id="edit_password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" autocomplete="new-password" />
                </div>
                <div class="flex items-center justify-end mt-6">
                    <button type="button" onclick="closeEditUserModal()" class="mr-2 px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Update</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout> 

<style>
    .custom-select {
        min-width: 150px;
        background-color: #1f2937;
        color: #222;
        border: 2px solid #6366f1;
        border-radius: 0.375rem;
        padding: 0.5rem 1rem;
        font-size: 1rem;
        transition: border-color 0.2s, box-shadow 0.2s;
        box-shadow: 0 2px 8px rgba(99, 102, 241, 0.08);
    }
    .custom-select:focus {
        border-color: #4f46e5;
        outline: none;
        box-shadow: 0 0 0 2px #6366f1;
    }
    .dark .custom-select {
        background-color: #374151;
        color: #f3f4f6;
        border-color: #818cf8;
    }
    .dark .custom-select:focus {
        border-color: #a5b4fc;
        box-shadow: 0 0 0 2px #818cf8;
    }
    .form-checkbox:focus {
        outline: none;
        box-shadow: 0 0 0 2px #6366f1;
    }
    /* Optional: Make the table horizontally scrollable on small screens */
    .overflow-x-auto {
        scrollbar-color: #6366f1 #e5e7eb;
        scrollbar-width: thin;
    }
</style>

<script>
function openEditUserModal(id, name, email, position) {
    document.getElementById('editUserModal').classList.remove('hidden');
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_position').value = position;

    // Set the form action dynamically
    document.getElementById('editUserForm').action = '/users/' + id;
}

function closeEditUserModal() {
    document.getElementById('editUserModal').classList.add('hidden');
}
</script>