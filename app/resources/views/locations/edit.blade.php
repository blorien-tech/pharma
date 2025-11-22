@extends('layouts.app')

@section('title', __('locations.edit_location') . ' - ' . __('navigation.app_name'))

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('locations.show', $location) }}" class="text-gray-600 hover:text-gray-900">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('locations.edit_location') }}</h1>
            <p class="mt-1 text-sm text-gray-600">{{ $location->getFullPath() }}</p>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-lg shadow">
        <form action="{{ route('locations.update', $location) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Location Code (Read-only) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('locations.code') }}
                </label>
                <input type="text"
                       value="{{ $location->code }}"
                       disabled
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-500">
                <p class="text-xs text-gray-500 mt-1">{{ __('locations.code_readonly') }}</p>
            </div>

            <!-- Location Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('locations.name') }} *
                </label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $location->name) }}"
                       required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Location Type (Read-only if has children) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('locations.type') }} *
                </label>
                @if($location->children->isNotEmpty())
                    <input type="text"
                           value="{{ ucfirst(strtolower($location->type)) }}"
                           disabled
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-500">
                    <input type="hidden" name="type" value="{{ $location->type }}">
                    <p class="text-xs text-gray-500 mt-1">{{ __('locations.type_readonly_has_children') }}</p>
                @else
                    <select name="type"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('type') border-red-500 @enderror">
                        <option value="RACK" {{ old('type', $location->type) === 'RACK' ? 'selected' : '' }}>{{ __('locations.type_rack') }}</option>
                        <option value="SHELF" {{ old('type', $location->type) === 'SHELF' ? 'selected' : '' }}>{{ __('locations.type_shelf') }}</option>
                        <option value="BIN" {{ old('type', $location->type) === 'BIN' ? 'selected' : '' }}>{{ __('locations.type_bin') }}</option>
                        <option value="FLOOR" {{ old('type', $location->type) === 'FLOOR' ? 'selected' : '' }}>{{ __('locations.type_floor') }}</option>
                        <option value="REFRIGERATOR" {{ old('type', $location->type) === 'REFRIGERATOR' ? 'selected' : '' }}>{{ __('locations.type_refrigerator') }}</option>
                        <option value="COUNTER" {{ old('type', $location->type) === 'COUNTER' ? 'selected' : '' }}>{{ __('locations.type_counter') }}</option>
                        <option value="WAREHOUSE" {{ old('type', $location->type) === 'WAREHOUSE' ? 'selected' : '' }}>{{ __('locations.type_warehouse') }}</option>
                    </select>
                    @error('type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                @endif
            </div>

            <!-- Parent Location (Read-only if has children) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('locations.parent_location') }}
                    <span class="text-xs text-gray-500">({{ __('locations.optional') }})</span>
                </label>
                @if($location->children->isNotEmpty())
                    <input type="text"
                           value="{{ $location->parent ? $location->parent->getFullPath() : __('locations.none_top_level') }}"
                           disabled
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-500">
                    <input type="hidden" name="parent_id" value="{{ $location->parent_id }}">
                    <p class="text-xs text-gray-500 mt-1">{{ __('locations.parent_readonly_has_children') }}</p>
                @else
                    <select name="parent_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('parent_id') border-red-500 @enderror">
                        <option value="">{{ __('locations.none_top_level') }}</option>
                        @foreach($availableParents as $parent)
                            @if($parent->id !== $location->id)
                                <option value="{{ $parent->id }}" {{ old('parent_id', $location->parent_id) == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->getFullPath() }} ({{ ucfirst(strtolower($parent->type)) }})
                                </option>
                            @endif
                        @endforeach
                    </select>
                    @error('parent_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                @endif
            </div>

            <!-- Capacity -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('locations.capacity') }}
                    <span class="text-xs text-gray-500">({{ __('locations.optional') }})</span>
                </label>
                <input type="number"
                       name="capacity"
                       value="{{ old('capacity', $location->capacity) }}"
                       min="{{ $location->getCurrentOccupancy() }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('capacity') border-red-500 @enderror">
                <p class="text-xs text-gray-500 mt-1">
                    {{ __('locations.capacity_help') }}
                    @if($location->getCurrentOccupancy() > 0)
                        <br>{{ __('locations.current_occupancy') }}: {{ $location->getCurrentOccupancy() }} {{ __('locations.batches') }}
                    @endif
                </p>
                @error('capacity')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Temperature Control Section -->
            <div class="border-t pt-6" x-data="{ checked: {{ old('temperature_controlled', $location->temperature_controlled) ? 'true' : 'false' }} }">
                <div class="flex items-center mb-4">
                    <input type="checkbox"
                           name="temperature_controlled"
                           id="temperature_controlled"
                           value="1"
                           x-model="checked"
                           {{ old('temperature_controlled', $location->temperature_controlled) ? 'checked' : '' }}
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
                               value="{{ old('temperature_min', $location->temperature_min) }}"
                               step="0.1"
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
                               value="{{ old('temperature_max', $location->temperature_max) }}"
                               step="0.1"
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
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('notes') border-red-500 @enderror">{{ old('notes', $location->notes) }}</textarea>
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
                       {{ old('is_active', $location->is_active) ? 'checked' : '' }}
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="is_active" class="ml-2 block text-sm font-medium text-gray-700">
                    {{ __('locations.is_active') }}
                </label>
            </div>

            <!-- Form Actions -->
            <div class="flex gap-3 pt-6 border-t">
                <a href="{{ route('locations.show', $location) }}"
                   class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium text-center">
                    {{ __('common.cancel') }}
                </a>
                <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium">
                    {{ __('locations.update_location') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Delete Location (if no children and no batches) -->
    @if($location->children->isEmpty() && $location->batches->isEmpty())
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-red-600 mb-2">{{ __('locations.danger_zone') }}</h3>
            <p class="text-sm text-gray-600 mb-4">{{ __('locations.delete_warning') }}</p>
            <form action="{{ route('locations.destroy', $location) }}" method="POST" onsubmit="return handleDeleteLocation(event)">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium">
                    {{ __('locations.delete_location') }}
                </button>
            </form>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
async function handleDeleteLocation(event) {
    event.preventDefault();

    const confirmed = await showConfirm(
        '{{ __('locations.confirm_delete') }}',
        '{{ __('locations.delete_location') }}'
    );

    if (confirmed) {
        event.target.submit();
    }

    return false;
}
</script>
@endpush

@endsection
