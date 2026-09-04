<?php
namespace Jankx\Extensions\Service\Taxonomies;

use Jankx\Extensions\Service\PostTypes\ServicePostType;

/**
 * Registers the "service_category" taxonomy (Nhóm dịch vụ).
 *
 * Groups services into categories such as "Vé tham quan", "Di chuyển",
 * "Lưu trú", "Hướng dẫn viên"...
 *
 * @package Jankx\Extensions\Service
 */
class ServiceCategoryTaxonomy
{
    const TAXONOMY = 'service_category';

    public function register(): void
    {
        add_action('init', [$this, 'register_taxonomy']);
        // Reuse the travel "destination" taxonomy as well, so a service can be
        // tagged with a place (Hà Nội, Ninh Bình...) when the travel extension
        // is active.
        add_action('registered_post_type', [$this, 'register_for_destination']);
    }

    public function register_taxonomy(): void
    {
        register_taxonomy(self::TAXONOMY, [ServicePostType::POST_TYPE], [
            'labels' => [
                'name'          => __('Nhóm dịch vụ', 'jankx'),
                'singular_name' => __('Nhóm dịch vụ', 'jankx'),
                'search_items'  => __('Tìm nhóm dịch vụ', 'jankx'),
                'all_items'     => __('Tất cả nhóm dịch vụ', 'jankx'),
                'edit_item'     => __('Sửa nhóm dịch vụ', 'jankx'),
                'update_item'   => __('Cập nhật nhóm dịch vụ', 'jankx'),
                'add_new_item'  => __('Thêm nhóm dịch vụ mới', 'jankx'),
                'new_item_name' => __('Tên nhóm dịch vụ mới', 'jankx'),
                'menu_name'     => __('Nhóm dịch vụ', 'jankx'),
            ],
            'hierarchical'      => true,
            'public'            => true,
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'rewrite'           => ['slug' => 'nhom-dich-vu'],
        ]);
    }

    /**
     * Attach the travel "destination" taxonomy to the service post type when
     * the destination taxonomy is available (registered by the travel extension).
     */
    public function register_for_destination(string $postType): void
    {
        if ($postType !== ServicePostType::POST_TYPE) {
            return;
        }
        if (taxonomy_exists('destination') && post_type_exists(ServicePostType::POST_TYPE)) {
            register_taxonomy_for_object_type('destination', ServicePostType::POST_TYPE);
        }
    }
}
