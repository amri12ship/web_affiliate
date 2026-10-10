@props(['color' => 'orange', 'size' => 'h-40 w-40'])

{{-- Decorative 3D sphere rendered purely with CSS. Purely visual. --}}
<div
    {{ $attributes->merge(['class' => "sa-sphere sa-sphere--{$color} {$size}"]) }}
    aria-hidden="true"
></div>
