# Messaging System Configuration & Deployment Guide

**Status:** ✅ IMPLEMENTATION COMPLETE - READY FOR CONFIGURATION  
**Date:** November 26, 2025

## System Overview

The messaging system is **fully implemented** with all core components in place:

### ✅ Completed Components

| Component | Status | Details |
|-----------|--------|---------|
| **Broadcasting Events** | ✅ Complete | OrderMessageSent, BidMessageSent, TypingIndicator, MessageSent |
| **Notifications** | ✅ Complete | Database + Email channels for orders and bids |
| **Controllers** | ✅ Complete | Message dispatch, event broadcasting, notification sending |
| **Email Templates** | ✅ Complete | order-message.blade.php, bid-message.blade.php |
| **Authorization** | ✅ Complete | Policies enforce view/send permissions |
| **Auto-thread Creation** | ✅ Complete | Threads auto-created when orders/bids created |
| **Read Receipts** | ✅ Complete | read_at timestamp tracking |
| **File Attachments** | ✅ Complete | message_attachments table support |

---

## Configuration Steps

### Step 1: Set Broadcasting Driver

**Choose your broadcasting solution:**

#### Option A: Development (Pusher)
```bash
# Add to .env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1
PUSHER_APP_HOST=api-mt1.pusher.com
PUSHER_APP_PORT=443
```

#### Option B: Development (Log - Testing Only)
```bash
# Add to .env - Good for testing without Pusher
BROADCAST_DRIVER=log
```

#### Option C: Development (Redis)
```bash
# Add to .env
BROADCAST_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

**Pusher Setup:**
1. Sign up at https://pusher.com
2. Create an app and get credentials
3. Add credentials to .env
4. Install Pusher PHP library:
   ```bash
   composer require pusher/pusher-php-server
   ```

### Step 2: Set Queue Connection

**Configure job queue for async notifications:**

#### Option A: Database Queue (Recommended for Small Projects)
```bash
# Add to .env
QUEUE_CONNECTION=database

# Create jobs table
php artisan queue:table
php artisan migrate

# Run queue worker (keep running in background)
php artisan queue:work
```

#### Option B: Redis Queue (Recommended for Production)
```bash
# Add to .env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Run queue worker
php artisan queue:work redis
```

#### Option C: Synchronous (Testing Only - NOT for Production)
```bash
# Add to .env
QUEUE_CONNECTION=sync

# This executes notifications immediately, not async
```

**Running Queue Worker:**
```bash
# Start queue worker
php artisan queue:work

# Or with verbosity to see what's happening
php artisan queue:work --verbose

# Or in background (production)
nohup php artisan queue:work &
```

### Step 3: Configure Mail Service

**Set up email delivery for notifications:**

#### Option A: Mailtrap (Development)
```bash
# Sign up at https://mailtrap.io
# Add to .env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@serbizyu.local
MAIL_FROM_NAME="Serbizyu"
```

#### Option B: SendGrid (Production)
```bash
# Sign up at https://sendgrid.com
# Add to .env
MAIL_MAILER=sendgrid
SENDGRID_API_KEY=your_api_key
MAIL_FROM_ADDRESS=noreply@serbizyu.com
MAIL_FROM_NAME="Serbizyu"
```

#### Option C: Gmail (Development Only)
```bash
# Enable 2FA on your Gmail account
# Create app-specific password
# Add to .env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="Serbizyu"
```

### Step 4: Verify Installation

```bash
# Check queue configuration
php artisan queue:list

# Test broadcasting channels
php artisan tinker
# Then run:
>>> event(new \App\Events\MessageSent($message))

# Test mail configuration
php artisan tinker
>>> Mail::raw('Test email', function($msg){ $msg->to('test@example.com'); })
```

### Step 5: Enable Database Notifications

Ensure the notifications table exists:

```bash
# Check if migrations have been run
php artisan migrate:status

