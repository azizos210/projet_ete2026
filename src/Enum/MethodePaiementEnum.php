<?php

namespace App\Enum;

enum MethodePaiementEnum: string
{
    case CARTE    = 'carte';
    case ESPECES  = 'especes';
    case ASSURANCE = 'assurance';
    case VIREMENT = 'virement';
}
