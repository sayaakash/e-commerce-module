<?php
abstract class AppException extends Exception {
    abstract public function getHttpCode(): int;

    public function getErrors(): array {
        return [];
    }
}
