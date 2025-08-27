{{-- filepath: resources/views/errors/403.blade.php --}}
@extends('layouts.app') {{-- Or use @extends('errors::minimal') if you want the default error layout --}}

@section('title', __('Forbidden'))
@section('code', '403')
@section('message', __('User does not have the right permissions.'))

<div class="flex flex-col items-center justify-center min-h-screen bg-gray-900 text-gray-300">
    <div class="text-2xl mb-4">403 | USER DOES NOT HAVE THE RIGHT PERMISSIONS.</div>
    <button
        onclick="window.history.back()"
        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg shadow transition"
    >
        Go Back
    </button>
</div>