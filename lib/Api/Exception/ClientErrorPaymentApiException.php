<?php declare(strict_types=1);

namespace NexiCheckout\Api\Exception;

class ClientErrorPaymentApiException extends PaymentApiException
{
    /**
     * @var array<string, string[]>
     */
    private readonly array $errors;

    public function __construct(
        string $message,
        string $errors,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $errorsBody = json_decode($errors, true, 512, \JSON_THROW_ON_ERROR);
        if (isset($errorsBody['errors']) && is_array($errorsBody['errors'])) {
            $this->errors = $errorsBody['errors'];
            return;
        }

        if (is_array($errorsBody)) {
            $this->errors = $errorsBody;
            return;
        }

        $this->errors = [
            'error' => ['Unknown api error'],
        ];

    }

    /**
     * @return array<string, string[]>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
