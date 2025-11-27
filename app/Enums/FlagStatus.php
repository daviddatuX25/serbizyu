<?php

namespace App\Enums;

enum FlagStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Resolved = 'resolved';
}
