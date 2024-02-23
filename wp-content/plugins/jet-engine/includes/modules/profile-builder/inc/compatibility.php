<?php
namespace Jet_Engine\Modules\Profile_Builder;

class Compatibility {

	/**
	 * Constructor for the class
	 */
	public function __construct() {

		$has_seo_plugin = false;

		// Rank Math SEO compatibility hooks.
		if ( defined( 'RANK_MATH_VERSION' ) ) {
			$has_seo_plugin = true;

			add_filter( 'rank_math/frontend/title',       array( Module::instance()->frontend, 'set_document_title_on_single_user_page' ) );
			add_filter( 'rank_math/frontend/description', array( Module::instance()->frontend, 'get_user_page_seo_description' ) );
			add_filter( 'rank_math/frontend/canonical',   array( Module::instance()->frontend, 'modify_canonical_url' ) );

			// Opengraph Type
			add_filter( 'rank_math/opengraph/type', array( $this, 'modify_opengraph_type' ) );

			// Remove specific article tags
			add_filter( 'rank_math/opengraph/facebook/og_updated_time',        array( $this, 'remove_seo_tag' ) );
			add_filter( 'rank_math/opengraph/facebook/article_published_time', array( $this, 'remove_seo_tag' ) );
			add_filter( 'rank_math/opengraph/facebook/article_modified_time',  array( $this, 'remove_seo_tag' ) );

			// Remove Slack tags
			add_action( 'rank_math/opengraph/slack', array( $this, 'remove_slack_tags' ) );

			// Opengraph Images
			$networks = array( 'facebook', 'twitter' );

			foreach ( $networks as $network ) {
				add_action( 'rank_math/opengraph/' . $network, array( $this, 'rank_math_opengraph_image' ), 5 );
			}
		}

		// Yoast SEO compatibility hooks.
		if ( defined( 'WPSEO_VERSION' ) ) {
			$has_seo_plugin = true;

			add_filter( 'wpseo_opengraph_title', array( Module::instance()->frontend, 'set_document_title_on_single_user_page' ) );
			add_filter( 'wpseo_twitter_title',   array( Module::instance()->frontend, 'set_document_title_on_single_user_page' ) );

			add_filter( 'wpseo_metadesc',            array( Module::instance()->frontend, 'get_user_page_seo_description' ) );
			add_filter( 'wpseo_opengraph_desc',      array( Module::instance()->frontend, 'get_user_page_seo_description' ) );
			add_filter( 'wpseo_twitter_description', array( Module::instance()->frontend, 'get_user_page_seo_description' ) );

			add_filter( 'wpseo_canonical',     array( Module::instance()->frontend, 'modify_canonical_url' ) );
			add_filter( 'wpseo_opengraph_url', array( Module::instance()->frontend, 'modify_canonical_url' ) );

			// Opengraph Type
			add_filter( 'wpseo_opengraph_type', array( $this, 'modify_opengraph_type' ) );

			// Remove specific article tags
			add_filter( 'wpseo_frontend_presenter_classes', array( $this, 'remove_yoast_presenter_classes' ) );

			// Opengraph Images
			add_filter( 'wpseo_add_opengraph_images', array( $this, 'yoast_opengraph_image' ) );
			add_filter( 'wpseo_twitter_image',        array( $this, 'yoast_twitter_image' ) );
		}

		// SEOPress compatibility hooks.
		if ( defined( 'SEOPRESS_VERSION' ) ) {
			$has_seo_plugin = true;

			add_filter( 'seopress_titles_title',     array( Module::instance()->frontend, 'set_document_title_on_single_user_page' ) );
			add_filter( 'seopress_titles_desc',      array( Module::instance()->frontend, 'get_user_page_seo_description' ) );
			add_filter( 'seopress_titles_canonical', array( $this, 'modify_seopress_canonical' ) );

			// Opengraph Type
			add_filter( 'seopress_social_og_type', array( $this, 'seopress_og_type' ) );

			// Remove specific article tags
			add_action( 'wp_head', array( $this, 'remove_seopress_article_tags' ), 0 );

			// Opengraph Images
			add_filter( 'seopress_social_og_thumb',           array( $this, 'seopress_social_og_image' ) );
			add_filter( 'seopress_social_twitter_card_thumb', array( $this, 'seopress_social_twitter_image' ) );
		}

		if ( $has_seo_plugin ) {
			remove_action( 'wp_head', array( Module::instance()->frontend, 'print_description_meta_tag' ), 1 );
		}
	}

	public function modify_seopress_canonical( $canonical ) {

		if ( ! Module::instance()->query->is_single_user_page() ) {
			return $canonical;
		}

		return sprintf( '<link rel="canonical" href="%s" />', htmlspecialchars( urldecode( wp_get_canonical_url() ) ) );
	}

