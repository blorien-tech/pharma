@extends('layouts.app')

@section('title', __('locations.title') . ' - ' . __('navigation.app_name'))

@section('content')
<div class="space-y-6" x-data="locationManager()">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('locations.title') }}</h1>
            <p class="mt-1 text-sm text-gray-600">{{ __('locations.subtitle') }}</p>
        </div>
        @canany(['owner', 'manager'], App\Models\User::class)
        <div class="flex gap-3">
            <button @click="showQuickHierarchyModal = true" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                {{ __('locations.quick_hierarchy') }}
            </button>
            <a href="{{ route('locations.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                {{ __('locations.add_location') }}
            </a>
        </div>
        @endcanany
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Total Locations -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">{{ __('locations.total_locations') }}</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['total_locations'] }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Occupied Locations -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">{{ __('locations.occupied') }}</p>
                    <p class="mt-2 text-3xl font-bold text-green-600">{{ $stats['occupied_locations'] }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
            <p class="mt-2 text-xs text-gray-500">{{ __('locations.of') }} {{ $stats['total_locations'] }} {{ __('locations.locations') }}</p>
        </div>

        <!-- Total Batches Stored -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">{{ __('locations.total_batches') }}</p>
                    <p class="mt-2 text-3xl font-bold text-indigo-600">{{ $stats['total_batches'] }}</p>
                </div>
                <div class="p-3 bg-indigo-100 rounded-full">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
            <p class="mt-2 text-xs text-gray-500">{{ __('locations.across_all_locations') }}</p>
        </div>

        <!-- Alerts -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">{{ __('locations.alerts') }}</p>
                    <p class="mt-2 text-3xl font-bold {{ $stats['locations_needing_attention'] > 0 ? 'text-red-600' : 'text-gray-400' }}">
                        {{ $stats['locations_needing_attention'] }}
                    </p>
                </div>
                <div class="p-3 {{ $stats['locations_needing_attention'] > 0 ? 'bg-red-100' : 'bg-gray-100' }} rounded-full">
                    <svg class="w-8 h-8 {{ $stats['locations_needing_attention'] > 0 ? 'text-red-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            <p class="mt-2 text-xs text-gray-500">{{ __('locations.need_attention') }}</p>
        </div>
    </div>

    <!-- Important Alerts Banner -->
    @if($locationsNeedingAttention->isNotEmpty())
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-yellow-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div class="flex-1">
                <h3 class="text-sm font-medium text-yellow-800 mb-2">{{ __('locations.attention_required') }}</h3>
                <div class="space-y-1">
                    @foreach($locationsNeedingAttention->take(5) as $item)
                        <div class="text-sm text-yellow-700">
                            <a href="{{ route('locations.show', $item['location']) }}" class="font-medium hover:underline">
                                {{ $item['location']->getFullPath() }}
                            </a>:
                            @foreach($item['alerts'] as $alert)
                                <span class="inline-block px-2 py-0.5 text-xs rounded
                                    {{ $alert['severity'] === 'danger' ? 'bg-red-100 text-red-700' : '' }}
                                    {{ $alert['severity'] === 'warning' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $alert['severity'] === 'info' ? 'bg-blue-100 text-blue-700' : '' }}">
                                    {{ $alert['message'] }}
                                </span>
                            @endforeach
                        </div>
                    @endforeach
                </div>
                @if($locationsNeedingAttention->count() > 5)
                <p class="text-xs text-yellow-600 mt-2">{{ __('locations.and_more', ['count' => $locationsNeedingAttention->count() - 5]) }}</p>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('locations.index') }}" class="flex gap-3">
            <input type="text"
                   name="search"
                   placeholder="{{ __('locations.search_locations') }}"
                   value="{{ request('search') }}"
                   class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                {{ __('common.search') }}
            </button>
            @if(request('search'))
            <a href="{{ route('locations.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium">
                {{ __('common.clear') }}
            </a>
            @endif
        </form>
    </div>

    <!-- Location Hierarchy Tree -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('locations.location_hierarchy') }}</h2>
        </div>
        <div class="p-6">
            @forelse($locationTree as $location)
                @include('locations.partials.tree-item', ['location' => $location, 'level' => 0])
            @empty
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('locations.no_locations') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('locations.get_started') }}</p>
                    @canany(['owner', 'manager'], App\Models\User::class)
                    <div class="mt-6 flex justify-center gap-3">
                        <button @click="showQuickHierarchyModal = true" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                            {{ __('locations.quick_hierarchy') }}
                        </button>
                        <a href="{{ route('locations.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            {{ __('locations.add_location') }}
                        </a>
                    </div>
                    @endcanany
                </div>
            @endforelse
        </div>
    </div>

    <!-- Quick Hierarchy Creation Modal -->
    @canany(['owner', 'manager'], App\Models\User::class)
    <div x-show="showQuickHierarchyModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 px-4"
         @click.self="showQuickHierarchyModal = false"
         style="display: none;">
        <div x-show="showQuickHierarchyModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90"
             class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden"
             @click.stop>
            <form @submit.prevent="createQuickHierarchy()">
                <!-- Header -->
                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-white">{{ __('locations.quick_hierarchy') }}</h3>
                            <p class="text-sm text-purple-100">{{ __('locations.quick_hierarchy_desc') }}</p>
                        </div>
                        <button @click="showQuickHierarchyModal = false" type="button" class="text-white hover:text-gray-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('locations.rack_name') }} *</label>
                        <input type="text"
                               x-model="hierarchyForm.rack_name"
                               required
                               placeholder="{{ __('locations.rack_name_example') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('locations.shelf_count') }} *</label>
                        <input type="number"
                               x-model.number="hierarchyForm.shelf_count"
                               required
                               min="1"
                               max="20"
                               placeholder="5"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('locations.bins_per_shelf') }} *</label>
                        <input type="number"
                               x-model.number="hierarchyForm.bin_count"
                               required
                               min="1"
                               max="20"
                               placeholder="4"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('locations.bin_capacity') }} *</label>
                        <input type="number"
                               x-model.number="hierarchyForm.bin_capacity"
                               required
                               min="1"
                               placeholder="10"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>

                    <!-- Preview -->
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-700 mb-2">{{ __('locations.preview') }}:</p>
                        <p class="text-sm text-gray-600">
                            {{ __('locations.will_create') }}:
                            <span class="font-semibold" x-text="1 + (hierarchyForm.shelf_count || 0) + ((hierarchyForm.shelf_count || 0) * (hierarchyForm.bin_count || 0))"></span>
                            {{ __('locations.total_locations') }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            1 {{ __('locations.rack') }} + <span x-text="hierarchyForm.shelf_count || 0"></span> {{ __('locations.shelves') }} +
                            <span x-text="(hierarchyForm.shelf_count || 0) * (hierarchyForm.bin_count || 0)"></span> {{ __('locations.bins') }}
                        </p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 border-t flex gap-3 justify-end">
                    <button @click="showQuickHierarchyModal = false"
                            type="button"
                            :disabled="hierarchyProcessing"
                            class="px-6 py-2.5 border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50">
                        {{ __('common.cancel') }}
                    </button>
                    <button type="submit"
                            :disabled="hierarchyProcessing"
                            :class="hierarchyProcessing ? 'bg-gray-400' : 'bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700'"
                            class="px-6 py-2.5 text-white rounded-lg font-medium disabled:opacity-50">
                        <span x-show="!hierarchyProcessing">{{ __('locations.create_hierarchy') }}</span>
                        <span x-show="hierarchyProcessing">{{ __('common.processing') }}...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endcanany
</div>

@push('scripts')
<script>
function locationManager() {
    return {
        showQuickHierarchyModal: false,
        hierarchyProcessing: false,
        hierarchyForm: {
            rack_name: '',
            shelf_count: 5,
            bin_count: 4,
            bin_capacity: 10
        },

        async createQuickHierarchy() {
            if (this.hierarchyProcessing) return;

            this.hierarchyProcessing = true;

            try {
                const response = await fetch('{{ route('locations.quick-hierarchy') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.hierarchyForm)
                });

                const data = await response.json();

                if (response.ok) {
                    window.showSuccess(
                        data.message || '{{ __('locations.hierarchy_created') }}',
                        '{{ __('common.success') }}'
                    );
                    this.showQuickHierarchyModal = false;
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    window.showError(
                        data.message || '{{ __('common.operation_failed') }}',
                        '{{ __('common.error') }}'
                    );
                }
            } catch (error) {
                console.error('Hierarchy creation error:', error);
                window.showError(
                    '{{ __('common.operation_failed') }}',
                    '{{ __('common.error') }}'
                );
            } finally {
                this.hierarchyProcessing = false;
            }
        }
    };
}
</script>
@endpush

@endsection
