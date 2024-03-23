@props(['user' => auth()->user(), 'size' => 50, 'id' => 'userPicture'])

<img src="{{ $user->picture ? asset($user->picture) : asset('asset/default.png') }}" alt="User profile picture"
    {{ $attributes }} id="{{ $id }}"
    style="width: {{ $size }}px; height: {{ $size }}px; border-radius: 50%; border: 2px solid;margin:0 auto;">
