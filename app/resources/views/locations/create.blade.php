@extends('layouts.app')

@section('title', __('locations.add_location') . ' - ' . __('navigation.app_name'))

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('locations.index') }}" class="text-gray-600 hover:text-gray-900">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('locations.add_location') }}</h1>
            <p class="mt-1 text-sm text-gray-600">{{ __('locations.create_subtitle') }}</p>
        </div>
    </div>

    <!-- Create Form -->
    <div class="bg-white rounded-lg shadow">
        <form action="{{ route('locations.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Location Code (Auto-generated preview) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('locations.code') }}
                    <span class="text-xs text-gray-500">({{ __('locations.auto_generated') }})</span>
                </label>
                <input type="text"
                       value="{{ __('locations.will_be_auto_generated') }}"
                       disabled
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-500">
                <p class="text-xs text-gray-500 mt-1">{{ __('locations.code_help') }}</p>
            </div>

            <!-- Location Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('locations.name') }} *
                </label>
                <input type="text"
                       name="name"
                       value="{{ old('name') }}"
                       required
                       placeholder="{{ __('locations.name_example') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Location Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('locations.type') }} *
                </label>
                <select name="type"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('type') border-red-500 @enderror">
                    <option value="">{{ __('locations.select_type') }}</option>
                    <option value="RACK" {{ old('type') === 'RACK' ? 'selected' : '' }}>{{ __('locations.type_rack') }}</option>
                    <option value="SHELF" {{ old('type') === 'SHELF' ? 'selected' : '' }}>{{ __('locations.type_shelf') }}</option>
                    <option value="BIN" {{ old('type') === 'BIN' ? 'selected' : '' }}>{{ __('locations.type_bin') }}</option>
                    <option value="FLOOR" {{ old('type') === 'FLOOR' ? 'selected' : '' }}>{{ __('locations.type_floor') }}</option>
                    <option value="REFRIGERATOR" {{ old('type') === 'REFRIGERATOR' ? 'selected' : '' }}>{{ __('locations.type_refrigerator') }}</option>
                    <option value="COUNTER" {{ old('type') === 'COUNTER' ? 'selected' : '' }}>{{ __('locations.type_counter') }}</option>
                    <option value="WAREHOUSE" {{ old('type') === 'WAREHOUSE' ? 'selected' : '' }}>{{ __('locations.type_warehouse') }}</option>
                </select>
                @error('type')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Parent Location (Optional) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('locations.parent_location') }}
                    <span class="text-xs text-gray-500">({{ __('locations.optional') }})</span>
                </label>
                <select name="parent_id"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('parent_id') border-red-500 @enderror">
                    <option value="">{{ __('locations.none_top_level') }}</option>
                    @foreach($availableParents as $parent)
                        <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                            {{ $parent->getFullPath() }} ({{ ucfirst(strtolower($parent->type)) }})
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">{{ __('locations.parent_help') }}</p>
                @error('parent_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Capacity -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('locations.capacity') }}
                    <span class="text-xs text-gray-500">({{ __('locations.optional') }})</span>
                </label>
                <input type="number"
                       name="capacity"
                       value="{{ old('capacity') }}"
                       min="1"
                       placeholder="10"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('capacity') border-red-500 @enderror">
                <p class="text-xs text-gray-500 mt-1">{{ __('locations.capacity_help') }}</p>
                @error('capacity')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Temperature Control Section -->
            <div class="border-t pt-6">
                <div class="flex items-center mb-4">
                    <input type="checkbox"
                           name="temperature_controlled"
                           id="temperature_controlled"
                           value="1"
                           {{ old('temperature_controlled') ? 'checked' : '' }}
                           x-data="{ checked: {{ old('temperature_controlled') ? 'true' : 'false' }} }"
                           x-model="checked"
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="temperature_controlled" class="ml-2 block text-sm font-medium text-gray-700">
                        {{ __('locations.temperature_controlled') }}
                    </label>
                </div>

                <div x-show="checked" x-transition class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('locations.min_temp') }} (°C)
                        </label>
                        <input type="number"
                               name="temperature_min"
                               value="{{ old('temperature_min') }}"
                               step="0.1"
                               placeholder="2"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('temperature_min') border-red-500 @enderror">
                        @error('temperature_min')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('locations.max_temp') }} (°C)
                        </label>
                        <input type="number"
                               name="temperature_max"
                               value="{{ old('temperature_max') }}"
                               step="0.1"
                               placeholder="8"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('temperature_max') border-red-500 @enderror">
                        @error('temperature_max')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('locations.notes') }}
                    <span class="text-xs text-gray-500">({{ __('locations.optional') }})</span>
                </label>
                <textarea name="notes"
                          rows="3"
                          placeholder="{{ __('locations.notes_placeholder') }}"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Active Status -->
            <div class="flex items-center">
                <input type="checkbox"
                       name="is_active"
                       id="is_active"
                       value="1"
                       {{ old('is_active', true) ? 'checked' : '' }}
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="is_active" class="ml-2 block text-sm font-medium text-gray-700">
                    {{ __('locations.is_active') }}
                </label>
            </div>

            <!-- Form Actions -->
            <div class="flex gap-3 pt-6 border-t">
                <a href="{{ route('locations.index') }}"
                   class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium text-center">
                    {{ __('common.cancel') }}
                </a>
                <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium">
                    {{ __('locations.create_location') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
