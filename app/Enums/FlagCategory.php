<?php

namespace App\Enums;

enum FlagCategory: string
{
    case Spam = 'spam';
    case Inappropriate = 'inappropriate';
    case Fraud = 'fraud';
    case MisleadingInfo = 'misleading_info';
    case CopyrightViolation = 'copyright_violation';
    case Other = 'other';
}