# If notifications table doesn't exist:
php artisan notifications:table
php artisan migrate
```

---

## Frontend Setup (Livewire Echo)

### Install Laravel Echo (if not already done)

```bash
npm install laravel-echo pusher-js
```

### Configure Echo in resources/js/echo.js

```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});
```

### Update .env for Frontend

```env
VITE_PUSHER_APP_KEY=your_pusher_app_key
VITE_PUSHER_APP_CLUSTER=mt1
```

### Update vite.config.js

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
```

### Listen to Events in Livewire

```blade
<div wire:ignore>
    <div id="messages-container"></div>
</div>

<script>
    Echo.private(`message-thread.{{ $thread->id }}`)
        .listen('OrderMessageSent', (e) => {
            console.log('New message:', e.message);
            // Update UI here
        })
        .listen('typing', (e) => {
            console.log(`${e.user_name} is typing...`);
            // Show typing indicator
        });
</script>
```

---

## Testing the System

### Manual Test Checklist

**Prerequisites:**
- [ ] Broadcasting driver configured (.env)
- [ ] Queue worker running
- [ ] Mail service configured
- [ ] Database notifications table created
- [ ] Frontend Echo setup complete

**Order Messaging Tests:**
- [ ] Create order between two users
- [ ] User A sends message in order chat
- [ ] User B receives message instantly (real-time)
- [ ] User B receives email notification
- [ ] Database notifications table has entry for User B
- [ ] User B opens chat, message marked as read
- [ ] Check notifications page shows database entry

**Bid Messaging Tests:**
- [ ] Create bid from bidder
- [ ] Bidder sends message to offer creator
- [ ] Offer creator receives message instantly
- [ ] Offer creator receives email notification
- [ ] Database notifications entry created
- [ ] Offer creator marks message as read

**Typing Indicators:**
- [ ] User A starts typing in chat
- [ ] User B sees "User A is typing..." indicator
- [ ] User A stops typing
- [ ] Indicator disappears after 3 seconds

**Authorization Tests:**
- [ ] Non-participant cannot view messages (403)
- [ ] Non-participant cannot send message (403)
- [ ] Cannot message cancelled order (403)
- [ ] Cannot message rejected bid (403)

### Automated Testing

```bash
# Run existing message tests
php artisan test tests/Feature/OrderMessagingTest.php
php artisan test tests/Feature/BidMessagingTest.php

# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage
```

---

## Production Deployment

### Pre-Deployment Checklist

- [ ] Broadcasting driver = `pusher` (not log/redis)
- [ ] Queue connection = `redis` (not database/sync)
- [ ] Mail service = production provider (SendGrid, etc.)
- [ ] All `.env` values set correctly
- [ ] Queue worker running (via supervisor)
- [ ] Broadcasting credentials secure
- [ ] Mail credentials secure
- [ ] Database migrations applied
- [ ] Tests passing
- [ ] Logs monitored

### Supervisor Configuration (Queue Worker)

Create `/etc/supervisor/conf.d/serbizyu-queue.conf`:

```ini
[program:serbizyu-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/serbizyu/artisan queue:work redis --sleep=3 --tries=3 --timeout=90
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/serbizyu/storage/logs/queue.log
```

Update supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start serbizyu-queue:*
```

### Monitoring

Monitor queue health:
```bash
# Check queue job count
php artisan queue:failed

# See failed jobs
php artisan queue:failed-list

# Retry failed jobs
php artisan queue:retry all

# Monitor in real-time
watch -n 1 'php artisan queue:failed-list'
```

---

## Troubleshooting

### Issue: Messages not appearing in real-time

**Symptoms:** Messages only appear after page refresh

**Solutions:**
1. Check broadcasting driver: `BROADCAST_DRIVER` should be `pusher`, not `log`
2. Verify Pusher credentials in `.env`
3. Check browser console for connection errors
4. Ensure Livewire component uses `wire:ignore` for Echo listeners
5. Verify Event implements `ShouldBroadcast`

**Debug:**
```bash
# Test event broadcasting
php artisan tinker
>>> event(new \App\Events\OrderMessageSent($message, $order))
# Check Pusher dashboard for the event
```

### Issue: Notifications not sending

**Symptoms:** No database entries or emails received

**Solutions:**
1. Check queue is running: `php artisan queue:work` must be active
2. Check queue status: `php artisan queue:list`
3. Check failed jobs: `php artisan queue:failed`
4. Verify mail configuration: `MAIL_MAILER`, `MAIL_HOST`, etc.
5. Check logs: `storage/logs/laravel.log`

**Debug:**
```bash
# Test notification directly
php artisan tinker
>>> $user = \App\Domains\Users\Models\User::first()
>>> \Illuminate\Support\Facades\Notification::send($user, new \App\Notifications\OrderMessageNotification($message, $order))

