<?php
namespace Jankx\Extensions\Service\Admin;

use Jankx\Extensions\Ecommerce\Currency\CurrencyManager;
use Jankx\Extensions\Service\Taxonomies\ServiceCategoryTaxonomy;

/**
 * Adds cost (chi phí) and category columns to the "service" list screen.
 *
 * @package Jankx\Extensions\Service
 */
class ServiceListColumns
{
    public function register(): void
    {
        add_filter('manage_service_posts_columns', [$this, 'addColumns']);
        add_action('manage_service_posts_custom_column', [$this, 'renderColumn'], 10, 2);
        add_filter('manage_service_posts_sortable_columns', [$this, 'sortableColumns']);
        add_action('admin_head', [$this, 'addStyles']);
    }

    public function addStyles(): void
    {
        global $pagenow, $post_type;
        if ($pagenow !== 'edit.php' || $post_type !== 'service') {
            return;
        }
        echo '<style>'
            . '.wp-list-table .column-price { width: 120px; }'
            . '.wp-list-table .column-service_category { width: 150px; }'
            . '.jankx-price-regular { text-decoration: line-through; color: #999; font-size: 0.85em; }'
            . '.jankx-price-sale { color: #d63638; font-weight: 600; }'
            . '</style>';
    }

    public function addColumns(array $columns): array
    {
        $newColumns = [];

        foreach ($columns as $key => $value) {
            if ($key === 'title') {
                $newColumns['title']            = $value;
                $newColumns['price']            = __('Chi phí', 'jankx');
                $newColumns['service_category'] = __('Nhóm dịch vụ', 'jankx');
                continue;
            }
            $newColumns[$key] = $value;
        }

        return $newColumns;
    }

    public function renderColumn(string $column, int $postId): void
    {
        switch ($column) {
            case 'price':
                $this->renderPriceColumn($postId);
                break;
            case 'service_category':
                $this->renderCategoryColumn($postId);
                break;
        }
    }

    protected function renderPriceColumn(int $postId): void
    {
        $price        = (float) get_post_meta($postId, '_service_price', true);
        $regularPrice = (float) get_post_meta($postId, '_service_regular_price', true);
        $salePrice    = (float) get_post_meta($postId, '_service_sale_price', true);

        if ($price <= 0 && $regularPrice <= 0 && $salePrice <= 0) {
            echo '<span style="color:#ccc;">—</span>';
            return;
        }

        if ($salePrice > 0 && $salePrice < $price) {
            echo '<span class="jankx-price-sale">' . esc_html(CurrencyManager::formatPrice($salePrice)) . '</span>';
            echo '<br><span class="jankx-price-regular">' . esc_html(CurrencyManager::formatPrice($price)) . '</span>';
        } else {
            echo '<span>' . esc_html(CurrencyManager::formatPrice($price)) . '</span>';
        }
    }

    protected function renderCategoryColumn(int $postId): void
    {
        $terms = get_the_terms($postId, ServiceCategoryTaxonomy::TAXONOMY);

        if (empty($terms) || is_wp_error($terms)) {
            echo '<span style="color:#ccc;">—</span>';
            return;
        }

        $links = [];
        foreach ($terms as $term) {
            $links[] = '<a href="' . esc_url(get_edit_term_link($term->term_id, ServiceCategoryTaxonomy::TAXONOMY)) . '">'
                . esc_html($term->name) . '</a>';
        }

        echo implode(', ', $links);
    }

    public function sortableColumns(array $columns): array
    {
        $columns['price'] = 'price';
        return $columns;
    }
}
