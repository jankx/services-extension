<?php
namespace Jankx\Extensions\Service;

use Jankx\Extensions\AbstractExtension;
use Jankx\Extensions\Ecommerce\EcommerceExtension;
use Jankx\Extensions\Service\Admin\ThumbnailColumn;
use Jankx\Extensions\Service\Admin\ServiceListColumns;
use Jankx\Extensions\Service\Meta\ServiceMetaBoxes;
use Jankx\Extensions\Service\PostTypes\ServicePostType;
use Jankx\Extensions\Service\Products\Service;
use Jankx\Extensions\Service\Taxonomies\ServiceCategoryTaxonomy;

/**
 * Service Extension
 *
 * Registers the "service" post type (dịch vụ kèm chi phí) into the shared
 * e-commerce flow (cart, checkout, payment, order) provided by the
 * base-ecommerce extension, so an individual service can be added to the cart.
 *
 * @package Jankx\Extensions\Service
 */
class ServiceExtension extends AbstractExtension
{
    protected static $instance;

    public function __construct()
    {
        $this->register_autoloader();
        parent::__construct();
    }

    protected function register_autoloader()
    {
        spl_autoload_register(function ($class) {
            $prefix = 'Jankx\\Extensions\\Service\\';
            $base_dir = __DIR__ . '/src/';

            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                return;
            }

            $relative_class = substr($class, $len);
            $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

            if (file_exists($file)) {
                require $file;
            }
        });
    }

    public function init(): void
    {
        self::$instance = $this;
    }

    public static function get_instance(): ?self
    {
        return self::$instance;
    }

    public function register_hooks(): void
    {
        // Register the service post type on every request.
        (new ServicePostType())->register();

        // Register service category taxonomy (+ destination when available).
        (new ServiceCategoryTaxonomy())->register();

        // Admin cost/stock meta boxes and list columns.
        if (is_admin()) {
            (new ServiceMetaBoxes())->register();
            (new ThumbnailColumn())->register();
            (new ServiceListColumns())->register();
        }

        // Register "service" into the shared e-commerce flow (cart, checkout,
        // payment, order) only when base-ecommerce is loaded. Registering
        // through the `jankx/ecommerce/register_product_types` hook keeps this
        // extension fail-soft if base-ecommerce is ever disabled.
        if (class_exists(EcommerceExtension::class)) {
            add_action('jankx/ecommerce/register_product_types', function ($registry) {
                $registry->register(ServicePostType::POST_TYPE, Service::class);
            });
        }

        // Register coupon scope support
        add_filter('jankx/coupon/product_scope/post_types', function ($types) {
            $types[] = [
                'label'     => __('Dịch vụ', 'jankx'),
                'rest_base' => 'service',
                'post_type' => ServicePostType::POST_TYPE,
            ];
            return $types;
        });

        add_filter('jankx/coupon/product_type_scope/post_types', function ($options) {
            $options[] = ['value' => ServicePostType::POST_TYPE, 'label' => __('Dịch vụ', 'jankx')];
            return $options;
        });

        // Register star rating data source for service posts.
        add_action('init', [$this, 'register_star_rating_provider'], 20);
    }

    public function register_star_rating_provider(): void
    {
        if (!class_exists('\Jankx\Gutenberg\StarRating\StarRatingRegistry')) {
            return;
        }

        \Jankx\Gutenberg\StarRating\StarRatingRegistry::register(
            new \Jankx\Gutenberg\StarRating\Providers\ConfigurableRatingProvider([
                'id'             => 'service_rating',
                'label'          => __('Service Rating', 'jankx'),
                'postTypes'      => ['service'],
                'ratingMetaKey'  => 'jankx_rating_average',
                'countMetaKey'   => 'jankx_rating_count',
                'editorControls' => [
                    [
                        'type'      => 'toggle',
                        'attribute' => 'showCount',
                        'label'     => __('Show review count', 'jankx'),
                    ],
                ],
            ])
        );
    }
}
