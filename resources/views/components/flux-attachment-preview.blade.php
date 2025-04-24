@props(['attachment'])

@if (Str::startsWith($attachment->file_type, 'image'))
    <div class="chat-image-preview">
        <a href="{{ Storage::url($attachment->file_path) }}" target="_blank">
            <img src="{{ Storage::url($attachment->file_path) }}" alt="{{ $attachment->file_name }}">
        </a>
        <div class="flex justify-between items-center mt-1 text-xs opacity-75">
            <span class="truncate max-w-40">{{ $attachment->file_name }}</span>
            <a href="{{ Storage::url($attachment->file_path) }}" download class="ml-2">
                <i class="fas fa-download"></i>
            </a>
        </div>
    </div>
@else
    <div class="chat-attachment">
        <div class="flex-shrink-0">
            @if (Str::startsWith($attachment->file_type, 'video'))
                <i class="fas fa-video"></i>
            @elseif(Str::startsWith($attachment->file_type, 'audio'))
                <i class="fas fa-music"></i>
            @elseif(Str::startsWith($attachment->file_type, 'application/pdf'))
                <i class="fas fa-file-pdf"></i>
            @elseif(Str::startsWith($attachment->file_type, 'application/msword') ||
                    Str::startsWith($attachment->file_type, 'application/vnd.openxmlformats-officedocument.wordprocessingml'))
                <i class="fas fa-file-word"></i>
            @elseif(Str::startsWith($attachment->file_type, 'application/vnd.ms-excel') ||
                    Str::startsWith($attachment->file_type, 'application/vnd.openxmlformats-officedocument.spreadsheetml'))
                <i class="fas fa-file-excel"></i>
            @else
                <i class="fas fa-file"></i>
            @endif
        </div>
        <div class="flex-1 truncate">
            <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="hover:underline">
                {{ $attachment->file_name }}
                <div class="text-xs opacity-75">
                    {{ round($attachment->file_size / 1024, 2) }} KB
                </div>
            </a>
        </div>
        <a href="{{ Storage::url($attachment->file_path) }}" download>
            <i class="fas fa-download"></i>
        </a>
    </div>
@endif
