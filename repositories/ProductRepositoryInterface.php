<?php
interface ProductRepositoryInterface {
    public function findAll(int $limit = 10, int $offset = 0): array;
    public function findById(int $id): ?array;
    public function create(array $data): int;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function count(): int;
}
