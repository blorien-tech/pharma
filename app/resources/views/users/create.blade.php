@extends('layouts.app')

@section('title', __('users.create_user') . ' - BLORIEN Pharma')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">{{ __('users.create_user') }}</h1>
        <p class="mt-1 text-sm text-gray-600">{{ __('users.subtitle') }}</p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
            @csrf

            @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">{{ __('users.full_name') }} *</label>
                <input type="text" name="name" id="name" required
                    value="{{ old('name') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">{{ __('users.email') }} *</label>
                <input type="email" name="email" id="email" required
                    value="{{ old('email') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">{{ __('users.password') }} *</label>
                <input type="password" name="password" id="password" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                <p class="mt-1 text-sm text-gray-500">{{ __('users.password_strength') }}</p>
            </div>

            <!-- Password Confirmation -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">{{ __('users.confirm_password') }} *</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
            </div>

            <!-- Role -->
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700">{{ __('users.role') }} *</label>
                <select name="role" id="role" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                    <option value="">{{ __('users.assign_role') }}</option>
                    <option value="owner" {{ old('role') === 'owner' ? 'selected' : '' }}>{{ __('users.owner') }}</option>
                    <option value="manager" {{ old('role') === 'manager' ? 'selected' : '' }}>{{ __('users.manager') }}</option>
                    <option value="cashier" {{ old('role') === 'cashier' ? 'selected' : '' }}>{{ __('users.cashier') }}</option>
                </select>
                <div class="mt-2 text-sm text-gray-600 space-y-1">
                    <p><strong>{{ __('users.owner') }}:</strong> {{ __('users.owner_desc') }}</p>
                    <p><strong>{{ __('users.manager') }}:</strong> {{ __('users.manager_desc') }}</p>
                    <p><strong>{{ __('users.cashier') }}:</strong> {{ __('users.cashier_desc') }}</p>
                </div>
            </div>

            <!-- Status -->
            <div class="flex items-center">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                    {{ __('users.active') }}
                </label>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('users.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium">
                    {{ __('common.cancel') }}
                </a>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                    {{ __('users.create_user') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
