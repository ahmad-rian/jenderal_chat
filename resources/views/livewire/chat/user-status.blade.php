<div class="text-xs text-base-content/70" wire:poll.10s>
    @if ($isOnline)
        <span class="text-success flex items-center">
            <span class="w-2 h-2 bg-success rounded-full mr-1"></span>
            Online
        </span>
    @else
        <span class="flex items-center">
            <span class="w-2 h-2 bg-base-300 rounded-full mr-1"></span>
            {{ $statusText }}
        </span>
    @endif
</div>
