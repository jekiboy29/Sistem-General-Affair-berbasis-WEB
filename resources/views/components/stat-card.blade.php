{{-- 👇 fallback dulu, sebelum props dieksekusi --}}
@php
    if (!isset($attributes)) {
        $attributes = new \Illuminate\View\ComponentAttributeBag();
    }
@endphp

@props([
    'title' => 'Tanpa Judul',
    'value' => 0,
    'color' => 'purple',
    'icon' => '📦',
])

@php
    // mapping warna biar Tailwind-nya aman di build
    $colorClasses = [
        'purple' => ['bg' => 'bg-purple-100', 'border' => 'border-purple-600', 'text' => 'text-purple-700'],
        'green'  => ['bg' => 'bg-green-100',  'border' => 'border-green-600',  'text' => 'text-green-700'],
        'yellow' => ['bg' => 'bg-yellow-100', 'border' => 'border-yellow-600', 'text' => 'text-yellow-700'],
        'red'    => ['bg' => 'bg-red-100',    'border' => 'border-red-600',    'text' => 'text-red-700'],
    ];

    $classes = $colorClasses[$color] ?? $colorClasses['purple'];
@endphp

<div {{ $attributes->merge(['class' => "{$classes['bg']} {$classes['border']} border-l-4 p-4 rounded-xl shadow text-center"]) }}>
    <div class="text-3xl mb-2">{{ $icon }}</div>
    <h2 class="text-gray-600 font-semibold">{{ $title }}</h2>
    <p class="text-2xl font-bold {{ $classes['text'] }}">{{ $value }}</p>
</div>
