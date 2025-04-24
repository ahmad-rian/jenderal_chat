<div class="flex items-center p-2 bg-gray-100 dark:bg-gray-800 rounded">
    <input type="text" wire:model="message" placeholder="Type your message..."
        class="flex-1 px-4 py-2 text-sm bg-white dark:bg-gray-700 rounded-l focus:outline-none focus:ring focus:ring-blue-300" />
    <button wire:click="sendMessage"
        class="px-4 py-2 text-white bg-blue-500 hover:bg-blue-600 rounded-r focus:outline-none focus:ring focus:ring-blue-300">
        Send
    </button>
</div>
