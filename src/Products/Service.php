<?php
namespace Jankx\Extensions\Service\Products;

use Jankx\Extensions\Ecommerce\Abstracts\AbstractProduct;
use Jankx\Extensions\Service\PostTypes\ServicePostType;

class Service extends AbstractProduct
{
    const PRICE_META_KEY            = '_jankx_price';
    const REGULAR_PRICE_META_KEY    = '_jankx_regular_price';
    const SALE_PRICE_META_KEY       = '_jankx_sale_price';
    const LEGACY_PRICE_META_KEYS    = ['_service_price'];
    const LEGACY_REGULAR_PRICE_META_KEYS = ['_service_regular_price'];
    const LEGACY_SALE_PRICE_META_KEYS    = ['_service_sale_price'];

    public function isInStock(): bool
    {
        if (!$this->post) {
            return false;
        }

        if (!(bool) get_post_meta($this->id, '_manage_stock', true)) {
            return true;
        }

        return (int) get_post_meta($this->id, '_stock_quantity', true) > 0;
    }

    public function getProductType(): string
    {
        return ServicePostType::POST_TYPE;
    }
}
