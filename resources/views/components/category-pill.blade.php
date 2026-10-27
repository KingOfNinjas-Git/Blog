@props(['href', 'active' => false])
{{-- One category in the bar at the top of the list; the chosen one is highlighted. --}}
<a href="{{ $href }}" @if ($active) aria-current="page" @endif
   {{ $attributes->class([
        'shrink-0 whitespace-nowrap rounded-full px-6 py-2 font-semibold shadow-md transition-all duration-300',
        'bg-blue-900 text-white' => $active,
        'bg-white text-blue-700 hover:bg-blue-600 hover:text-white' => ! $active,
   ]) }}>{{ $slot }}</a>
