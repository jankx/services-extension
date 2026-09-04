<?php
/** @var string $price */
/** @var string $regularPrice */
/** @var string $salePrice */
/** @var string $manageStock */
/** @var string $stockQuantity */
if (!defined('ABSPATH')) {
    exit;
}
?>
<table class="form-table jankx-service-form-table">
    <tr>
        <th><label for="service_price"><?php esc_html_e('Chi phí dịch vụ (VNĐ)', 'jankx'); ?></label></th>
        <td>
            <input type="number" step="1000" min="0" id="service_price" name="service_price" class="regular-text" value="<?php echo esc_attr($price); ?>" />
            <p class="description"><?php esc_html_e('Chi phí bán cho dịch vụ này, dùng làm giá khi thêm vào giỏ hàng.', 'jankx'); ?></p>
        </td>
    </tr>
    <tr>
        <th><label for="service_regular_price"><?php esc_html_e('Chi phí gốc (VNĐ)', 'jankx'); ?></label></th>
        <td>
            <input type="number" step="1000" min="0" id="service_regular_price" name="service_regular_price" class="regular-text" value="<?php echo esc_attr($regularPrice); ?>" />
            <p class="description"><?php esc_html_e('Chi phí gốc, dùng để hiển thị % giảm giá.', 'jankx'); ?></p>
        </td>
    </tr>
    <tr>
        <th><label for="service_sale_price"><?php esc_html_e('Chi phí khuyến mãi (VNĐ)', 'jankx'); ?></label></th>
        <td>
            <input type="number" step="1000" min="0" id="service_sale_price" name="service_sale_price" class="regular-text" value="<?php echo esc_attr($salePrice); ?>" />
            <p class="description"><?php esc_html_e('Để trống nếu không có khuyến mãi.', 'jankx'); ?></p>
        </td>
    </tr>
    <tr>
        <th><label for="manage_stock"><?php esc_html_e('Quản lý số lượng', 'jankx'); ?></label></th>
        <td>
            <label>
                <input type="checkbox" name="manage_stock" value="1" <?php checked($manageStock, 1); ?> />
                <?php esc_html_e('Bật quản lý số lượng', 'jankx'); ?>
            </label>
        </td>
    </tr>
    <tr>
        <th><label for="stock_quantity"><?php esc_html_e('Số lượng khả dụng', 'jankx'); ?></label></th>
        <td>
            <input type="number" min="0" id="stock_quantity" name="stock_quantity" style="width:100px" value="<?php echo esc_attr($stockQuantity); ?>" />
        </td>
    </tr>
</table>
