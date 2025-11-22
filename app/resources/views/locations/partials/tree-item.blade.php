@php
    $occupancy = $location->getCurrentOccupancy();
    $capacity = $location->capacity;
    $percentage = $location->getOccupancyPercentage();
    $isFull = $location->isFull();
@endphp

<div class="mb-2" x-data="{ open: {{ $level === 0 ? 'true' : 'false' }} }">
    <div class="flex items-center justify-between p-3 rounded-lg border {{ $isFull ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-gray-50' }} hover:bg-gray-100 transition"
         style="margin-left: {{ $level * 24 }}px">
        <div class="flex items-center gap-3 flex-1">
            <!-- Expand/Collapse Toggle -->
            @if($location->children->isNotEmpty())
                <button @click="open = !open" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            @else
                <div class="w-5"></div>
            @endif

            <!-- Location Type Icon -->
            <div class="p-2 rounded-lg
                {{ $location->type === 'RACK' ? 'bg-blue-100 text-blue-600' : '' }}
                {{ $location->type === 'SHELF' ? 'bg-green-100 text-green-600' : '' }}
                {{ $location->type === 'BIN' ? 'bg-purple-100 text-purple-600' : '' }}
                {{ in_array($location->type, ['FLOOR', 'COUNTER', 'WAREHOUSE']) ? 'bg-gray-100 text-gray-600' : '' }}
                {{ $location->type === 'REFRIGERATOR' ? 'bg-cyan-100 text-cyan-600' : '' }}">
                @if($location->type === 'RACK')
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                @elseif($location->type === 'SHELF')
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                @elseif($location->type === 'BIN')
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                @elseif($location->type === 'REFRIGERATOR')
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                @else
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                @endif
            </div>

            <!-- Location Info -->
            <div class="flex-1">
                <div class="flex items-center gap-2">
                    <h4 class="font-semibold text-gray-900">{{ $location->name }}</h4>
                    <span class="text-xs text-gray-500">{{ $location->code }}</span>
                    @if($location->temperature_controlled)
                        <span class="px-2 py-0.5 text-xs font-medium bg-cyan-100 text-cyan-700 rounded">
                            {{ $location->temperature_min }}°C - {{ $location->temperature_max }}°C
                        </span>
                    @endif
                </div>
                <p class="text-sm text-gray-600">
                    {{ ucfirst(strtolower($location->type)) }}
                    @if($capacity)
                        • {{ __('locations.occupancy') }}: {{ $occupancy }}/{{ $capacity }}
                        ({{ number_format($percentage, 0) }}%)
                    @endif
                </p>
            </div>

            <!-- Capacity Bar (for locations with capacity) -->
            @if($capacity)
                <div class="w-32">
                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full transition-all
                            {{ $percentage >= 100 ? 'bg-red-500' : '' }}
                            {{ $percentage >= 75 && $percentage < 100 ? 'bg-yellow-500' : '' }}
                            {{ $percentage < 75 ? 'bg-green-500' : '' }}"
                             style="width: {{ min($percentage, 100) }}%">
                        </div>
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex items-center gap-2">
                <a href="{{ route('locations.show', $location) }}" class="text-blue-600 hover:text-blue-800" title="{{ __('common.view') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </a>
                @canany(['owner', 'manager'], App\Models\User::class)
                <a href="{{ route('locations.edit', $location) }}" class="text-green-600 hover:text-green-800" title="{{ __('common.edit') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </a>
                @endcanany
            </div>
        </div>
    </div>

    <!-- Children (recursive) -->
    @if($location->children->isNotEmpty())
        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            @foreach($location->children as $child)
                @include('locations.partials.tree-item', ['location' => $child, 'level' => $level + 1])
            @endforeach
        </div>
    @endif
</div>
