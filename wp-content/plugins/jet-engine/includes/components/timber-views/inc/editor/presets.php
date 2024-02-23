<?php
/**
 * Presets storage
 */
namespace Jet_Engine\Timber_Views\Editor;

use Jet_Engine\Timber_Views\Package;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Presets {

	public $twig;

	public function __construct( $twig ) {
		$this->twig = $twig;
	}

	public function get_presets() {
		return apply_filters( 'jet-engine/twig-views/presets', [
			[
				'name' => __( 'Preset 1', 'jet-engine' ),
				'html' => "<div class=\"cb-preset-01\">
	<a href=\"{{ jet_engine_url(args={}) }}\" class=\"cb-preset-01__thumb\">
		<img src=\"{{ jet_engine_url(args={source:'_thumbnail_url',size:'thumbnail',fallback:'https://picsum.photos/150.jpg'}) }}\" alt=\"{{ jet_engine_data(args={fallback:'Post title'}) }}\">
	</a>
	<div class=\"cb-preset-01__content\">
		<div class=\"cb-preset-01__date\"><svg viewbox=\"0 0 24 24\" class=\"cb-preset-01__date-icon\"><path d=\"M12 2c5.514 0 10 4.486 10 10s-4.486 10-10 10-10-4.486-10-10 4.486-10 10-10zm0-2c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm1 12v-6h-2v8h7v-2h-5z\"/></svg>{{ jet_engine_data(args={key:'post_date'})|jet_engine_callback(args={cb:'jet_engine_date'}) }}</div>
		<div class=\"cb-preset-01__title\">
			<a href=\"{{ jet_engine_url(args={}) }}\" class=\"cb-preset-01__title-link\">{{ jet_engine_data(args={fallback:'Post title'}) }}</a>
		</div>
	</div>
</div>",
				'css'  => "selector .cb-preset-01 {
	display: flex;
	align-items: center;
	gap: 30px;
}
/* Thumbnail */
selector .cb-preset-01__thumb {
	flex: 0 0 150px;
}
/* Date */
selector .cb-preset-01__date {
	opacity: .5;
	display: flex;
	align-items: center;
	gap: 5px;
	margin: 0 0 5px;
}
selector svg.cb-preset-01__date-icon {
	width: 16px;
	height: 16px;
}
/* Title */
selector .cb-preset-01__title {
	font-size: 1.4em;
	line-height: 1.2em;
	font-weight: bold;
}
selector .cb-preset-01__title-link {
	text-decoration: none;
}
",
			],
			[
				'name' => __( 'Preset 2', 'jet-engine' ),
				'html' => "<div class=\"cb-preset-02\">
	<a href=\"{{ jet_engine_url(args={}) }}\" class=\"cb-preset-02__link\">
		<img src=\"{{ jet_engine_url(args={source:'_thumbnail_url',size:'thumbnail',fallback:'https://picsum.photos/400.jpg'}) }}\" alt=\"{{ jet_engine_data(args={fallback:'Post title'}) }}\" class=\"cb-preset-02__thumb\">
		<div class=\"cb-preset-02__content\">
			<div class=\"cb-preset-02__content-inner\">
				<div class=\"cb-preset-02__date\">
					<svg class=\"cb-preset-02__icon\" xmlns=\"http://www.w3.org/2000/svg\" viewbox=\"0 0 24 24\"><path d=\"M12 2c5.514 0 10 4.486 10 10s-4.486 10-10 10-10-4.486-10-10 4.486-10 10-10zm0-2c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm1 12v-6h-2v8h7v-2h-5z\"/></svg>{{ jet_engine_data(args={key:'post_date'})|jet_engine_callback(args={cb:'jet_engine_date'}) }}
				</div>
				<div class=\"cb-preset-02__title\">
					{{ jet_engine_data(args={fallback:'Post title'}) }}
				</div>
				<div class=\"cb-preset-02__author\">
					<svg class=\"cb-preset-02__icon\" viewbox=\"0 0 24 24\"><path d=\"M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm7.753 18.305c-.261-.586-.789-.991-1.871-1.241-2.293-.529-4.428-.993-3.393-2.945 3.145-5.942.833-9.119-2.489-9.119-3.388 0-5.644 3.299-2.489 9.119 1.066 1.964-1.148 2.427-3.393 2.945-1.084.25-1.608.658-1.867 1.246-1.405-1.723-2.251-3.919-2.251-6.31 0-5.514 4.486-10 10-10s10 4.486 10 10c0 2.389-.845 4.583-2.247 6.305z\"/></svg>{{ post.author.name }}
				</div>
			</div>
		</div>
	</a>
</div>",
				'css'  => "selector .cb-preset-02__link {
	display: block;
	position: relative;
	text-decoration: none;
}
/* Thumbnail */
selector .cb-preset-02__thumb {
	display: block;
	width: 100%;
	height: auto;
}
/* Icons */
selector .cb-preset-02__icon {
	width: 16px;
}
selector .cb-preset-02__icon path {
	fill: currentColor;
}
/* Content */
selector .cb-preset-02__content {
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background: rgba(0,0,0,.7);
	color: #fff;
	transition: all 150ms linear;
	opacity: 0;
}
selector .cb-preset-02__content:hover {
	opacity: 1;
}
selector .cb-preset-02__content-inner {
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
	height: 100%;
}
/* Title */
selector .cb-preset-02__title {
	font-size: 2em;
	line-height: 1.2em;
	text-align: center;
	font-weight: bold;
	padding: 5px 0;
}
/* Date and author */
selector .cb-preset-02__date,
selector .cb-preset-02__author {
	opacity: .7;
	display: flex;
	align-items: center;
	gap: 6px;
	padding: 5px 0;
}
",
			],
			[
				'name' => __( 'Preset 3', 'jet-engine' ),
				'html' => "<div class=\"cb-preset-03\">
	<a href=\"{{ jet_engine_url(args={}) }}\" class=\"cb-preset-03__thumb-link\">
		<img class=\"cb-preset-03__thumb-img\" src=\"{{ jet_engine_url(args={source:'_thumbnail_url',fallback:'https://picsum.photos/400.jpg'})|resize(300,150) }}\" alt=\"{{ jet_engine_data(args={fallback:'Post title'}) }}\">
		<div class=\"cb-preset-03__cover\">
			<div class=\"cb-preset-03__author\">
				<svg class=\"cb-preset-03__author-icon\" xmlns=\"http://www.w3.org/2000/svg\" viewbox=\"0 0 24 24\"><path d=\"M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm7.753 18.305c-.261-.586-.789-.991-1.871-1.241-2.293-.529-4.428-.993-3.393-2.945 3.145-5.942.833-9.119-2.489-9.119-3.388 0-5.644 3.299-2.489 9.119 1.066 1.964-1.148 2.427-3.393 2.945-1.084.25-1.608.658-1.867 1.246-1.405-1.723-2.251-3.919-2.251-6.31 0-5.514 4.486-10 10-10s10 4.486 10 10c0 2.389-.845 4.583-2.247 6.305z\"/></svg>
				{{ post.author.name }}
			</div>
		</div>
	</a>
	<div class=\"cb-preset-03__content\">
		<a href=\"{{ jet_engine_url(args={}) }}\" class=\"cb-preset-03__title\">
			{{ jet_engine_data(args={fallback:'Post title'}) }}
		</a>
		<div class=\"cb-preset-03__excerpt\">
			{{ jet_engine_data(args={key:'post_excerpt',wp_excerpt:'true',excerpt_length:'10',fallback:'Post excerpt'}) }}
		</div>
		<a href=\"{{ jet_engine_url(args={}) }}\" class=\"cb-preset-03__more\">
			Read more
		</a>
	</div>
</div>",
				'css'  => "selector .cb-preset-03 {
	overflow: hidden;
	border-radius: 5px;
	box-shadow: 0 5px 10px rgba(0,0,0,.2);
}
/* Image and author */
selector .cb-preset-03__thumb-link {
	display: block;
	position: relative;
}
selector .cb-preset-03__thumb-img {
	width: 100%;
	display: block;
}
selector .cb-preset-03__cover {
	position: absolute;
	left: 0;
	right: 0;
	bottom: 0;
	top: 0;
	background: linear-gradient(0deg, rgba(0,0,0,1) 10%, rgba(0,0,0,0) 60%);
}
selector .cb-preset-03__author {
	display: flex;
	gap: 6px;
	align-items: center;
	color: #fff;
	position: absolute;
	bottom: 0;
	left: 0;
	padding: 20px;
	font-weight: bold;
}
selector .cb-preset-03__author-icon {
	width: 20px;
	height: 20px;
}
selector .cb-preset-03__author-icon path {
	fill: currentcolor;
}
/* Content */
selector .cb-preset-03__content {
	padding: 20px;
	background: #f3f7f9;
}
selector .cb-preset-03__title {
	text-decoration: none;
	font-size: 1.5em;
	line-height: 1.3em;
	font-weight: bold;
	display: block;
	margin: 0 0 15px;
}
selector .cb-preset-03__excerpt {
	margin: 0 0 20px;
}
selector .cb-preset-03__more {
	border: 2px solid currentcolor;
	border-radius: 4px;
	font-size: 1.2em;
	line-height: 1.2em;
	font-weight: bold;
	text-decoration: none;
	width: 100%;
	display: block;
	text-align: center;
	padding: 10px;
	box-sizing: border-box;
}
",
			],
			[
				'name' => __( 'Preset 4', 'jet-engine' ),
				'html' => "<div class=\"cb-preset-04\">
	<a href=\"{{ jet_engine_url(args={}) }}\" class=\"cb-preset-04__link\">
		<img src=\"{{ jet_engine_url(args={source:'_thumbnail_url',size:'thumbnail',fallback:'https://picsum.photos/400.jpg'}) }}\" alt=\"{{ jet_engine_data(args={fallback:'Post title'}) }}\" class=\"cb-preset-04__thumb\">
		<div class=\"cb-preset-04__content\">
			<div class=\"cb-preset-04__content-inner\">
				<div class=\"cb-preset-04__date\">
					<svg class=\"cb-preset-04__icon\" xmlns=\"http://www.w3.org/2000/svg\" viewbox=\"0 0 24 24\"><path d=\"M12 2c5.514 0 10 4.486 10 10s-4.486 10-10 10-10-4.486-10-10 4.486-10 10-10zm0-2c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm1 12v-6h-2v8h7v-2h-5z\"/></svg>{{ jet_engine_data(args={key:'post_date'})|jet_engine_callback(args={cb:'jet_engine_date'}) }}
				</div>
				<div class=\"cb-preset-04__title\">
					{{ jet_engine_data(args={fallback:'Post title'}) }}
				</div>
				<div class=\"cb-preset-04__author\">
					<svg class=\"cb-preset-04__icon\" viewbox=\"0 0 24 24\"><path d=\"M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm7.753 18.305c-.261-.586-.789-.991-1.871-1.241-2.293-.529-4.428-.993-3.393-2.945 3.145-5.942.833-9.119-2.489-9.119-3.388 0-5.644 3.299-2.489 9.119 1.066 1.964-1.148 2.427-3.393 2.945-1.084.25-1.608.658-1.867 1.246-1.405-1.723-2.251-3.919-2.251-6.31 0-5.514 4.486-10 10-10s10 4.486 10 10c0 2.389-.845 4.583-2.247 6.305z\"/></svg>{{ post.author.name }}
				</div>
			</div>
		</div>
	</a>
</div>",
				'css'  => "selector .cb-preset-04__link {
	display: block;
	position: relative;
	text-decoration: none;
}
/* Thumbnail */
selector .cb-preset-04__thumb {
	display: block;
	width: 100%;
	height: auto;
}
/* Icons */
selector .cb-preset-04__icon {
	width: 16px;
}
selector .cb-preset-04__icon path {
	fill: currentColor;
}
/* Content */
selector .cb-preset-04__content {
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background: linear-gradient(0deg, rgba(0,0,0,.8) 30%, rgba(0,0,0,0) 60%);
	color: #fff;
}

selector .cb-preset-04__content-inner {
	display: flex;
	flex-direction: column;
	align-items: flex-start;
	justify-content: flex-end;
	height: 100%;
	box-sizing: border-box;
	padding: 30px;
}
/* Title */
selector .cb-preset-04__title {
	font-size: 2em;
	line-height: 1.5em;
	font-weight: bold;
}
/* Date and author */
selector .cb-preset-04__date,
selector .cb-preset-04__author {
	opacity: .7;
	display: flex;
	align-items: center;
	gap: 6px;
	padding: 5px 0;
}

",
			],
			[
				'name' => __( 'Preset 5', 'jet-engine' ),
				'html' => "<div class=\"cb-preset-05\">
	<div class=\"cb-preset-05__excerpt\">
		{{ jet_engine_data(args={key:'post_excerpt',wp_excerpt:'true',excerpt_length:'20',fallback:'Post excerpt'}) }}
	</div>
	<a href=\"{{ jet_engine_url(args={}) }}\" class=\"cb-preset-05__thumb-link\">
		<img class=\"cb-preset-05__thumb-img\" src=\"{{ jet_engine_url(args={source:'_thumbnail_url',size:'thumbnail',fallback:'https://picsum.photos/100.jpg'}) }}\" alt=\"{{ jet_engine_data(args={fallback:'Post title'}) }}\">
	</a>
	<div class=\"cb-preset-05__title\">
		{{ jet_engine_data(args={fallback:'Post title'}) }}
	</div>
	<div class=\"cb-preset-05__author\">
		{{ post.author.name }}
	</div>
</div>",
				'css'  => "selector .cb-preset-05 {
	text-align: center;
	display: flex;
	flex-direction: column;
	align-items: center;
}
selector .cb-preset-05__excerpt {
	margin: 0 0 30px;
}
selector .cb-preset-05__thumb-link {
	border: 4px solid currentcolor;
	margin: 0 0 5px;
	width: 100px;
	height: 100px;
	box-sizing: border-box;
	border-radius: 50px;
	overflow: hidden;
}
selector .cb-preset-05__thumb-img {
	display: block;
}
selector .cb-preset-05__title {
	font-size: 1.2em;
	font-weight: bold;
}
selector .cb-preset-05__author {
	font-style: italic;
	opacity: .5;
}
",
			],
		] );
	}

	public function get_presets_with_preview( $settings, $listing_id ) {
		
		$presets = $this->get_presets();
		$preview = new \Jet_Engine_Listings_Preview( $settings, $listing_id );
		$preview_object = $preview->get_preview_object();

		foreach ( $presets as $index => $preset ) {
			
			$html = Package::instance()->render_html(
				$preset['html'],
				Package::instance()->get_context_for_object( $preview_object ),
				$this->twig
			);

			$css = sprintf(
				'<style>%s</style>',
				str_replace( 'selector', '.jet-engine-timber-preview-' . $index, $preset['css'] )
			);

			$presets[ $index ]['preview'] = sprintf(
				'<div class="jet-engine-timber-preview-%1$d">%2$s%3$s</div>',
				$index, $css, $html
			);

		}

		return $presets;

	}

}