	public function rank_math_opengraph_image( $opengraph ) {

		if ( ! Module::instance()->query->is_single_user_page() ) {
			return;
		}

		$user = Module::instance()->query->get_queried_user();

		if ( ! $user ) {
			return;
		}

		$user_seo_image = $this->get_user_page_image();

		add_action( 'rank_math/opengraph/' . $opengraph->network . '/add_images', function ( $opengraph_image ) use ( $opengraph, $user, $user_seo_image ) {

			if ( ! empty( $user_seo_image ) ) {
				$opengraph_image->add_image_by_id( $user_seo_image );
				return;
			}

			$image_id = \RankMath\Helper::get_user_meta( $opengraph->prefix . '_image_id', $user->ID );

			if ( empty( $image_id ) ) {
				return;
			}

			$opengraph_image->add_image_by_id( $image_id );
		} );
	}

	public function yoast_opengraph_image( $image_container ) {

		if ( ! Module::instance()->query->is_single_user_page() ) {
			return $image_container;
		}

		$user_seo_image = $this->get_user_page_image();

		if ( empty( $user_seo_image ) ) {
			return $image_container;
		}

		$image_container->add_image_by_id( $user_seo_image );

		return $image_container;
	}

	public function yoast_twitter_image( $image ) {

		if ( ! Module::instance()->query->is_single_user_page() ) {
			return $image;
		}

		$user_seo_image = $this->get_user_page_image( 'url' );

		if ( empty( $user_seo_image ) ) {
			return $image;
		}

		return $user_seo_image;
	}

	public function seopress_social_og_image( $image_tags ) {

		if ( ! Module::instance()->query->is_single_user_page() ) {
			return $image_tags;
		}

		$user_seo_image = $this->get_user_page_image();

		if ( empty( $user_seo_image ) ) {
			return $image_tags;
		}

		$image_src = wp_get_attachment_image_src( $user_seo_image, 'full' );

		if ( empty( $image_src ) ) {
			return $image_tags;
		}

		$meta_tags = '<meta property="og:image" content="' . $image_src[0] . '">' . "\n";
		$meta_tags .= '<meta property="og:image:width" content="' . $image_src[1] . '">' . "\n";
		$meta_tags .= '<meta property="og:image:height" content="' . $image_src[2] . '">' . "\n";

		$alt = get_post_meta( $user_seo_image, '_wp_attachment_image_alt', true );

		if ( ! empty( $alt ) ) {
			$alt = trim( strip_tags( $alt ) );

			$meta_tags .= '<meta property="og:image:alt" content="' . $alt . '">' . "\n";
		}

		return $meta_tags;
	}

	public function seopress_social_twitter_image( $image_tag ) {

		if ( ! Module::instance()->query->is_single_user_page() ) {
			return $image_tag;
		}

		$user_seo_image = $this->get_user_page_image( 'url' );

		if ( empty( $user_seo_image ) ) {
			return $image_tag;
		}

		return '<meta name="twitter:image" content="' . $user_seo_image . '">';
	}

	public function get_user_page_image( $prop = 'id' ) {

		$user = Module::instance()->query->get_queried_user();

		if ( ! $user ) {
			return null;
		}

		$field = Module::instance()->settings->get( 'user_page_seo_image', '' );

		if ( empty( $field ) ) {
			return null;
		}

		$image = get_user_meta( $user->ID, $field, true );

		if ( empty( $image ) ) {
			return null;
		}

		$img_data = \Jet_Engine_Tools::get_attachment_image_data_array( $image, $prop );

		if ( empty( $img_data[ $prop ] ) ) {
			return null;
		}

		return $img_data[ $prop ];
	}

	public function modify_opengraph_type( $type ) {

		if ( Module::instance()->query->is_single_user_page() ) {
			return 'profile';
		}

		return $type;
	}

	public function seopress_og_type( $og_type ) {

		if ( ! Module::instance()->query->is_single_user_page() ) {
			return $og_type;
		}

		return '<meta property="og:type" content="profile">';
	}

	public function remove_seo_tag( $content ) {

		if ( Module::instance()->query->is_single_user_page() ) {
			return null;
		}

		return $content;
	}

	public function remove_slack_tags( $network ) {

		if ( ! Module::instance()->query->is_single_user_page() ) {
			return;
		}

		remove_action( 'rank_math/opengraph/slack', array( $network, 'enhanced_data' ), 20 );
	}

	public function remove_yoast_presenter_classes( $presenters ) {

		if ( ! Module::instance()->query->is_single_user_page() ) {
			return $presenters;
		}

		$presenters = array_filter( $presenters, function ( $presenter ) {

			if ( false !== strpos( $presenter, 'Article_Published_Time_Presenter' ) ) {
				return false;
			}

			if ( false !== strpos( $presenter, 'Article_Modified_Time_Presenter' ) ) {
				return false;
			}

			return true;
		} );

		return $presenters;
	}

	public function remove_seopress_article_tags() {

		if ( ! Module::instance()->query->is_single_user_page() ) {
			return;
		}

		remove_action( 'wp_head', 'seopress_social_facebook_og_author_hook', 1 );
	}

}