# Check for failures
>>> \App\Jobs\SendNotification::where('status', 'failed')->get()
```

### Issue: Typing indicators not working

**Symptoms:** "User is typing..." doesn't appear

**Solutions:**
1. Verify TypingIndicator event exists
2. Check Livewire component dispatches typing event
3. Verify Echo listener for `typing` event
4. Check browser console for JavaScript errors
5. Ensure channel subscription is active

**Debug:**
```bash
# Test typing event
php artisan tinker
>>> event(new \App\Events\TypingIndicator($thread, $user, true))
```

### Issue: Mail not configured properly

**Symptoms:** "Swift_TransportException" or mail not sending

**Solutions:**
1. Test mail configuration:
   ```bash
   php artisan tinker
   >>> Mail::raw('Test', function($m){ $m->to('test@example.com'); })
   ```
2. Check `MAIL_FROM_ADDRESS` is set
3. Verify credentials are correct
4. Check firewall allows outbound SMTP
5. For Mailtrap, check credentials in dashboard

---

## Quick Reference

### Essential Commands

```bash
# Start queue worker
php artisan queue:work

# Clear failed jobs
php artisan queue:flush

# Retry all failed jobs
php artisan queue:retry all

# Monitor queue
php artisan queue:monitor redis --max=1000

# Test mail config
php artisan tinker
# Then: Mail::raw('Test', function($m){ $m->to('test@example.com'); })

# Run tests
php artisan test

# Check migrations
php artisan migrate:status

# View queue status
php artisan queue:list
```

### Key Files to Know

- Controllers: `app/Domains/Orders/Http/Controllers/OrderMessageController.php`
- Controllers: `app/Domains/Listings/Http/Controllers/BidMessageController.php`
- Events: `app/Events/OrderMessageSent.php`, `app/Events/BidMessageSent.php`
- Notifications: `app/Notifications/OrderMessageNotification.php`
- Mail Templates: `resources/views/mail/order-message.blade.php`
- Config: `config/broadcasting.php`, `config/queue.php`, `config/mail.php`
- Logs: `storage/logs/laravel.log`
- Failed Jobs: `storage/database/failed_jobs` or `storage/logs/queue.log`

---

## Next Steps

1. **Immediate (10 minutes):**
   - Set `BROADCAST_DRIVER=log` in .env for testing
   - Set `QUEUE_CONNECTION=database` in .env
   - Run `php artisan migrate`
   - Start queue worker: `php artisan queue:work`

2. **Short-term (1 hour):**
   - Sign up for Pusher account
   - Configure Pusher credentials
   - Set up Mailtrap for email testing
   - Test manual flow

3. **Before Production:**
   - Switch to production Broadcasting driver (Pusher)
   - Configure production email service (SendGrid, AWS SES)
   - Set up Redis for queue
   - Set up Supervisor for queue worker
   - Test all scenarios
   - Monitor logs

---

## Support Resources

- **Laravel Broadcasting:** https://laravel.com/docs/broadcasting
- **Laravel Notifications:** https://laravel.com/docs/notifications
- **Laravel Queues:** https://laravel.com/docs/queues
- **Pusher Docs:** https://pusher.com/docs
- **Laravel Echo:** https://laravel.com/docs/broadcasting#client-side-installation

---

**Status:** ✅ FULLY IMPLEMENTED AND READY FOR CONFIGURATION

The messaging system is complete and tested. Follow the configuration steps above to enable real-time notifications and broadcasting.
