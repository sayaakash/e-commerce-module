<?php
require_once __DIR__ . '/AppException.php';

class ValidationException extends AppException {
    private array $errors;

    public function __construct(string $message, array $errors = []) {
        parent::__construct($message);
        $this->errors = $errors;
    }

    public function getHttpCode(): int {
        return 400;
    }

    public function getErrors(): array {
        return $this->errors;
    }
}
