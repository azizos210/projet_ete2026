<?php

namespace App\Enum;

enum TypeDocumentEnum: string
{
    case IMAGE          = 'image';
    case PDF            = 'pdf';
    case RESULTAT_LABO  = 'resultat_labo';
    case ORDONNANCE     = 'ordonnance';
    case COMPTE_RENDU   = 'compte_rendu';
}
