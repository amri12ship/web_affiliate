@props(['size' => 'h-24 w-24'])

{{-- Decorative rounded geometric shape. Purely visual. --}}
<div
    {{ $attributes->merge(['class' => "sa-shape {$size}"]) }}
    aria-hidden="true"
></div>
