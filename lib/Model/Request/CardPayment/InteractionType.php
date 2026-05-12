<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request\CardPayment;

enum InteractionType: int
{
    case VirtualCard = 0;
}
