<?php
/**
 * Plugin Name: Book Previewer
 * Plugin URI: #
 * Description: The Book Previewer plugin allows users to preview books on your book store website. This plugin enhances the user experience by enabling book previews and improving the visual presentation of books (products).
 * Version: 0.0.1
 * Author: Sirajum Mahdi
 * Author URI: https://sirajummahdi.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: book-previewer
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class BookPreviewer {

	private $version;

	public function __construct() {
		$this->version = $this->get_plugin_version();
		add_action('init', array($this, 'init'));
	}

	public function init() {
		if ($this->is_woocommerce_active()) {
			add_action('wp_enqueue_scripts', array($this, 'bp_enqueue_scripts'));
			add_filter('body_class', array($this, 'bp_add_gallery_body_class'));
			add_action('wp_footer', array($this, 'bp_add_gallery_popup'));
			add_action('woocommerce_after_add_to_cart_button', array($this, 'bp_add_open_popup_button'));
			add_action('wp_footer', array($this, 'bp_add_popup_open_function'));
			add_filter('woocommerce_get_settings_products', array($this, 'bp_add_featured_image_class_settings'));
		} else {
			add_action('admin_notices', array($this, 'bp_woocommerce_missing_notice'));
		}
	}

	public function is_woocommerce_active() {
		return class_exists('WooCommerce');
	}

	public function bp_enqueue_scripts() {
		wp_enqueue_script('book-previewer-script', plugin_dir_url(__FILE__) . 'script.js', array('jquery'), $this->version, true);
		wp_enqueue_style('book-previewer-style', plugin_dir_url(__FILE__) . 'style.css', array(), $this->version);
	}

	public function bp_add_gallery_body_class($classes) {
		if (is_product()) {
			$product = wc_get_product();
			$product_id = $product ? $product->get_id() : 0;
			$gallery_images = get_post_meta($product_id, '_product_image_gallery', true);

			if (!empty($gallery_images)) {
				$classes[] = 'bp-has-gallery';
			} else {
				$classes[] = 'bp-no-gallery';
			}
		}
		return $classes;
	}

	public function bp_add_gallery_popup() {
		if (is_product()) {
			$product = wc_get_product();
			$product_id = $product ? $product->get_id() : 0;
			$gallery_images = get_post_meta($product_id, '_product_image_gallery', true);

			if (!empty($gallery_images)) {
				$gallery_image_ids = explode(',', $gallery_images);

				echo '<div class="bp-popup">';
				echo '<div class="bp-popup-content">';
				echo '<span class="bp-close-popup">&times;</span>';
				echo '<ul id="bp-gallery-list">';

				foreach ($gallery_image_ids as $image_id) {
					$image_url = wp_get_attachment_url($image_id);
					echo '<li><img src="' . esc_url($image_url) . '" alt="' . esc_attr__('Gallery Image', 'book-previewer') . '"></li>';
				}

				echo '</ul>';
				echo '</div>';
				echo '</div>';
			}
		}
	}

	public function bp_add_open_popup_button() {
		if (is_product()) {
			echo '<div>';
			echo '<button type="button" class="bp-open-popup-button">' . esc_html__('Read Preview', 'book-previewer') . '</button>';
			echo '</div>';
		}
	}

	public function bp_add_popup_open_function() {
		if (is_product()) {
			global $product;
			$product_id = $product ? $product->get_id() : 0;
			$featured_image_class = get_option('book-previewer-featured-image-class', 'book-cover');
			echo '<script>';
			echo 'jQuery(document).ready(function($) {';
			echo '$(".' . $featured_image_class . ' img").on("click", showPopup);'; // Updated line
			echo '});';
			echo '</script>';
		}
	}

	public function bp_add_featured_image_class_settings($settings) {
		$settings[] = array(
			'title' => __('Book Previewer', 'book-previewer'),
			'type' => 'title',
			'id' => 'book-previewer-options',
		);
		$settings[] = array(
			'title' => __('Featured Image Class', 'book-previewer'),
			'desc' => __('Enter the class name of the featured image container', 'book-previewer'),
			'id' => 'book-previewer-featured-image-class',
			'type' => 'text',
			'default' => 'book-cover',
		);
		$settings[] = array(
			'type' => 'sectionend',
			'id' => 'book-previewer-options',
		);
		return $settings;
	}

	public function bp_woocommerce_missing_notice() {
		?>
        <div class="error">
            <p><?php esc_html_e('Book Previewer plugin requires WooCommerce to be installed and activated.', 'book-previewer');?></p>
        </div>
        <?php
}

	private function get_plugin_version() {
		$plugin_data = get_file_data(__FILE__, array('Version' => 'Version'));
		return $plugin_data['Version'];
	}
}

new BookPreviewer();
