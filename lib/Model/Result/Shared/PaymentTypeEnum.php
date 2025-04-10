<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result\Shared;

enum PaymentTypeEnum: string
{
    case CARD = 'CARD';
    case INVOICE = 'INVOICE';
    case A2A = 'A2a';
    case INSTALLMENT = 'INSTALLMENT';
    case WALLET = 'WALLET';
    case PREPAID_INVOICE = 'PREPAID_INVOICE';
}
