@extends('layout.conquer')

@section('title')

@section('content')
    <div class="max-w-4xl mx-auto px-6 py-10">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">User Profile</h1>

        <div class="bg-white shadow-lg rounded-2xl p-8 md:p-10">
            <div class="flex flex-col md:flex-row items-center md:items-start text-center md:text-left">
                <!-- Profile Image -->
                @if (auth()->user()->image)
                    <img src="{{ asset('user_image/' . auth()->user()->image) }}" alt="Profile Image"
                        class="w-32 h-32 object-cover rounded-full border-4 border-blue-200 shadow-md mb-4 md:mb-0 md:mr-6">
                @else
                    <div
                        class="w-32 h-32 bg-gray-200 rounded-full flex items-center justify-center text-gray-500 shadow-md mb-4 md:mb-0 md:mr-6">
                        No Image
                    </div>
                @endif

                <!-- User Info -->
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">{{ auth()->user()->nama }}</h1>
                    <p class="text-sm text-gray-500">{{ auth()->user()->tipe_user }}</p>
                    <p class="text-sm text-green-600 mt-1">
                        {{ auth()->user()->email_verified_at ? 'Email Verified' : 'Email Not Verified' }}
                    </p>
                </div>
            </div>

            <!-- User Details -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-sm font-semibold text-gray-700">Email</h2>
                    <p class="text-gray-600">{{ auth()->user()->email }}</p>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-700">Username</h2>
                    <p class="text-gray-600">{{ auth()->user()->username }}</p>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-700">Phone Number</h2>
                    <p class="text-gray-600">{{ auth()->user()->no_hp ?? '-' }}</p>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-700">Password (Encrypted)</h2>
                    <p class="text-gray-400 italic">Hidden for security</p>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-700">Remember Token</h2>
                    <p class="text-gray-600 text-sm truncate">{{ auth()->user()->remember_token ?? '-' }}</p>
                </div>
            </div>

            <!-- Action -->
            @if (auth()->user()->tipe_user === 'admin')
                <div class="mt-10">
                    <a href="{{ route('users.edit', auth()->user()->id) }}"
                        class="inline-block bg-blue-600 text-white px-5 py-2 rounded-full shadow hover:bg-blue-700 transition">
                        ✏️ Edit Profile
                    </a>
                </div>
            @else
                <div class="mt-10">
                    <a href="{{ route('users.edit', auth()->user()->id) }}"
                        class="inline-block bg-blue-600 text-white px-5 py-2 rounded-full shadow hover:bg-blue-700 transition">
                        ✏️ Edit Password
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
