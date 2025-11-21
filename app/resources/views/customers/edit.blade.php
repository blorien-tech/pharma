@extends('layouts.app')

@section('title', 'Edit Customer - BLORIEN Pharma')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">{{ __('customers.edit_customer') }}</h1>
        <p class="mt-1 text-sm text-gray-600">{{ __('common.update_information') }}</p>
    </div>

    <form action="{{ route('customers.update', $customer) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li class="text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow-md p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('common.basic_information') }}</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700">{{ __('common.full_name') }} *</label>
                    <input type="text" name="name" id="name" required value="{{ old('name', $customer->name) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">{{ __('common.phone_number') }} *</label>
                    <input type="text" name="phone" id="phone" required value="{{ old('phone', $customer->phone) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">{{ __('customers.email') }}</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $customer->email) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
                </div>

                <div>
                    <label for="id_number" class="block text-sm font-medium text-gray-700">{{ __('common.id_number') }}</label>
                    <input type="text" name="id_number" id="id_number" value="{{ old('id_number', $customer->id_number) }}"
                        placeholder="{{ __('common.id_number_placeholder') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
                </div>

                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700">{{ __('customers.city') }}</label>
                    <input type="text" name="city" id="city" value="{{ old('city', $customer->city) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
                </div>

                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700">{{ __('customers.address') }}</label>
                    <textarea name="address" id="address" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">{{ old('address', $customer->address) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Credit Settings -->
        <div class="bg-white rounded-lg shadow-md p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('common.credit_settings') }}</h2>

            @if($customer->current_balance > 0)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-sm text-yellow-800">
                    <strong>{{ __('common.note') }}:</strong> {{ __('alerts.customer_outstanding_balance', ['balance' => '৳' . number_format($customer->current_balance, 2)]) }}
                    {{ __('alerts.disabling_credit_not_recommended') }}
                </p>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="flex items-center">
                        <input type="hidden" name="credit_enabled" value="0">
                        <input type="checkbox" name="credit_enabled" id="credit_enabled" value="1"
                            {{ old('credit_enabled', $customer->credit_enabled) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">{{ __('common.enable_credit_customer') }}</span>
                    </label>
                </div>

                <div>
                    <label for="credit_limit" class="block text-sm font-medium text-gray-700">{{ __('customers.credit_limit') }} (৳)</label>
                    <input type="number" name="credit_limit" id="credit_limit" step="0.01" min="0"
                        value="{{ old('credit_limit', $customer->credit_limit) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
                    <p class="mt-1 text-xs text-gray-500">{{ __('common.current_balance') }}: ৳{{ number_format($customer->current_balance, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="bg-white rounded-lg shadow-md p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('common.additional_information') }}</h2>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700">{{ __('common.notes') }}</label>
                <textarea name="notes" id="notes" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">{{ old('notes', $customer->notes) }}</textarea>
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1"
                        {{ old('is_active', $customer->is_active) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">{{ __('common.active_customer') }}</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('customers.show', $customer) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium">
                {{ __('common.cancel') }}
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                {{ __('common.update_customer') }}
            </button>
        </div>
    </form>
</div>
@endsection
