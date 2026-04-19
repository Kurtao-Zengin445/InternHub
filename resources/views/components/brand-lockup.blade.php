@props([
    'theme' => 'light',
    'size' => 'md',
    'subtitle' => 'Sistem Manajemen Magang',
])

@php
    $themes = [
        'light' => [
            'markBg' => 'bg-white/10 ring-1 ring-white/15',
            'markText' => 'text-white',
            'titleText' => 'text-white',
            'subtitleText' => 'text-slate-300',
        ],
        'dark' => [
            'markBg' => 'bg-slate-100',
            'markText' => 'text-blue-700',
            'titleText' => 'text-slate-950',
            'subtitleText' => 'text-slate-500',
        ],
    ][$theme] ?? [
        'markBg' => 'bg-white/10 ring-1 ring-white/15',
        'markText' => 'text-white',
        'titleText' => 'text-white',
        'subtitleText' => 'text-slate-300',
    ];

    $sizes = [
        'sm' => [
            'wrapper' => 'gap-2.5',
            'mark' => 'h-10 w-10 rounded-xl',
            'icon' => 'h-5 w-5',
            'title' => 'text-lg',
            'subtitle' => 'text-[11px]',
        ],
        'md' => [
            'wrapper' => 'gap-3',
            'mark' => 'h-14 w-14 rounded-2xl',
            'icon' => 'h-8 w-8',
            'title' => 'text-2xl',
            'subtitle' => 'text-sm',
        ],
    ][$size] ?? [
        'wrapper' => 'gap-3',
        'mark' => 'h-14 w-14 rounded-2xl',
        'icon' => 'h-8 w-8',
        'title' => 'text-2xl',
        'subtitle' => 'text-sm',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center no-underline '.$sizes['wrapper']]) }}>
    <span class="flex items-center justify-center {{ $sizes['mark'] }} {{ $themes['markBg'] }} {{ $themes['markText'] }}">
        <x-application-logo class="{{ $sizes['icon'] }}" />
    </span>
    <span class="flex flex-col">
        <span class="{{ $sizes['title'] }} font-extrabold tracking-tight {{ $themes['titleText'] }}" style="font-family:'Montserrat',sans-serif;">InternHub</span>
        <span class="{{ $sizes['subtitle'] }} {{ $themes['subtitleText'] }}">{{ $subtitle }}</span>
    </span>
</span>
