@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between mt-4">
        <p class="text-sm text-gray-600">
            {{ __('Showing page') }} <span class="font-semibold">{{ $paginator->currentPage() }}</span>
            {{ __('of') }} <span class="font-semibold">{{ $paginator->lastPage() }}</span>
        </p>

        <div class="flex space-x-2">
            @if ($paginator->onFirstPage())
                <span class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-300 rounded-lg cursor-default">
                    {{ __('Previous') }}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="px-4 py-2 text-sm font-medium text-white bg-green-700 border border-green-700 rounded-lg hover:bg-green-800">
                    {{ __('Previous') }}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="px-4 py-2 text-sm font-medium text-white bg-green-700 border border-green-700 rounded-lg hover:bg-green-800">
                    {{ __('Next') }}
                </a>
            @else
                <span class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-300 rounded-lg cursor-default">
                    {{ __('Next') }}
                </span>
            @endif
        </div>
    </nav>
@endif
