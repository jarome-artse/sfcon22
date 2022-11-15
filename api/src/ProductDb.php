<?php

declare(strict_types=1);

namespace App;

use App\ApiResource\Product;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

class ProductDb
{
    public function __construct(private readonly string $dbPath)
    {

    }

    /**
     * @return Product[]
     */
    public function getCollection(): iterable
    {
        return $this->getData();
    }

    /**
     * @param \Symfony\Component\Uid\Uuid $id
     *
     * @return \App\ApiResource\Product|null
     */
    public function get(Uuid $id): Product|null
    {
        return $this->getData()[(string) $id] ?? null;
    }

    public function save(Product $product): void
    {
        $data = $this->getData();
        $data[(string) $product->id] = $product;

        $this->saveData($data);
    }

    public function delete(Product $product): void
    {
        $data = $this->getData();
        unset($data[(string) $product->id]);

        $this->saveData($data);
    }

    private function getData(): iterable
    {
        if (!file_exists($this->dbPath)) {
            return [];
        }

        return unserialize(file_get_contents($this->dbPath), ['allowed_classes' => [Product::class, UuidV4::class]]);
    }

    private function saveData(array $data): void
    {
        file_put_contents($this->dbPath, serialize($data));
    }
}
