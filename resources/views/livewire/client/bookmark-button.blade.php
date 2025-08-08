<div>
<button 
    wire:click="toggleBookmark" 
    class="bg-white bg-opacity-80 hover:bg-opacity-100 p-2 rounded-full shadow-sm transition-all duration-200 group {{ $isBookmarked ? 'text-yellow-500' : 'text-gray-600' }}"
    wire:loading.attr="disabled"
>
    <div wire:loading.remove>
        <i class="fas fa-bookmark text-sm {{ $isBookmarked ? 'text-yellow-500' : 'group-hover:text-yellow-400' }}"></i>
    </div>
    <div wire:loading class="text-gray-400">
        <i class="fas fa-spinner fa-spin text-sm"></i>
    </div>
</button>

</div>
