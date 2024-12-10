<?php

namespace App\Enums;

enum PaymentType: string
{
    case CASH = 'cash';
    case INSTALLMENT = 'installment';
}
