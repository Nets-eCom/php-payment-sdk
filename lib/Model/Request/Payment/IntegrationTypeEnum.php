<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request\Payment;

enum IntegrationTypeEnum
{
    case EmbeddedCheckout;
    case HostedPaymentPage;
}
