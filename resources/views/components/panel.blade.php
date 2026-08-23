@props(['class' => ''])

<div class="bg-white/5 rounded-xl p-6 border border-transparent hover:border-blue-800 transition-colors duration-300 group {{ $class }}">
    {{ $slot }}
</div>