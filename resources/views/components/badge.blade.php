@props(['value'])

@php
    $label = match($value) {
        1 => 'Buruk',
        2 => 'Cukup',
        3 => 'Baik',
        default => 'N/A',
    };

    $color = match($value) {
        1 => '#e74c3c',    // Red
        2 => '#f39c12',    // Orange
        3 => '#2ecc71',    // Green
        default => '#bdc3c7', // Gray
    };
@endphp

<span style="
    display: inline-block;
    padding: 6px 12px;
    font-size: 13px;
    font-weight: bold;
    color: #fff;
    background-color: {{ $color }};
    border-radius: 999px;
">
    {{ $label }}
</span>
