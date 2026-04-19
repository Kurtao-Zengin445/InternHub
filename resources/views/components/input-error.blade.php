@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'space-y-1 text-sm text-rose-600']) }}>
        @foreach ((array) $messages as $message)
            <li class="flex items-start gap-2">
                <span class="mt-[2px] inline-block h-2 w-2 flex-shrink-0 rounded-full bg-rose-500"></span>
                <span>{{ $message }}</span>
            </li>
        @endforeach
    </ul>
@endif
