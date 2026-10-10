@props(['size' => 'h-32 w-32'])

{{-- Decorative floating ring. Purely visual. --}}
<div
    {{ $attributes->merge(['class' => "sa-ring {$size}"]) }}
    aria-hidden="true"
></div>
