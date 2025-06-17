<?php declare(strict_types=1);

namespace NexiCheckout\Model\Webhook\Data\CardDetails;

class ThreeDSecure
{
    public function __construct(
        private readonly string $acsUrl,
        private readonly string $authenticationEnrollmentStatus,
        private readonly string $authenticationStatus,
        private readonly string $eci
    ) {
    }

    public function getAcsUrl(): string
    {
        return $this->acsUrl;
    }

    public function getAuthenticationEnrollmentStatus(): string
    {
        return $this->authenticationEnrollmentStatus;
    }

    public function getAuthenticationStatus(): string
    {
        return $this->authenticationStatus;
    }

    public function getEci(): string
    {
        return $this->eci;
    }
}
