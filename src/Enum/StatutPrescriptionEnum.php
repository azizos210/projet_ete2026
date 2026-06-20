<?php

namespace App\Enum;

enum StatutPrescriptionEnum: string
{
    case ACTIVE             = 'active';
    case TRANSMISE_PHARMACIE = 'transmise_pharmacie';
    case EXPIREE            = 'expiree';
}
