{{-- Reusable Flag Modal Component --}}
@props(['contentType' => 'Order', 'showTrigger' => true])

<div x-data="{
    showModal: false,
    flaggedId: null,
    flaggedTitle: '',
    category: '',
    reason: '',
    isSubmitting: false,
    successMessage: '',
    errorMessage: '',
    resetForm() {
        this.category = '';
        this.reason = '';
        this.flaggedId = null;
        this.flaggedTitle = '';
        this.successMessage = '';
        this.errorMessage = '';
    },
    async handleSubmit(e) {
        this.isSubmitting = true;
        this.successMessage = '';
        this.errorMessage = '';

        // Wait for form submission, then show success
        setTimeout(() => {
            this.successMessage = 'Thank you for reporting! Our team will review this shortly.';
            setTimeout(() => {
                this.showModal = false;
                this.resetForm();
            }, 2000);
            this.isSubmitting = false;
        }, 500);
    }
}"
     @open-flag-modal.window="showModal = true; flaggedId = $event.detail.id; flaggedTitle = $event.detail.title"
     class="fixed inset-0 z-50 overflow-y-auto"
     x-show="showModal"
     style="display: none;">

    <!-- Backdrop -->
    <div @click="showModal = false; resetForm()" class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

    <!-- Modal Content -->
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full border border-gray-200"
             @click.stop
             x-transition>

            <!-- Success Message -->
            <div x-show="successMessage" x-transition class="bg-green-50 border-b border-green-200 p-4">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="text-sm font-medium text-green-800" x-text="successMessage"></p>
                </div>
            </div>

            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200" x-show="!successMessage">
                <h3 class="text-lg font-semibold text-gray-900">Report {{ $contentType }}</h3>
                <button type="button"
                        @click="showModal = false; resetForm()"
                        class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form method="POST" action="{{ route('admin.flags.store') }}"
                  @submit.prevent="handleSubmit($event); $el.submit();"
                  class="space-y-4 p-6"
                  x-show="!successMessage">
                @csrf

                <!-- Content Type Hidden Fields -->
                @if($contentType === 'Order')
                    <input type="hidden" name="flaggable_type" value="App\Domains\Orders\Models\Order">
                @elseif($contentType === 'Service')
                    <input type="hidden" name="flaggable_type" value="App\Domains\Listings\Models\Service">
                @elseif($contentType === 'OpenOffer')
                    <input type="hidden" name="flaggable_type" value="App\Domains\Listings\Models\OpenOffer">
                @elseif($contentType === 'Review')
                    <input type="hidden" name="flaggable_type" value="App\Domains\Reviews\Models\Review">
                @endif

                <input type="hidden" name="flaggable_id" :value="flaggedId">

                <!-- Item Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ $contentType }}</label>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-2">
                        <p class="text-gray-900 font-medium truncate" x-text="flaggedTitle"></p>
                    </div>
                </div>

                <!-- Category Selection -->
                <div>
                    <label for="flag-category" class="block text-sm font-medium text-gray-700 mb-2">
                        Reason <span class="text-red-500">*</span>
                    </label>
                    <select name="category"
                            id="flag-category"
                            x-model="category"
                            required
                            :disabled="isSubmitting"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 disabled:opacity-50">
                        <option value="">-- Select a reason --</option>
                        <option value="spam">Spam</option>
                        <option value="inappropriate">Inappropriate Content</option>
                        <option value="fraud">Fraud or Scam</option>
                        <option value="misleading_info">Misleading Information</option>
                        <option value="copyright_violation">Copyright Violation</option>
                        <option value="other">Other</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Reason Text Area -->
                <div>
                    <label for="flag-reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Details <span class="text-red-500">*</span>
                    </label>
                    <textarea name="reason"
                              id="flag-reason"
                              x-model="reason"
                              required
                              maxlength="500"
                              rows="4"
                              :disabled="isSubmitting"
                              placeholder="Please provide details about why you're reporting this..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 disabled:opacity-50"></textarea>
                    <p class="mt-1 text-xs text-gray-500" x-text="`${reason.length} / 500 characters`"></p>
                    @error('reason')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Info Alert -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <p class="text-xs text-blue-800">
                        <strong>Note:</strong> Your report is important to us. Our moderation team will review this report promptly.
                    </p>
                </div>

                <!-- Modal Actions -->
                <div class="flex gap-3 pt-4 border-t border-gray-200">
                    <button type="button"
                            @click="showModal = false; resetForm()"
                            :disabled="isSubmitting"
                            class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition disabled:opacity-50">
                        Cancel
                    </button>
                    <button type="submit"
                            :disabled="isSubmitting || !category || !reason"
                            class="flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition disabled:opacity-50 flex items-center justify-center gap-2">
                        <span x-show="!isSubmitting">Report</span>
                        <span x-show="isSubmitting" class="inline-flex gap-1">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Submitting
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
