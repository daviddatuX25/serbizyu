<div>
    <!-- Chat Popup with Dynamic UI -->
    <livewire:chat-popup
        :thread="$thread"
        title="Work Discussion"
        type="work"
    />

    <!-- Fallback inline chat for non-popup pages -->
    @if($thread && request()->query('view') === 'inline')
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
            <div class="p-6">
                <!-- Chat Header -->
                <div class="pb-4 border-b border-gray-200 mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Work Discussion</h3>
                    <p class="text-sm text-gray-600">
                        Chat with team members about this work instance
                    </p>
                </div>

                <!-- Chat Interface Component -->
                <livewire:chat-interface :thread="$thread" />
            </div>
        </div>
    @elseif(!$thread)
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-center">
                <p class="text-gray-600">No conversation yet. Start messaging!</p>
            </div>
        </div>
    @endif
</div>
