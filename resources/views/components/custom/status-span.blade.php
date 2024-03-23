@props(['status'])

@php
    $statusText = [
        0 => ['text' => __('status.pending'), 'class' => 'warning'],
        1 => ['text' => __('status.approved'), 'class' => 'success'],
        2 => ['text' => __('status.rejected'), 'class' => 'danger'],
        3 => ['text' => __('status.removed'), 'class' => 'danger'],
    ];

    $statusData = $statusText[$status];
@endphp

<span class="badge badge-pill bg-{{ $statusData['class'] }}">{{ $statusData['text'] }}</span>
