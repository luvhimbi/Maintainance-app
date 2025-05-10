<?php

namespace App\Enums;

enum Specialization: string
{
    case GENERAL = 'General';
    case PLUMBING = 'Plumbing';
    case ELECTRICAL = 'Electrical';
    case STRUCTURAL = 'Structural';

    
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}