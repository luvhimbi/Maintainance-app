<?php

namespace App\Enums;

enum Specialization: string
{
    case GENERAL = 'General';
    case PLUMBING = 'Plumbing';
    case ELECTRICAL = 'Electrical';
    case STRUCTURAL = 'Structural';
    case HVAC = 'HVAC';
    case FURNITURE = 'Furniture';
    case PC = 'PC';

    
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
    public static function fromIssueType(string $issueType): ?self
    {
        return match(strtolower($issueType)) {
            'electrical' => self::ELECTRICAL,
            'plumbing' => self::PLUMBING,
            'structural' => self::STRUCTURAL,
            'hvac' => self::HVAC,
            'furniture' => self::FURNITURE,
            'pc', 'it' => self::PC,
            default => self::GENERAL
        };
    }

    
    public function getRelatedIssueTypes(): array
    {
        return match($this) {
            self::ELECTRICAL => ['Electrical', 'Lighting', 'Power'],
            self::PLUMBING => ['Plumbing', 'Water', 'Drainage'],
            self::STRUCTURAL => ['Structural', 'Walls', 'Ceiling'],
            self::HVAC => ['HVAC', 'Ventilation', 'AC'],
            self::FURNITURE => ['Furniture', 'Chair', 'Table'],
            self::PC => ['PC', 'Computer', 'Network'],
            default => ['General', 'Other']
        };
    }
    
    /**
     * Get the priority level of the specialization.
     * 
     * @return int Priority level (1 = highest, 5 = lowest)
     */

    public function getPriorityLevel(): int
    {
        return match($this) {
            self::ELECTRICAL => 1,  // Highest priority
            self::PLUMBING => 2,
            self::STRUCTURAL => 2,
            self::HVAC => 3,
            self::FURNITURE => 4,
            self::PC => 3,
            default => 5
        };
    }
    

}

