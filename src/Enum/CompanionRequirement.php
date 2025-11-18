<?php
namespace App\Enum;

enum CompanionRequirement: string
{
    case NOT_REQUIRED = 'not_required';
    case OPTIONAL = 'optional';
    case REQUIRED = 'required';

    public function label(): string
    {
        return match($this) {
            self::NOT_REQUIRED => 'Nicht erforderlich',
            self::OPTIONAL     => 'Optional',
            self::REQUIRED     => 'Erforderlich',
        };
    }
}
