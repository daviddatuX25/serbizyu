<div>
    <!-- Chat Popup Container -->
    <div class="fixed bottom-0 right-0 z-40 p-4">
        @if($isOpen && !$isMinimized)
            <!-- Full Chat Window -->
            <div class="bg-white rounded-lg shadow-2xl overflow-hidden flex flex-col"
                 style="width: 400px; height: 600px;"
                 x-data="{ shown: true }"
                 x-show="shown"
                 x-transition>

                <!-- Chat Header -->
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-4 py-3 flex items-center justify-between cursor-pointer hover:from-blue-600 hover:to-blue-700 transition"
                     wire:click="toggleMinimize">
                    <div>
                        <h3 class="font-semibold text-sm">{{ $title }}</h3>
                        <p class="text-xs opacity-90">Online</p>
                    </div>
                    <button wire:click.stop="close" class="hover:bg-blue-700 p-1 rounded transition ml-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Chat Content -->
                @if($thread)
                    <div class="flex-1 overflow-hidden">
                        <livewire:chat-interface :thread="$thread" />
                    </div>
                @else
                    <div class="flex-1 flex items-center justify-center bg-gray-50">
                        <p class="text-gray-500 text-sm">No conversation available</p>
                    </div>
                @endif
            </div>
        @elseif($isMinimized)
            <!-- Minimized Header -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-t-lg px-4 py-3 flex items-center justify-between cursor-pointer shadow-lg hover:from-blue-600 hover:to-blue-700 transition"
                 style="width: 300px;"
                 wire:click="toggleMinimize">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                    <span class="font-semibold text-sm">{{ $title }}</span>
                </div>
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                    </svg>
                    <button wire:click.stop="close" class="hover:bg-blue-700 p-1 rounded transition ml-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @else
            <!-- Chat Button (Closed) -->
            <button wire:click="toggleChat"
                    class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-full p-4 shadow-lg hover:shadow-xl transition transform hover:scale-110"
                    title="Open Chat">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"></path>
                </svg>
            </button>
        @endif
    </div>

    <!-- Backdrop (when chat is open) -->
    @if($isOpen && !$isMinimized)
        <div class="fixed inset-0 bg-black bg-opacity-30 z-30 md:hidden"
             wire:click="close"
             x-transition></div>
    @endif

    <!-- Alpine.js initialization for animations -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Any additional initialization can go here
        });
    </script>
</div>
