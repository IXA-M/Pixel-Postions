@php
    $method = strtoupper($attributes->get('method', 'GET'));
    $htmlMethod = in_array($method, ['GET', 'POST']) ? $method : 'POST';
@endphp

<form {{ $attributes->except('method')->merge(["class" => "max-w-2xl mx-auto space-y-6", "method" => $htmlMethod]) }}>
    @if ($method !== 'GET')
        @csrf
    @endif

    @if (! in_array($method, ['GET', 'POST']))
        @method($method)
    @endif

    {{ $slot }}
</form>
