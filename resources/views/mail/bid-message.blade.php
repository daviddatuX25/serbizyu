@component('mail::message')
# New Message in Your Bid

Hi {{ $recipient->name }},

**{{ $sender->name }}** sent you a message in Bid **#{{ $bid->id }}** for "{{ $bid->openOffer->service->name }}".

@component('mail::panel')
**Message Preview:**

{{ \Illuminate\Support\Str::limit($message->content, 150, '...') }}
@endcomponent

@component('mail::button', ['url' => route('bids.show', $bid), 'color' => 'primary'])
View Full Conversation
@endcomponent

**Bid Details:**
- **Bid ID:** #{{ $bid->id }}
- **Service:** {{ $bid->openOffer->service->name }}
- **Status:** {{ ucfirst($bid->status->value) }}
- **Bid Amount:** {{ config('app.currency', '$') }}{{ number_format($bid->proposed_price, 2) }}
- **Created:** {{ $bid->created_at->format('F d, Y') }}

---

You can reply directly by clicking the button above or logging into your Serbizyu account.

**Keep your conversations within Serbizyu** to ensure both parties are protected.

Thanks,<br>
**{{ config('app.name') }} Team**

@slot('subcopy')
Don't want to receive these emails? You can [manage your notification preferences]({{ route('settings.notifications') }}) in your account.
@endslot
@endcomponent
