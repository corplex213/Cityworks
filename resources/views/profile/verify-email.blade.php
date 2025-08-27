{{-- filepath: resources/views/profile/verify-email.blade.php --}}
<x-app-layout>
    <div class="flex flex-col items-center justify-center min-h-screen bg-gray-900">
        <div class="bg-gray-800 p-8 rounded-lg shadow-lg max-w-md w-full text-center">
            <h2 class="text-2xl font-bold text-white mb-4">Verify Your Email</h2>
            <p class="text-gray-300 mb-4">
                We've sent a verification link to your new email address.<br>
                Please check your inbox and click the link to verify your account.
            </p>
            <p class="text-gray-400 text-sm mb-4">
                <b>Note:</b> Check your SPAM or Junk folder if you don't see the email.
            </p>
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    Resend Verification Email
                </button>
            </form>
            <a href="{{ route('logout') }}" class="block mt-4 text-gray-400 hover:text-white underline"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                Log Out
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>
    </div>
</x-app-layout>