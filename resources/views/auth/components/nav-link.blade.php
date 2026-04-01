<a {{ $attributes }}
   class="{{ $active ? 'bg-red-600 text-white' : 'text-gray-700 hover:text-red-600' }}
   px-3 py-2 rounded-md text-sm font-medium">
   {{ $slot }}
</a>