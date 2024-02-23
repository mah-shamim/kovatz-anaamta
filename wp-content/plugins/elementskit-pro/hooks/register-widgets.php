<?php
namespace ElementsKit\Hooks;

defined( 'ABSPATH' ) || exit;

class Register_Widgets {
	use \ElementsKit\Traits\Singleton;

	public function __construct() {
		add_filter( 'elementskit/widgets/list', [$this, 'get_list'] );
	}

	public function get_list($list) {

		return array_merge($list, [
			'blog-posts' => [
				'slug'    => 'blog-posts',
				'title'   => 'Blog Posts',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'blog-posts/',
				'widget-category' => 'wp-posts' // Post Widgets
			],
			'advanced-accordion' => [
				'slug'    => 'advanced-accordion',
				'title'   => 'Advanced Accordion',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'advanced-accordion/',
				'widget-category' => 'general' // General
			],
			'advanced-tab'       => [
				'slug'    => 'advanced-tab',
				'title'   => 'Advanced Tab',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'advanced-tab/',
				'widget-category' => 'general' // General
			],
			'hotspot'            => [
				'slug'    => 'hotspot',
				'title'   => 'Hotspot',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'hotspot/',
				'widget-category' => 'general' // General
			],
			'motion-text'        => [
				'slug'    => 'motion-text',
				'title'   => 'Motion Text',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'motion-text/',
				'widget-category' => 'general' // General
			],
			'twitter-feed'       => [
				'slug'    => 'twitter-feed',
				'title'   => 'Twitter Feed',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'twitter-feed/',
				'widget-category' => 'social-media-feeds' // Social Media Feeds Widgets
			],

			'instagram-feed'       => [
				'slug'    => 'instagram-feed',
				'title'   => 'Instagram Feed',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'instagram-feed/',
				'widget-category' => 'social-media-feeds' // Social Media Feeds Widgets
			],
			'gallery'              => [
				'slug'    => 'gallery',
				'title'   => 'Gallery',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'gallery/',
				'widget-category' => 'general' // General
			],
			'whatsapp'     => [
				'slug'    => 'whatsapp',
				'title'   => 'WhatsApp',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'whatsapp/',
				'widget-category' => 'general' // General
			],
			'chart'                => [
				'slug'    => 'chart',
				'title'   => 'Chart',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'chart/',
				'widget-category' => 'general' // General
			],
			'woo-category-list'    => [
				'slug'    => 'woo-category-list',
				'title'   => 'Woo Category List',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'woo-category-list/',
				'widget-category' => 'woocommerce' // Woocommerce Widgets
			],
			'woo-mini-cart'        => [
				'slug'    => 'woo-mini-cart',
				'title'   => 'Woo Mini Cart',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'woo-mini-cart/',
				'widget-category' => 'woocommerce' // Woocommerce Widgets
			],
			'woo-product-carousel' => [
				'slug'    => 'woo-product-carousel',
				'title'   => 'Woo Product Carousel',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'woo-product-carousel/',
				'widget-category' => 'woocommerce' // Woocommerce Widgets
			],
			'woo-product-list'     => [
				'slug'    => 'woo-product-list',
				'title'   => 'Woo Product List',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'woo-product-list/',
				'widget-category' => 'woocommerce' // Woocommerce Widgets
			],
			'table'                => [
				'slug'    => 'table',
				'title'   => 'Table',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'table/',
				'widget-category' => 'general' // General
			],
			'timeline'             => [
				'slug'    => 'timeline',
				'title'   => 'Timeline',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'timeline/',
				'widget-category' => 'general' // General
			],
			'creative-button'      => [
				'slug'    => 'creative-button',
				'title'   => 'Creative Button',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'creative-button/',
				'widget-category' => 'general' // General
			],
			'vertical-menu'        => [
				'slug'    => 'vertical-menu',
				'title'   => 'Vertical Menu',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'vertical-menu/',
				'widget-category' => 'header-footer' // ElementsKit Header Footer
			],
			'advanced-toggle'      => [
				'slug'    => 'advanced-toggle',
				'title'   => 'Advanced Toggle',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'advanced-toggle/',
				'widget-category' => 'general' // General
			],
			'image-swap'           => [
				'slug'    => 'image-swap',
				'title'   => 'Image Swap',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'image-swap/',
				'widget-category' => 'general' // General
			],
			'video-gallery'        => [
				'slug'    => 'video-gallery',
				'title'   => 'Video Gallery',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'video-gallery/',
				'widget-category' => 'general' // General
			],
			'zoom'                 => [
				'slug'    => 'zoom',
				'title'   => 'Zoom',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'zoom/',
				'widget-category' => 'meeting-widgets' // Meeting Widgets
			],
			'behance-feed'         => [
				'slug'    => 'behance-feed',
				'title'   => 'Behance Feed',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'behance-feed/',
				'widget-category' => 'social-media-feeds' // Social Media Feeds Widgets
			],
			'breadcrumb' => [
				'slug'    => 'breadcrumb',
				'title'   => 'Breadcrumb',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'breadcrumb/',
				'widget-category' => 'general' // General
			],
			'dribble-feed' => [
				'slug'    => 'dribble-feed',
				'title'   => 'Dribbble Feed',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'dribble-feed/',
				'widget-category' => 'social-media-feeds' // Social Media Feeds Widgets
			],
			'facebook-feed' => [
				'slug'    => 'facebook-feed',
				'title'   => 'Facebook feed',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'facebook-feed/',
				'widget-category' => 'social-media-feeds' // Social Media Feeds Widgets
			],  
			'facebook-review' => [
				'slug'    => 'facebook-review',
				'title'   => 'Facebook review',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'facebook-review/',
				'widget-category' => 'review-widgets' // Review Widgets
			], 
			'yelp' => [
				'slug'    => 'yelp',
				'title'   => 'Yelp',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'yelp/',
				'widget-category' => 'review-widgets' // Review Widgets
			],
			'popup-modal' => [
				'slug'    => 'popup-modal',
				'title'   => 'Popup Modal',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'popup-modal/',
				'widget-category' => 'general' // General
			],
			'google-map' => [
				'slug'    => 'google-map',
				'title'   => 'Google Map',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'google-map/',
				'widget-category' => 'general' // General 
			],
			'unfold' => [
				'slug'    => 'unfold',
				'title'   => 'Unfold',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'unfold/',
				'widget-category' => 'general' // General 
			],
			'pinterest-feed' => [
				'slug'    => 'pinterest-feed',
				'title'   => 'Pinterest Feed',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'pinterest-feed/',
				'widget-category' => 'social-media-feeds' // Social Media Feeds Widgets
			],
			'advanced-slider' => [
				'slug'    => 'advanced-slider',
				'title'   => 'Advanced Slider',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'advanced-slider/',
				'widget-category' => 'general' // General
			],
			'image-hover-effect' => [
				'slug'    => 'image-hover-effect',
				'title'   => 'Image Hover Effect',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'image-hover-effect/',
				'widget-category' => 'general' // General
			],            
			'fancy-animated-text' => [
				'slug'    => 'fancy-animated-text',
				'title'   => 'Fancy Animated Text',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'fancy-animated-text/',
				'widget-category' => 'general' // General
			],
			'price-menu' => [
				'slug'    => 'price-menu',
				'title'   => 'Price Menu',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'price-menu/',
				'widget-category' => 'general' // General
			],
			'team-slider' => [
				'slug'    => 'team-slider',
				'title'   => 'Team Slider',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'team-slider/',
				'widget-category' => 'general' // General
			],
			'audio-player' => [
				'slug'    => 'audio-player',
				'title'   => 'Audio Player',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'audio-player/',
				'widget-category' => 'general' // General
			],
			'stylish-list' => [
				'slug'    => 'stylish-list',
				'title'   => 'Stylish List',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'stylish-list/',
				'widget-category' => 'general' // General
			],
			'flip-box' => [
				'slug'    => 'flip-box',
				'title'   => 'Flip Box',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'flip-box/',
				'widget-category' => 'general' // General
			],
			'image-morphing' => [
				'slug'    => 'image-morphing',
				'title'   => 'Image Morphing',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'image-morphing/',
				'widget-category' => 'general' // General
			],
			'content-ticker' => [
				'slug'    => 'content-ticker',
				'title'   => 'Content Ticker',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'content-ticker/',
				'widget-category' => 'general' // General
			],
			'coupon-code' => [
				'slug'    => 'coupon-code',
				'title'   => 'Coupon Code',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'coupon-code/',
				'widget-category' => 'general' // General
			],
			'comparison-table' => [
				'slug'    => 'comparison-table',
				'title'   => 'Comparison Table',
				'package' => 'pro',
				'path'    => \ElementsKit::widget_dir() . 'comparison-table/',
				'widget-category' => 'general' // General
			]
		]);
	}
}