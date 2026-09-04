<?php
namespace Jankx\Extensions\Service\PostTypes;

/**
 * Registers the "service" Custom Post Type.
 *
 * A service (dịch vụ) is a purchasable item with an associated cost (chi phí),
 * e.g. vé tham quan, dịch vụ đưa đón, thuê xe, hướng dẫn viên, lưu trú...
 *
 * @package Jankx\Extensions\Service
 */
class ServicePostType
{
    const POST_TYPE = 'service';

    public function register(): void
    {
        add_action('init', [$this, 'register_post_type']);
        add_filter('use_block_editor_for_post_type', [$this, 'disableBlockEditor'], 10, 2);
    }

    public function register_post_type(): void
    {
        if (post_type_exists(self::POST_TYPE)) {
            return;
        }

        $labels = [
            'name'               => __('Dịch vụ', 'jankx'),
            'singular_name'      => __('Dịch vụ', 'jankx'),
            'menu_name'          => __('Dịch vụ', 'jankx'),
            'add_new'            => __('Thêm dịch vụ', 'jankx'),
            'add_new_item'       => __('Thêm dịch vụ mới', 'jankx'),
            'edit_item'          => __('Sửa dịch vụ', 'jankx'),
            'new_item'           => __('Dịch vụ mới', 'jankx'),
            'view_item'          => __('Xem dịch vụ', 'jankx'),
            'view_items'         => __('Xem các dịch vụ', 'jankx'),
            'search_items'       => __('Tìm dịch vụ', 'jankx'),
            'not_found'          => __('Không tìm thấy dịch vụ nào', 'jankx'),
            'not_found_in_trash' => __('Không có dịch vụ nào trong thùng rác', 'jankx'),
            'all_items'          => __('Tất cả dịch vụ', 'jankx'),
            'archives'           => __('Lưu trữ dịch vụ', 'jankx'),
            'featured_image'     => __('Ảnh đại diện dịch vụ', 'jankx'),
        ];

        register_post_type(self::POST_TYPE, [
            'labels'        => $labels,
            'public'        => true,
            'show_in_rest'  => true,
            'menu_icon'     => 'dashicons-services',
            'menu_position' => 22,
            'supports'      => ['title', 'editor', 'excerpt', 'thumbnail', 'custom-fields'],
            'has_archive'   => 'dich-vu',
            'rewrite'       => ['slug' => 'dich-vu', 'with_front' => false],
            'show_in_menu'  => true,
        ]);

        add_post_type_support(self::POST_TYPE, 'block-templates');
    }

    public function disableBlockEditor(bool $useBlockEditor, string $postType): bool
    {
        if ($postType === self::POST_TYPE) {
            return false;
        }
        return $useBlockEditor;
    }
}
