<?php

namespace App\Enum;

enum StatutFactureEnum: string
{
    case EN_ATTENTE = 'en_attente';
    case PAYEE      = 'payee';
    case PARTIELLE  = 'partielle';
}
