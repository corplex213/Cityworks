<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('User Access Control') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Pending User Approvals Table --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                           @if(session('success'))
                                <div id="successAlert" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                                    <span class="block sm:inline">{{ session('success') }}</span>
                                </div>
                            @endif
                            <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                                <i class="fas fa-user-clock mr-2 text-gray-400"></i>
                                Pending User Approvals
                            </h3>
                            <div class="flex items-center gap-2 ml-auto">
                                <div class="relative w-full max-w-xs ml-auto">
                                    <input
                                        id="pendingUserSearch"
                                        type="text"
                                        placeholder="Search pending users..."
                                        class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 p-2 pl-10 pr-10 rounded-lg shadow focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                    >
                                    <!-- Search Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-3 h-5 w-5 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    <!-- Clear Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="absolute right-3 top-3 h-5 w-5 text-gray-400 cursor-pointer hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        onclick="document.getElementById('pendingUserSearch').value=''; document.getElementById('pendingUserSearch').dispatchEvent(new Event('input'));">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </div>
                                <button type="button"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow"
                                    onclick="openRegisterModal()">
                                    + Register New User
                                </button>
                            </div>
                        </div>
                            <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead>
                                            <tr>
                                                <th class="px-6 py-3 bg-gray-50 dark:bg-gray-900 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">Name</th>
                                                <th class="px-6 py-3 bg-gray-50 dark:bg-gray-900 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">Email</th>
                                                <th class="px-6 py-3 bg-gray-50 dark:bg-gray-900 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">Position</th>
                                                <th class="px-6 py-3 bg-gray-50 dark:bg-gray-900 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            @forelse($pendingUsers as $user)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-900 dark:text-gray-100 max-w-xs overflow-x-auto">{{ $user->name }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500 dark:text-gray-400 max-w-xs overflow-x-auto">{{ $user->email }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500 dark:text-gray-400">{{ $user->position }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                                        <form method="POST" action="{{ route('user-access.approve', $user) }}" class="inline">
                                                            @csrf
                                                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                                                                Approve
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="{{ route('user-access.reject', $user) }}" class="inline ml-2" onsubmit="return confirm('Are you sure you want to reject this user?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                                                                Reject
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="px-6 py-4 text-center text-gray-400">
                                                        No pending user approvals.
                                                    </td>
                                                </tr>
                                            @endforelse
                                            <tr id="pendingUserNoResult" style="display:none;">
                                                <td colspan="4" class="px-6 py-4 text-center text-gray-400">
                                                    No results found.
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                        </div> 
                </div>

            {{-- Users Table --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                            <i class="fas fa-users mr-2 text-gray-400"></i>
                            Users Table
                        </h3>
                        <div class="relative w-full max-w-xs ml-auto">
                            <input
                                id="usersTableSearch"
                                type="text"
                                placeholder="Search users..."
                                class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 p-2 pl-10 pr-10 rounded-lg shadow focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            >
                            <!-- Search Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-3 h-5 w-5 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <!-- Clear Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="absolute right-3 top-3 h-5 w-5 text-gray-400 cursor-pointer hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                onclick="document.getElementById('usersTableSearch').value=''; document.getElementById('usersTableSearch').dispatchEvent(new Event('input'));">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                    </div>
                    @if(session('role_changed'))
                        <div id="roleChangedAlert" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">
                                Role for <b>{{ session('user_name') }}</b> has been changed to <b>{{ session('new_role') }}</b>.
                            </span>
                        </div>
                    @endif
                    @if(session('error'))
                        <div id="errorAlert" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                            @if(session('role_change_attempt'))
                                <div class="mt-1 text-red-800 text-xs">
                                    Attempted to change the role for <b>{{ session('user_name') }}</b>, but administrative roles cannot be changed.
                                </div>
                            @endif
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
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center"> <!-- Add text-center here -->
                                            @can('manage user access')
                                                <button type="button"
                                                    class="ml-2 text-indigo-600 hover:text-indigo-900"
                                                    onclick="openAssignRoleModal({{ $user->id }}, '{{ $user->name }}', '{{ strtolower(str_contains($user->roles->first()->name ?? '', 'admin') ? 'administrative' : (str_contains($user->roles->first()->name ?? '', 'manager') ? 'managerial' : 'staff')) }}')">
                                                    Assign Role
                                                </button>
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
                                <tr id="usersTableNoResult" style="display:none;">
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-400">
                                        No results found.
                                    </td>
                                </tr>
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
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                                <i class="fas fa-key mr-2 text-gray-400"></i>
                                Role & Permission Matrix
                            </h3>
                            <div>
                                <button type="submit" form="rolePermissionForm" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                                    Update Permissions
                                </button>
                                <button type="submit" form="rolePermissionForm" name="restore_default" value="1" class="ml-2 px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                                    Restore to Default
                                </button>
                            </div>
                        </div>
                        <div id="unsavedChangesAlert" class="hidden mb-4 px-4 py-2 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700">
                            You have unsaved changes. <b>Don't forget to click "Update Permissions" to save.</b>
                        </div>
                        <form id="rolePermissionForm" action="{{ route('user-access.update-role-permissions') }}" method="POST">
                            @csrf
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
                                        @foreach($permissionGroups as $group => $groupPermissions)
                                            <tr>
                                                <td colspan="{{ 1 + $roles->count() }}" class="bg-gray-100 dark:bg-gray-700 font-bold px-6 py-3 text-left text-gray-700 dark:text-gray-200 uppercase">
                                                    {{ $group }}
                                                </td>
                                            </tr>
                                            @foreach($permissions->whereIn('name', $groupPermissions) as $permission)
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
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Register New User Modal -->
            <div id="registerModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div id="registerModalContent"
                    class="opacity-0 scale-95 transform transition-all duration-300 ease-out bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 p-8 rounded-lg w-full max-w-md relative">
                    <button
                        type="button"
                        onclick="closeRegisterModal()"
                        class="absolute top-3 right-3 w-9 h-9 flex items-center justify-center rounded-full bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                        aria-label="Close modal"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-gray-200">Register New User</h2>
                    <!-- Your copied form goes here -->
                    <form method="POST" action="{{ route('user-access.register') }}" id="registerForm" novalidate>
                        @csrf
                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" value="{{ old('name') }}" required minlength="3" maxlength="100" pattern="^[A-Za-z\s\.\-]+$" placeholder="Enter full name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            <span class="text-red-500 text-xs hidden" id="nameError"></span>
                        </div>
                        <!-- Email -->
                        <div class="mt-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" value="{{ old('email') }}" required placeholder="Enter email address" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            <span class="text-red-500 text-xs hidden" id="emailError"></span>
                        </div>
                        <!-- Position -->
                        <div class="mt-4">
                            <x-input-label for="position" :value="__('Position')" />
                            <select id="position" name="position" class="custom-select block mt-1 w-full bg-white text-gray-800 border-gray-300 dark:bg-gray-800 dark:text-white dark:border-gray-700 rounded-md shadow-sm" required>
                                <option value="" disabled {{ old('position') ? '' : 'selected' }}>{{ __('Select a position') }}</option>
                                <optgroup label="Administrative Engineer">
                                    <option value="City Engineer" {{ old('position') == 'City Engineer' ? 'selected' : '' }}>{{ __('City Engineer') }}</option>
                                    <option value="Assistant City Engineer" {{ old('position') == 'Assistant City Engineer' ? 'selected' : '' }}>{{ __('Assistant City Engineer') }}</option>
                                    <option value="Supervising Administrative Officer" {{ old('position') == 'Supervising Administrative Officer' ? 'selected' : '' }}>{{ __('Supervising Administrative Officer') }}</option>
                                    <option value="Division Head" {{ old('position') == 'Division Head' ? 'selected' : '' }}>{{ __('Division Head') }}</option>
                                </optgroup>
                                <optgroup label="Managerial Engineer">
                                    <option value="Group Leaders" {{ old('position') == 'Group Leaders' ? 'selected' : '' }}>{{ __('Group Leaders') }}</option>
                                </optgroup>
                                <optgroup label="Staff Engineer">
                                    <option value="Technical Personnel" {{ old('position') == 'Technical Personnel' ? 'selected' : '' }}>{{ __('Technical Personnel') }}</option>
                                    <option value="Engineering Assistant" {{ old('position') == 'Engineering Assistant' ? 'selected' : '' }}>{{ __('Engineering Assistant') }}</option>
                                </optgroup>
                            </select>
                            <x-input-error :messages="$errors->get('position')" class="mt-2" />
                            <span class="text-red-500 text-xs hidden" id="positionError"></span>
                        </div>
                        <!-- Password -->
                        <div class="mt-4">
                            <x-input-label for="password" :value="__('Password')" />
                            <div class="relative">
                                <x-text-input id="modal_password" class="block mt-1 w-full pr-10" type="password" name="password" required minlength="8" maxlength="100" pattern=".{8,}" autocomplete="new-password" />
                                <button type="button" id="toggleModalPassword" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 dark:text-gray-300" tabindex="-1">
                                    <!-- Eye open SVG -->
                                    <svg id="eyeOpenModal" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <!-- Eye closed SVG -->
                                    <svg id="eyeClosedModal" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.956 9.956 0 012.293-3.95M6.634 6.634A9.956 9.956 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.973 9.973 0 01-4.293 5.03M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                                    </svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            <span class="text-red-500 text-xs hidden" id="passwordError"></span>
                        </div>
                        <!-- Confirm Password -->
                        <div class="mt-4">
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                            <div class="relative">
                                <x-text-input id="modal_password_confirmation" class="block mt-1 w-full pr-10" type="password" name="password_confirmation" required minlength="8" maxlength="100" autocomplete="new-password" />
                                <button type="button" id="toggleModalPasswordConfirm" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 dark:text-gray-300" tabindex="-1">
                                    <!-- Eye open SVG -->
                                    <svg id="eyeOpenModalConfirm" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <!-- Eye closed SVG -->
                                    <svg id="eyeClosedModalConfirm" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.956 9.956 0 012.293-3.95M6.634 6.634A9.956 9.956 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.973 9.973 0 01-4.293 5.03M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                                    </svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            <span class="text-red-500 text-xs hidden" id="passwordConfirmError"></span>
                        </div>
                        <div class="flex justify-end mt-6">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow">
                                Register
                            </button>
                        </div>
                    </form>
                </div>
            </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div id="editUserModalContent"
            class="opacity-0 scale-95 transform transition-all duration-300 ease-out bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 p-8 rounded-lg w-full max-w-md relative">
            <button
                type="button"
                onclick="closeEditUserModal()"
                class="absolute top-3 right-3 w-9 h-9 flex items-center justify-center rounded-full bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                aria-label="Close modal"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-gray-200">Edit User</h2>
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')
                <!-- Name -->
                <div>
                    <x-input-label for="edit_name" :value="__('Name')" />
                    <x-text-input id="edit_name" class="block mt-1 w-full" type="text" name="name" required autofocus />
                    <span class="text-red-500 text-xs hidden" id="editNameError"></span>
                </div>
                <!-- Email -->
                <div class="mt-4">
                    <x-input-label for="edit_email" :value="__('Email')" />
                    <x-text-input id="edit_email" class="block mt-1 w-full" type="email" name="email" required />
                    <span class="text-red-500 text-xs hidden" id="editEmailError"></span>
                </div>
                <!-- Position -->
                 <div class="mt-4">
                    <x-input-label for="edit_position" :value="__('Position')" />
                    <select id="edit_position" name="position" class="custom-select block mt-1 w-full bg-white text-gray-800 border-gray-300 dark:bg-gray-800 dark:text-white dark:border-gray-700 rounded-md shadow-sm" required>
                        <option value="" disabled>{{ __('Select a position') }}</option>
                        <optgroup label="Administrative Engineer">
                            <option value="City Engineer">{{ __('City Engineer') }}</option>
                            <option value="Assistant City Engineer">{{ __('Assistant City Engineer') }}</option>
                            <option value="Supervising Administrative Officer">{{ __('Supervising Administrative Officer') }}</option>
                            <option value="Division Head">{{ __('Division Head') }}</option>
                        </optgroup>
                        <optgroup label="Managerial Engineer">
                            <option value="Group Leaders">{{ __('Group Leaders') }}</option>
                        </optgroup>
                        <optgroup label="Staff Engineer">
                            <option value="Technical Personnel">{{ __('Technical Personnel') }}</option>
                            <option value="Engineering Assistant">{{ __('Engineering Assistant') }}</option>
                        </optgroup>
                    </select>
                    <span class="text-red-500 text-xs hidden" id="editPositionError"></span>
                </div>
                <!-- New Password -->
                <div class="mt-4">
                    <x-input-label for="edit_password" :value="__('New Password (leave blank to keep current)')" />
                    <x-text-input id="edit_password" class="block mt-1 w-full pr-10" type="password" name="password" autocomplete="new-password" />
                    <span class="text-red-500 text-xs hidden" id="editPasswordError"></span>
                </div>
                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-input-label for="edit_password_confirmation" :value="__('Confirm New Password')" />
                    <x-text-input id="edit_password_confirmation" class="block mt-1 w-full pr-10" type="password" name="password_confirmation" autocomplete="new-password" />
                    <span class="text-red-500 text-xs hidden" id="editPasswordConfirmError"></span>
                </div>
                <div class="flex justify-end mt-6">
                    <button type="button" onclick="closeEditUserModal()" class="mr-2 px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Cancel</button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow">Update</button>
                </div>
            </form>
        </div>
    </div>
<!-- Assign Role Modal -->
    <div id="assignRoleModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div id="assignRoleModalContent"
            class="opacity-0 scale-95 transform transition-all duration-300 ease-out bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 p-8 rounded-lg w-full max-w-md relative">
            <button
                type="button"
                onclick="closeAssignRoleModal()"
                class="absolute top-3 right-3 w-9 h-9 flex items-center justify-center rounded-full bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                aria-label="Close modal"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-gray-200">
                Assign Role to <span id="assignRoleUserName"></span>
            </h2>
            <div class="mb-4 px-3 py-2 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 rounded">
                <strong>Warning:</strong> An <b>Administrative Engineer</b> cannot change their own role.
            </div>
            <form id="assignRoleForm" method="POST">
                @csrf
                <input type="hidden" name="role" id="selectedRole">
                <div class="space-y-4">
                    <div>
                        <label class="block p-4 rounded-lg border border-gray-600 hover:border-indigo-500 cursor-pointer transition bg-white dark:bg-gray-800"
                            onclick="selectRole('administrative')">
                            <input type="radio" name="role_option" value="administrative" class="mr-2" id="role_admin_radio">
                            <span class="font-semibold text-gray-800 dark:text-gray-100">Administrative</span>
                            <div class="text-gray-500 dark:text-gray-400 text-sm mt-1">Full access to user management and permissions.</div>
                        </label>
                    </div>
                    <div>
                        <label class="block p-4 rounded-lg border border-gray-600 hover:border-indigo-500 cursor-pointer transition bg-white dark:bg-gray-800"
                            onclick="selectRole('managerial')">
                            <input type="radio" name="role_option" value="managerial" class="mr-2" id="role_manager_radio">
                            <span class="font-semibold text-gray-800 dark:text-gray-100">Managerial</span>
                            <div class="text-gray-500 dark:text-gray-400 text-sm mt-1">Can manage teams and oversee activities.</div>
                        </label>
                    </div>
                    <div>
                        <label class="block p-4 rounded-lg border border-gray-600 hover:border-indigo-500 cursor-pointer transition bg-white dark:bg-gray-800"
                            onclick="selectRole('staff')">
                            <input type="radio" name="role_option" value="staff" class="mr-2" id="role_staff_radio">
                            <span class="font-semibold text-gray-800 dark:text-gray-100">Staff</span>
                            <div class="text-gray-500 dark:text-gray-400 text-sm mt-1">Basic access for regular personnel.</div>
                        </label>
                    </div>
                </div>
                <div class="flex justify-end mt-6">
                    <button type="button" onclick="closeAssignRoleModal()" class="mr-2 px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Cancel</button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow">Assign</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout> 

<style>
    .custom-select {
        min-width: 150px;
        background-color: #1f2937;
        color: #fff;
        border: 1px solid #374151;
        border-radius: 0.375rem;
        padding: 0.5rem 1rem;
        font-size: 1rem;
        transition: border-color 0.2s, box-shadow 0.2s;
        box-shadow: 0 2px 8px rgba(99, 102, 241, 0.08);
    }
    .custom-select:focus {
        border-color: #6366f1;
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
    /* Add or update this style block */
    .overflow-x-auto {
        overflow-x: auto;
    }
    .max-w-xs {
        max-width: 16rem; /* Adjust as needed */
    }
</style>

<script>
function openAssignRoleModal(userId, userName, currentRole) {
    const modal = document.getElementById('assignRoleModal');
    const content = document.getElementById('assignRoleModalContent');
    document.getElementById('assignRoleUserName').textContent = userName;
    document.getElementById('assignRoleForm').action = '/user-access-control/' + userId + '/assign-role';
    // Set the current role as selected
    document.querySelectorAll('input[name="role_option"]').forEach(r => {
        r.checked = (r.value === currentRole);
    });
    document.getElementById('selectedRole').value = currentRole || '';
    modal.classList.remove('hidden');
    setTimeout(() => {
        content.classList.remove('opacity-0', 'scale-95');
        content.classList.add('opacity-100', 'scale-100');
    }, 10);
}
function closeAssignRoleModal() {
    const modal = document.getElementById('assignRoleModal');
    const content = document.getElementById('assignRoleModalContent');
    content.classList.remove('opacity-100', 'scale-100');
    content.classList.add('opacity-0', 'scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}
function selectRole(role) {
    document.getElementById('selectedRole').value = role;
    document.querySelectorAll('input[name="role_option"]').forEach(r => {
        r.checked = r.value === role;
    });
}
document.getElementById('assignRoleForm').addEventListener('submit', function(e) {
    if (!document.getElementById('selectedRole').value) {
        e.preventDefault();
        alert('Please select a role.');
    }
});
function openRegisterModal() {
            const modal = document.getElementById('registerModal');
            const content = document.getElementById('registerModalContent');
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('opacity-0', 'scale-95');
                content.classList.add('opacity-100', 'scale-100');
            }, 10);
        }

        function closeRegisterModal() {
            const modal = document.getElementById('registerModal');
            const content = document.getElementById('registerModalContent');
            content.classList.remove('opacity-100', 'scale-100');
            content.classList.add('opacity-0', 'scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }


document.addEventListener('DOMContentLoaded', function () {
    setTimeout(function() {
        let alerts = ['successAlert', 'roleChangedAlert', 'errorAlert'];
        alerts.forEach(function(id) {
            let el = document.getElementById(id);
            if (el) {
                el.style.transition = "opacity 0.5s";
                el.style.opacity = 0;
                setTimeout(() => el.style.display = "none", 500);
            }
        });
    }, 3000);
    const openBtn = document.querySelector('.bg-indigo-600');
    if (openBtn) openBtn.onclick = openRegisterModal;
    const closeBtn = document.querySelector('#registerModal button[aria-label="Close modal"]');
    if (closeBtn) closeBtn.onclick = closeRegisterModal;

    // Show modal if there are validation errors
    @if($errors->any())
        document.getElementById('registerModal').classList.remove('hidden');
    @endif

    // Password field
    const passwordInput = document.getElementById('modal_password');
    const togglePassword = document.getElementById('toggleModalPassword');
    const eyeOpenModal = document.getElementById('eyeOpenModal');
    const eyeClosedModal = document.getElementById('eyeClosedModal');
    if (togglePassword && passwordInput && eyeOpenModal && eyeClosedModal) {
        togglePassword.addEventListener('click', function () {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            eyeOpenModal.classList.toggle('hidden', !isPassword);
            eyeClosedModal.classList.toggle('hidden', isPassword);
        });
    }

    // Confirm password field
    const passwordConfirmInput = document.getElementById('modal_password_confirmation');
    const togglePasswordConfirm = document.getElementById('toggleModalPasswordConfirm');
    const eyeOpenModalConfirm = document.getElementById('eyeOpenModalConfirm');
    const eyeClosedModalConfirm = document.getElementById('eyeClosedModalConfirm');
    if (togglePasswordConfirm && passwordConfirmInput && eyeOpenModalConfirm && eyeClosedModalConfirm) {
        togglePasswordConfirm.addEventListener('click', function () {
            const isPassword = passwordConfirmInput.getAttribute('type') === 'password';
            passwordConfirmInput.setAttribute('type', isPassword ? 'text' : 'password');
            eyeOpenModalConfirm.classList.toggle('hidden', !isPassword);
            eyeClosedModalConfirm.classList.toggle('hidden', isPassword);
        });
    }

    document.getElementById('registerForm').addEventListener('submit', function(e) {
        let valid = true;

        // Name validation
        const name = document.getElementById('name');
        const nameError = document.getElementById('nameError');
        if (!name.value.trim().match(/^[A-Za-z\s\.\-]{3,}$/)) {
            nameError.textContent = "Please enter a valid name (letters only, min 3 characters).";
            nameError.classList.remove('hidden');
            valid = false;
        } else {
            nameError.classList.add('hidden');
        }

        // Email validation
        const email = document.getElementById('email');
        const emailError = document.getElementById('emailError');
        if (!email.value.trim().match(/^[^@\s]+@[^@\s]+\.[^@\s]+$/)) {
            emailError.textContent = "Please enter a valid email address.";
            emailError.classList.remove('hidden');
            valid = false;
        } else {
            emailError.classList.add('hidden');
        }

        // Position validation
        const position = document.getElementById('position');
        const positionError = document.getElementById('positionError');
        if (!position.value) {
            positionError.textContent = "Please select a position.";
            positionError.classList.remove('hidden');
            valid = false;
        } else {
            positionError.classList.add('hidden');
        }

        // Password validation
        const password = document.getElementById('modal_password');
        const passwordError = document.getElementById('passwordError');
        if (password.value.length < 8) {
            passwordError.textContent = "Password must be at least 8 characters.";
            passwordError.classList.remove('hidden');
            valid = false;
        } else {
            passwordError.classList.add('hidden');
        }

        // Confirm password validation
        const passwordConfirm = document.getElementById('modal_password_confirmation');
        const passwordConfirmError = document.getElementById('passwordConfirmError');
        if (password.value !== passwordConfirm.value) {
            passwordConfirmError.textContent = "Passwords do not match.";
            passwordConfirmError.classList.remove('hidden');
            valid = false;
        } else {
            passwordConfirmError.classList.add('hidden');
        }
        if (!valid) e.preventDefault();
    });


    // Validation for Edit User Form
    document.getElementById('editUserForm').addEventListener('submit', function(e) {
        let valid = true;

        // Name validation
        const name = document.getElementById('edit_name');
        const nameError = document.getElementById('editNameError');
        if (!name.value.trim().match(/^[A-Za-z\s\.\-]{3,}$/)) {
            nameError.textContent = "Please enter a valid name (letters only, min 3 characters).";
            nameError.classList.remove('hidden');
            valid = false;
        } else {
            nameError.classList.add('hidden');
        }

        // Email validation
        const email = document.getElementById('edit_email');
        const emailError = document.getElementById('editEmailError');
        if (!email.value.trim().match(/^[^@\s]+@[^@\s]+\.[^@\s]+$/)) {
            emailError.textContent = "Please enter a valid email address.";
            emailError.classList.remove('hidden');
            valid = false;
        } else {
            emailError.classList.add('hidden');
        }

        // Position validation
        const position = document.getElementById('edit_position');
        const positionError = document.getElementById('editPositionError');
        if (!position.value) {
            positionError.textContent = "Please select a position.";
            positionError.classList.remove('hidden');
            valid = false;
        } else {
            positionError.classList.add('hidden');
        }

        // Password validation (if not blank)
        const password = document.getElementById('edit_password');
        const passwordError = document.getElementById('editPasswordError');
        if (password.value && password.value.length < 8) {
            passwordError.textContent = "Password must be at least 8 characters.";
            passwordError.classList.remove('hidden');
            valid = false;
        } else {
            passwordError.classList.add('hidden');
        }

        // Confirm password validation (if password is not blank)
        const passwordConfirm = document.getElementById('edit_password_confirmation');
        const passwordConfirmError = document.getElementById('editPasswordConfirmError');
        if (password.value && password.value !== passwordConfirm.value) {
            passwordConfirmError.textContent = "Passwords do not match.";
            passwordConfirmError.classList.remove('hidden');
            valid = false;
        } else {
            passwordConfirmError.classList.add('hidden');
        }

        if (!valid) e.preventDefault();
    });
});
function openEditUserModal(id, name, email, position) {
    const modal = document.getElementById('editUserModal');
    const content = document.getElementById('editUserModalContent');
    modal.classList.remove('hidden');
    setTimeout(() => {
        content.classList.remove('opacity-0', 'scale-95');
        content.classList.add('opacity-100', 'scale-100');
    }, 10);

    document.getElementById('edit_name').value = name;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_position').value = position;
    document.getElementById('editUserForm').action = '/users/' + id;
}

function closeEditUserModal() {
    const modal = document.getElementById('editUserModal');
    const content = document.getElementById('editUserModalContent');
    content.classList.remove('opacity-100', 'scale-100');
    content.classList.add('opacity-0', 'scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

// Pending User Approvals Search
document.getElementById('pendingUserSearch').addEventListener('input', function() {
    let filter = this.value.toLowerCase();
    let pendingTable = document.querySelectorAll('.bg-white.dark\\:bg-gray-800.overflow-hidden.shadow-sm.sm\\:rounded-lg.mb-8 table')[0];
    if (!pendingTable) return;
    let pendingRows = pendingTable.querySelectorAll('tbody tr:not(#pendingUserNoResult)');
    let found = false;
    pendingRows.forEach(function(row) {
        let text = row.textContent.toLowerCase();
        let show = text.includes(filter);
        row.style.display = show ? '' : 'none';
        if (show) found = true;
    });
    document.getElementById('pendingUserNoResult').style.display = found ? 'none' : '';
});

// Users Table Search
document.getElementById('usersTableSearch').addEventListener('input', function() {
    let filter = this.value.toLowerCase();
    let usersTable = document.querySelectorAll('.bg-white.dark\\:bg-gray-800.overflow-hidden.shadow-sm.sm\\:rounded-lg table')[1];
    if (!usersTable) return;
    let userRows = usersTable.querySelectorAll('tbody tr:not(#usersTableNoResult)');
    let found = false;
    userRows.forEach(function(row) {
        let text = row.textContent.toLowerCase();
        let show = text.includes(filter);
        row.style.display = show ? '' : 'none';
        if (show) found = true;
    });
    document.getElementById('usersTableNoResult').style.display = found ? 'none' : '';
});
</script>