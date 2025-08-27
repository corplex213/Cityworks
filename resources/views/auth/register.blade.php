<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf
      
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Enter your full name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="Enter your email address" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Position -->
        <div class="mt-4" data-dropdown-postion=>
            <x-input-label for="position" :value="__('Position')" />
            
            <select id="position" name="position" data-dropdown-postion="bottom" class="custom-select block mt-1 w-full bg-gray-800 text-white border-gray-700 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                <option value="" disabled selected>{{ __('Select a position') }}</option>
            
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
            
            <x-input-error :messages="$errors->get('position')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <div class="relative">
                <x-text-input id="password" class="block mt-1 w-full pr-10"
                    type="password"
                    name="password"
                    required autocomplete="new-password" />
                <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 dark:text-gray-300" tabindex="-1">
                    <!-- Eye icon -->
                    <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <!-- Eye-slash icon -->
                    <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-5.523 0-10-4.477-10-10a9.956 9.956 0 012.458-6.675M21 21L3 3m18 18A9.956 9.956 0 0021 9c0-5.523-4.477-10-10-10a9.956 9.956 0 00-6.675 2.458M15 12a3 3 0 01-3 3m0 0a3 3 0 01-3-3m6 0a3 3 0 00-3-3" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <div class="relative">
                <x-text-input id="password_confirmation" class="block mt-1 w-full pr-10"
                    type="password"
                    name="password_confirmation" required autocomplete="new-password" />
                <button type="button" id="togglePasswordConfirm" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 dark:text-gray-300" tabindex="-1">
                    <!-- Eye icon -->
                    <svg id="eyeOpenConfirm" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <!-- Eye-slash icon -->
                    <svg id="eyeClosedConfirm" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-5.523 0-10-4.477-10-10a9.956 9.956 0 012.458-6.675M21 21L3 3m18 18A9.956 9.956 0 0021 9c0-5.523-4.477-10-10-10a9.956 9.956 0 00-6.675 2.458M15 12a3 3 0 01-3 3m0 0a3 3 0 01-3-3m6 0a3 3 0 00-3-3" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    
    </form>
</x-guest-layout>



<style>
  /* Custom styling for dropdown */
  .custom-select {
    appearance: none;
    transition: all 0.3s ease;
    scroll-behavior: smooth;
  }
  
  /* Styling for dropdown when open */
  .custom-select:focus {
    transform: translateY(2px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }
  
  /* Styling for options */
  .custom-select option, 
  .custom-select optgroup {
    background-color: #1f2937; /* dark background */
    color: white;
    padding: 8px;
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Password field
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');
    const eyeOpen = document.getElementById('eyeOpen');
    const eyeClosed = document.getElementById('eyeClosed');
    togglePassword.addEventListener('click', function () {
        const isPassword = passwordInput.getAttribute('type') === 'password';
        passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
        eyeOpen.classList.toggle('hidden', !isPassword);
        eyeClosed.classList.toggle('hidden', isPassword);
    });

    // Confirm password field
    const passwordConfirmInput = document.getElementById('password_confirmation');
    const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
    const eyeOpenConfirm = document.getElementById('eyeOpenConfirm');
    const eyeClosedConfirm = document.getElementById('eyeClosedConfirm');
    togglePasswordConfirm.addEventListener('click', function () {
        const isPassword = passwordConfirmInput.getAttribute('type') === 'password';
        passwordConfirmInput.setAttribute('type', isPassword ? 'text' : 'password');
        eyeOpenConfirm.classList.toggle('hidden', !isPassword);
        eyeClosedConfirm.classList.toggle('hidden', isPassword);
    });
  });
</script>
