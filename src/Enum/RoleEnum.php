<?php

namespace App\Enum;

enum RoleEnum: string
{
    case ADMIN             = 'ROLE_ADMIN';
    case DIRECTEUR_MEDICAL = 'ROLE_DIRECTEUR_MEDICAL';
    case MEDECIN           = 'ROLE_MEDECIN';
    case INFIRMIER         = 'ROLE_INFIRMIER';
    case SECRETAIRE        = 'ROLE_SECRETAIRE';
    case PATIENT           = 'ROLE_PATIENT';
}
