<?php

namespace App\Enum;

enum TypeNotificationEnum: string
{
    case RAPPEL_RDV       = 'rappel_rdv';
    case ALERTE_SIGNE_VITAL = 'alerte_signe_vital';
    case NOUVEAU_MESSAGE  = 'nouveau_message';
    case SYSTEME          = 'systeme';
    case RESULTAT         = 'resultat';
}
