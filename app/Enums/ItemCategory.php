<?php

namespace App\Enums;

enum ItemCategory: string
{
    case Electronics = 'electronics';
    case IdDocument = 'id_document';
    case WalletBag = 'wallet_bag';
    case Keys = 'keys';
    case BooksStationery = 'books_stationery';
    case Accessories = 'accessories';
    case Other = 'other';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return __('enums.category.'.$this->value);
    }

    public function icon(): string
    {
        return match ($this) {
            self::Electronics => 'smartphone',
            self::IdDocument => 'id-card',
            self::WalletBag => 'wallet',
            self::Keys => 'key-round',
            self::BooksStationery => 'book-open',
            self::Accessories => 'tag',
            self::Other => 'package',
        };
    }

    public function isAutoSensitive(): bool
    {
        return $this === self::IdDocument;
    }

    public function fallbackPhotoPath(): string
    {
        return match ($this) {
            self::Electronics => 'images/demo/items/tk-0001-macbook-lost.webp',
            self::IdDocument => 'images/demo/items/tk-0014-ktm-found.webp',
            self::WalletBag => 'images/demo/items/tk-0002-wallet-lost.webp',
            self::Keys => 'images/demo/items/tk-0003-keys-lost.webp',
            self::BooksStationery => 'images/demo/items/tk-0018-textbook-found.webp',
            self::Accessories => 'images/demo/items/tk-0020-watch-found.webp',
            self::Other => 'images/demo/items/tk-0016-tumbler-found.webp',
        };
    }
}
