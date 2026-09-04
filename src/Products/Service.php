<?php
namespace Jankx\Extensions\Service\Products;

use Jankx\Extensions\Ecommerce\Abstracts\AbstractProduct;
use Jankx\Extensions\Service\PostTypes\ServicePostType;

/**
 * Concrete product for the "service" post type.
 *
 * Costs (chi phí) are read from post meta:
 *   _service_price         — selling price (chi phí dịch vụ)
 *   _service_regular_price — optional regular (compare-at) price
 *   _service_sale_price    — optional sale price
 *   _manage_stock          — whether stock is managed
 *   _stock_quantity        — available stock
 *
 * @package Jankx\Extensions\Service
 */
class Service extends AbstractProduct
{
    public function getPrice(): float
    {
        $sale  = $this->getSalePrice();
        $price = (float) get_post_meta($this->id, '_service_price', true);

        return $sale > 0 ? $sale : $price;
    }

    public function getRegularPrice(): float
    {
        return (float) get_post_meta($this->id, '_service_regular_price', true);
    }

    public function getSalePrice(): float
    {
        return (float) get_post_meta($this->id, '_service_sale_price', true);
    }

    public function isPurchasable(): bool
    {
        return $this->post
            && $this->post->post_status === 'publish'
            && $this->getPrice() > 0
            && $this->isInStock();
    }

    public function isInStock(): bool
    {
        if (!$this->post) {
            return false;
        }

        if (!(bool) get_post_meta($this->id, '_manage_stock', true)) {
            return true;
        }

        $stock = (int) get_post_meta($this->id, '_stock_quantity', true);

        return $stock > 0;
    }

    public function getProductType(): string
    {
        return ServicePostType::POST_TYPE;
    }
}
