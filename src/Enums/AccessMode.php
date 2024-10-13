<?php

/**
 * AccessMode.php
 * @Auteur: Christophe Dufour
 */

namespace Blackfox\Json\Enums;

enum AccessMode: string
{
    case Reading = 'r';
    case Writing = 'w';
}
