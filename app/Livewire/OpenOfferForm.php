<?php

namespace App\Livewire;

use App\Domains\Common\Interfaces\AddressProviderInterface;
use App\Domains\Listings\Models\OpenOffer;
use App\Enums\PaymentMethod;
use App\Livewire\Traits\Addressable;
use App\Livewire\Traits\WithMedia;
use App\Livewire\Traits\Workflowable;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class OpenOfferForm extends Component
{
    use Addressable;
    use WithMedia;
    use Workflowable;

    public ?OpenOffer $offer = null;

    public string $title = '';

    public ?string $description = null;

    public ?float $budget = null;

    public ?int $category_id = null;

    public bool $pay_first = false;

    public ?string $payment_method = 'any';

    public ?int $address_id = null;

    public ?string $deadline = null;

    public string $deadline_option = '7';

    public array $categories = [];

    public array $paymentMethods = [];

    public function boot(AddressProviderInterface $addressProvider)
    {
        $this->bootAddressable($addressProvider);
    }

    protected function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'budget' => 'required|numeric|min:1',
            'category_id' => 'required|integer|exists:categories,id',
            'workflow_template_id' => 'nullable|integer|exists:workflow_templates,id',
            'pay_first' => 'nullable|boolean',
            'payment_method' => 'required|in:cash,online,any',
            'address_id' => 'nullable|integer|exists:addresses,id',
            'deadline_option' => 'required|in:1,3,7,14,30',
        ];

        if ($this->showAddressModal) {
            $rules = array_merge($rules, $this->addressValidationRules());
        }

        return $rules;
    }

    protected $listeners = [
        // you can listen to custom events if needed
    ];

    public function mount(?OpenOffer $offer = null, ?Collection $addresses = null)
    {
        $this->offer = $offer;
        $this->mountAddressable($addresses ?? new Collection);
        $this->mountWorkflowable($offer);
        $this->paymentMethods = PaymentMethod::options();

        $this->categories = app(\App\Domains\Listings\Services\CategoryService::class)->listAllCategories()->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])->toArray();

        if ($this->offer && $this->offer->exists) {
            $this->title = $this->offer->title;
            $this->description = $this->offer->description;
            $this->budget = $this->offer->budget;
            $this->category_id = $this->offer->category_id;
            $this->pay_first = (bool) $this->offer->pay_first;
            $this->payment_method = $this->offer->payment_method?->value ?? 'any';
            $this->address_id = $this->offer->address_id;
            $this->deadline = $this->offer->deadline ? $this->offer->deadline->format('Y-m-d') : null;

            $this->loadExistingMedia($this->offer);
        } else {
            $this->payment_method = 'any';

            // Check if workflow_id is passed in request
            if (request()->has('workflow_id')) {
                $this->workflow_template_id = request('workflow_id');
            }
        }
    }

    public function save()
    {
        if (empty($this->address_id) && ! $this->showAddressModal) {
            $this->addError('address_id', 'Please select an address or add a new one.');

            return;
        }

        $this->validate();

        $openOfferService = app(\App\Domains\Listings\Services\OpenOfferService::class);

        $deadline = now()->addDays((int) $this->deadline_option);

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'budget' => $this->budget,
            'category_id' => $this->category_id,
            'workflow_template_id' => $this->workflow_template_id,
            'pay_first' => $this->pay_first,
            'payment_method' => $this->payment_method,
            'address_id' => $this->address_id,
            'deadline' => $deadline,
            'images_to_remove' => $this->imagesToRemove,
        ];

        $uploadedFiles = $this->getUploadedFiles();

        try {
            if ($this->offer && $this->offer->exists) {
                $updated = $openOfferService->updateOpenOffer($this->offer, $data, $uploadedFiles);
                $this->dispatch('openOfferSaved', $updated->id);
                session()->flash('success', 'Open offer updated successfully!');

                return redirect()->route('creator.openoffers.index');
            } else {
                $created = $openOfferService->createOpenOffer(auth()->user(), $data, $uploadedFiles);
                $this->dispatch('openOfferSaved', $created->id);
                session()->flash('success', 'Open offer created successfully!');

                return redirect()->route('creator.openoffers.index');
            }
        } catch (\Throwable $e) {
            \Log::error('OpenOfferForm save failed: '.$e->getMessage(), ['exception' => $e]);
            $this->addError('save', 'Failed to save open offer: '.$e->getMessage());
        }
    }

    public function close(OpenOffer $openoffer)
    {
        $this->authorize('close', $openoffer);

        $this->openOfferService->closeOpenOffer($openoffer);

        return back()->with('success', 'Open Offer closed successfully!');
    }

    public function render()
    {
        return view('livewire.open-offer-form');
    }
}
