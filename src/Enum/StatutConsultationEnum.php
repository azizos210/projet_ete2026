<?php

namespace App\Enum;

enum StatutConsultationEnum: string
{
    case EN_COURS = 'en_cours';
    case TERMINEE = 'terminee';
    case ANNULEE  = 'annulee';
}
