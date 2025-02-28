<?php

namespace App\Enum;

enum ProductCategory: string
{
    case AUTOMOTIVE = 'Automotive';
    case BEAUTY = 'Beauty & Personal Care';
    case ELECTRONICS = 'Electronics';
    case FASHION = 'Fashion';
    case FITNESS = 'Fitness & Health';
    case FOOD = 'Food & Beverages';
    case HOME_DECOR = 'Home Decor';
    case PET_SUPPLIES = 'Pet Supplies';
    case SPORTS = 'Sports & Outdoors';
    case TOYS = 'Toys & Games';

    /**
     * @return string[]
     */
    public static function getValues(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }
}
