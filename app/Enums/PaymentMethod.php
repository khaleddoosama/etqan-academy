<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case WALLET = 'wallet';
    case INSTAPAY = 'instapay';
}
