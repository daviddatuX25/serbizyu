@component('mail::message')
# New Message in Your Order

Hi {{ $recipient->name }},

**{{ $sender->name }}** sent you a message in Order **#{{ $order->id }}**.

@component('mail::panel')
**Message Preview:**

{{ \Illuminate\Support\Str::limit($message->content, 150, '...') }}
@endcomponent

@component('mail::button', ['url' => route('orders.show', $order), 'color' => 'primary'])
View Full Conversation
@endcomponent

**Order Details:**
- **Order ID:** #{{ $order->id }}
- **Service:** {{ $order->service->name ?? 'N/A' }}
- **Status:** {{ ucfirst($order->status) }}
- **Created:** {{ $order->created_at->format('F d, Y') }}

---

You can reply directly by clicking the button above or logging into your Serbizyu account.

**Keep your conversations within Serbizyu** to ensure both parties are protected.

Thanks,<br>
**{{ config('app.name') }} Team**

@slot('subcopy')
Don't want to receive these emails? You can [manage your notification preferences]({{ route('settings.notifications') }}) in your account.
@endslot
@endcomponent
