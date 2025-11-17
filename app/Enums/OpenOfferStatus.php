<?php

namespace App\Enums;

enum OpenOfferStatus: string
{
    case PENDING = 'pending';
    case OPEN = 'open';
    case CLOSED = 'closed';
    case FULFILLED = 'fulfilled';
    case CANCELLED = 'cancelled';
}
