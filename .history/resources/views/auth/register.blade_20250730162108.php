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
                </optgroup>
            </select>
            
            <x-input-error :messages="$errors->get('position')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
          <x-input-label for="password" :value="__('Password')" />

          <div class="relative">
              <x-text-input id="password" class="block mt-1 w-full"
                              type="password"
                              name="password"
                              required autocomplete="new-password" />
              <button type="button" onclick="togglePasswordVisibility('password')" class="absolute inset-y-0 right-0 px-3 text-xs text-gray-600 dark:text-gray-400 focus:outline-none">
                  {{ __('Show') }}
              </button>
          </div>

          <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
          <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

          <div class="relative">
              <x-text-input id="password_confirmation" class="block mt-1 w-full"
                              type="password"
                              name="password_confirmation" required autocomplete="new-password" />
              <button type="button" onclick="togglePasswordVisibility('password_confirmation')" class="absolute inset-y-0 right-0 px-3 text-xs text-gray-600 dark:text-gray-400 focus:outline-none">
                {{ __('Show') }}
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
  // Add smooth behavior to all select elements
  document.addEventListener('DOMContentLoaded', function() {
    const selects = document.querySelectorAll('.custom-select');
    
    selects.forEach(select => {
      select.addEventListener('mousedown', function(e) {
        if (window.innerWidth > 768) { // Only on desktop
          // Add animation class
          this.classList.add('animate-open');
          
          // Remove animation class after animation completes
          setTimeout(() => {
            this.classList.remove('animate-open');
          }, 300);
        }
      });
    });
  });
  function togglePasswordVisibility(fieldId) {
        const field = document.getElementById(fieldId);
        const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
        field.setAttribute('type', type);
    }
</script>
