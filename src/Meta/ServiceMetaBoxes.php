<?php
namespace Jankx\Extensions\Service\Meta;

use Jankx\Extensions\Service\PostTypes\ServicePostType;

/**
 * Admin meta boxes for the "service" CPT: cost (chi phí) and stock.
 *
 * @package Jankx\Extensions\Service
 */
class ServiceMetaBoxes
{
    const NONCE_ACTION = 'jankx_service_meta';
    const NONCE_NAME   = 'jankx_service_meta_nonce';

    public function register(): void
    {
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post_' . ServicePostType::POST_TYPE, [$this, 'save']);
    }

    public function add_meta_boxes(): void
    {
        add_meta_box(
            'jankx_service_pricing',
            __('Chi phí & Tồn kho', 'jankx'),
            [$this, 'render_pricing_box'],
            ServicePostType::POST_TYPE,
            'normal',
            'high'
        );
    }

    protected function nonce_field(): void
    {
        wp_nonce_field(self::NONCE_ACTION, self::NONCE_NAME);
    }

    public function render_pricing_box(\WP_Post $post): void
    {
        $this->nonce_field();

        $price         = get_post_meta($post->ID, '_service_price', true);
        $regularPrice  = get_post_meta($post->ID, '_service_regular_price', true);
        $salePrice     = get_post_meta($post->ID, '_service_sale_price', true);
        $manageStock   = get_post_meta($post->ID, '_manage_stock', true);
        $stockQuantity = get_post_meta($post->ID, '_stock_quantity', true);

        include __DIR__ . '/views/service-pricing.php';
    }

    public function save(int $post_id): void
    {
        if (
            !isset($_POST[self::NONCE_NAME]) ||
            !wp_verify_nonce($_POST[self::NONCE_NAME], self::NONCE_ACTION)
        ) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        update_post_meta($post_id, '_service_price', (float) ($_POST['service_price'] ?? 0));
        update_post_meta($post_id, '_service_regular_price', (float) ($_POST['service_regular_price'] ?? 0));
        update_post_meta($post_id, '_service_sale_price', (float) ($_POST['service_sale_price'] ?? 0));
        update_post_meta($post_id, '_manage_stock', !empty($_POST['manage_stock']) ? 1 : 0);
        update_post_meta($post_id, '_stock_quantity', absint($_POST['stock_quantity'] ?? 0));
    }
}
