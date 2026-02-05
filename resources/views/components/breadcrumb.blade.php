@props(['items'])

<nav class="mb-6 text-sm text-gray-500">
    <ol class="flex items-center space-x-2">
        @foreach ($items as $item)
            <li class="flex items-center">
                @if (!$loop->first)
                    <svg class="h-4 w-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5l7 7-7 7"/>
                    </svg>
                @endif

                @if (isset($item['url']))
                    <a href="{{ $item['url'] }}" class="hover:text-blue-600">
                        {{ $item['label'] }}
                    </a>
                @else
                    <span class="text-gray-700 font-medium">
                        {{ $item['label'] }}
                    </span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
