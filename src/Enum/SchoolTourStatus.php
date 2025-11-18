<?php

namespace App\Enum;

enum SchoolTourStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case PENDING_WIP = 'pending_wip';
    case PENDING_APPROVAL = 'pending_approval';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => '✅ Aktiv',
            self::INACTIVE => '❌ Inaktiv',
            self::PENDING_WIP => '📝 In Bearbeitung',
            self::PENDING_APPROVAL => '⏳ Warten auf Freigabe',
        };
    }
}
