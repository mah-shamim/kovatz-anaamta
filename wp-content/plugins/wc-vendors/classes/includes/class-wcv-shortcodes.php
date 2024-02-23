<?php

/**
 * WCV_Shortcodes class.
 *
 * @class          WCV_Shortcodes
 * @version        1.0.0
 * @package        WCVendors/Classes
 * @category       Class
 * @author         WC Vendors (Jamie Madden http://github.com/digitalchild)
 */
class WCV_Shortcodes {

    /**
     * Initialize shortcodes
     */
    public function __construct() {
    // Define shortcodes.
    // Recent Products.
    add_shortcode( 'wcv_recent_products', array( $this, 'recent_products' ) );
    // Products by vendor.
    add_shortcode( 'wcv_products', array( $this, 'products' ) );
    // Featured products by vendor.
    add_shortcode( 'wcv_featured_products', array( $this, 'featured_products' ) );
    // Sale products by vendor.
    add_shortcode( 'wcv_sale_products', array( $this, 'sale_products' ) );
    // Top Rated products by vendor.
    add_shortcode( 'wcv_top_rated_products', array( $this, 'top_rated_products' ) );
    // Best Selling product.
    add_shortcode( 'wcv_best_selling_products', array( $this, 'best_selling_products' ) );
    // List products in a category shortcode.
    add_shortcode( 'wcv_product_category', array( $this, 'product_category' ) );
    // List of paginated vendors.
    add_shortcode( 'wcv_vendorslist', array( $this, 'wcv_vendorslist' ) );
    // Show the sold by label based on the product page.
    add_shortcode( 'wcv_sold_by', array( $this, 'wcv_sold_by' ) );

    // Vendor list actions.
    add_action( 'wcvendors_before_vendor_list', 'wcv_before_vendor_list', 10, 1 );
    add_action( 'wcvendors_after_vendor_list', 'wcv_after_vendor_list' );
    add_action( 'wcvendors_vendor_list_filter', 'wcv_vendor_list_filter', 10, 3 );
    add_action( 'wcvendors_vendor_list_loop', 'wcv_vendor_list_loop', 10, 1 );
    }

    /**
     * Get vendor by slug.
     *
     * @param string $slug The vendor slug.
     * @return int|string
     */
    public static function get_vendor( $slug ) {

    $vendor_id = get_user_by( 'slug', $slug );

    if ( ! empty( $vendor_id ) ) {
        $author = $vendor_id->ID;
    } else {
        $author = '';
    }

    return $author;
    }

    /**
     * Get recent products based on vendor username
     *
     * @param array|string $atts The shortcode attributes.
     * @return string
     */
    public static function recent_products( $atts ) {

    global $woocommerce_loop;

    extract( // phpcs:ignore
        shortcode_atts(
            array(
				'per_page' => '12',
				'vendor'   => '',
				'columns'  => '4',
				'orderby'  => 'date',
				'order'    => 'desc',
            ),
            $atts
        )
    );

    $meta_query = WC()->query->get_meta_query();

    $args = array(
        'post_type'           => 'product',
        'post_status'         => 'publish',
        'author'              => self::get_vendor( $vendor ),
        'ignore_sticky_posts' => 1,
        'posts_per_page'      => $per_page,
        'orderby'             => $orderby,
        'order'               => $order,
        'meta_query'          => $meta_query,
    );

    ob_start();

    $args     = apply_filters_deprecated( 'wcv_shortcode_products_query', array( $args, $atts ), '2.3.0', 'wcvendors_shortcode_products_query' );
    $products = new WP_Query( apply_filters( 'wcvendors_shortcode_products_query', $args, $atts ) );

    $woocommerce_loop['columns'] = esc_attr( $columns );

    if ( $products->have_posts() ) : ?>

        <?php woocommerce_product_loop_start(); ?>

        <?php
        while ( $products->have_posts() ) :
        $products->the_post();
        ?>

        <?php wc_get_template_part( 'content', 'product' ); ?>

        <?php endwhile; // end of the loop. ?>

        <?php woocommerce_product_loop_end(); ?>

        <?php
    endif;

    wp_reset_postdata();

    return '<div class="woocommerce columns-' . esc_attr( $columns ) . '">' . ob_get_clean() . '</div>';
    }

    /**
     * List all products for a vendor shortcode
     *
     * @access public
     *
     * @param array|string $atts The shortcode attributes.
     *
     * @return string
     */
    public static function products( $atts ) {

    global $woocommerce_loop;

    if ( empty( $atts ) ) {
        return '';
    }

    extract( // phpcs:ignore
        shortcode_atts(
            array(
				'vendor'  => '',
				'columns' => '4',
				'orderby' => 'title',
				'order'   => 'asc',
            ),
            $atts
        )
    );

    $meta_query = WC()->query->get_meta_query();

    $args = array(
        'post_type'           => 'product',
        'post_status'         => 'publish',
        'author'              => self::get_vendor( $vendor ),
        'ignore_sticky_posts' => 1,
        'orderby'             => $orderby,
        'order'               => $order,
        'posts_per_page'      => -1,
        'meta_query'          => $meta_query,
    );

    if ( isset( $atts['skus'] ) ) {
        $skus                 = explode( ',', $atts['skus'] );
        $skus                 = array_map( 'trim', $skus );
        $args['meta_query'][] = array(
			'key'     => '_sku',
			'value'   => $skus,
			'compare' => 'IN',
        );
    }

    if ( isset( $atts['ids'] ) ) {
        $ids              = explode( ',', $atts['ids'] );
        $ids              = array_map( 'trim', $ids );
        $args['post__in'] = $ids;
    }

    ob_start();

    $args     = apply_filters_deprecated( 'wcv_shortcode_products_query', array( $args, $atts ), '2.3.0', 'wcvendors_shortcode_products_query' );
    $products = new WP_Query( apply_filters( 'wcvendors_shortcode_products_query', $args, $atts ) );

    $woocommerce_loop['columns'] = esc_attr( $columns );

    if ( $products->have_posts() ) :
        ?>

        <?php woocommerce_product_loop_start(); ?>

        <?php
        while ( $products->have_posts() ) :
        $products->the_post();
        ?>

        <?php wc_get_template_part( 'content', 'product' ); ?>

        <?php
        endwhile; // end of the loop.
        ?>

        <?php woocommerce_product_loop_end(); ?>

        <?php
    endif;

    wp_reset_postdata();

    return '<div class="woocommerce columns-' . esc_attr( $columns ) . '">' . ob_get_clean() . '</div>';
    }


    /**
     * Output featured products
     *
     * @access public
     *
     * @param array|string $atts The shortcode attributes.
     *
     * @return string
     */
    public static function featured_products( $atts ) {

    global $woocommerce_loop;

    extract( // phpcs:ignore
        shortcode_atts(
            array(
				'vendor'   => '',
				'per_page' => '12',
				'columns'  => '4',
				'orderby'  => 'date',
				'order'    => 'desc',
            ),
            $atts
        )
    );

    $meta_query  = WC()->query->get_meta_query();
    $tax_query   = WC()->query->get_tax_query();
    $tax_query[] = array(
        'taxonomy' => 'product_visibility',
        'field'    => 'name',
        'terms'    => 'featured',
        'operator' => 'IN',
    );

    $args = array(
        'post_type'           => 'product',
        'author'              => self::get_vendor( $vendor ),
        'post_status'         => 'publish',
        'ignore_sticky_posts' => 1,
        'posts_per_page'      => $per_page,
        'orderby'             => $orderby,
        'order'               => $order,
        'meta_query'          => $meta_query,
        'tax_query'           => $tax_query,
    );

    ob_start();

    $args     = apply_filters_deprecated( 'wcv_shortcode_products_query', array( $args, $atts ), '2.3.0', 'wcvendors_shortcode_products_query' );
    $products = new WP_Query( apply_filters( 'wcvendors_shortcode_products_query', $args, $atts ) );

    $woocommerce_loop['columns'] = esc_attr( $columns );

    if ( $products->have_posts() ) :
        ?>

        <?php woocommerce_product_loop_start(); ?>

        <?php
        while ( $products->have_posts() ) :
        $products->the_post();
        ?>

        <?php wc_get_template_part( 'content', 'product' ); ?>

        <?php
        endwhile; // end of the loop.
        ?>

        <?php woocommerce_product_loop_end(); ?>

        <?php
        wp_reset_postdata();

    endif;

    return '<div class="woocommerce columns-' . esc_attr( $columns ) . '">' . ob_get_clean() . '</div>';
    }

    /**
     * List all products on sale
     *
     * @access public
     *
     * @param array|string $atts The shortcode attributes.
     *
     * @return string
     */
    public static function sale_products( $atts ) {

    global $woocommerce_loop;

    extract( // phpcs:ignore
        shortcode_atts(
            array(
				'vendor'   => '',
				'per_page' => '12',
				'columns'  => '4',
				'orderby'  => 'title',
				'order'    => 'asc',
            ),
            $atts
        )
    );

    // Get products on sale.
    $product_ids_on_sale = wc_get_product_ids_on_sale();

    $meta_query   = array();
    $meta_query[] = WC()->query->visibility_meta_query();
    $meta_query[] = WC()->query->stock_status_meta_query();
    $meta_query   = array_filter( $meta_query );

    $args = array(
        'posts_per_page' => $per_page,
        'author'         => self::get_vendor( $vendor ),
        'orderby'        => $orderby,
        'order'          => $order,
        'no_found_rows'  => 1,
        'post_status'    => 'publish',
        'post_type'      => 'product',
        'meta_query'     => $meta_query,
        'post__in'       => array_merge( array( 0 ), $product_ids_on_sale ),
    );

    ob_start();

    $args     = apply_filters_deprecated( 'wcv_shortcode_products_query', array( $args, $atts ), '2.3.0', 'wcvendors_shortcode_products_query' );
    $products = new WP_Query( apply_filters( 'wcvendors_shortcode_products_query', $args, $atts ) );

    $woocommerce_loop['columns'] = esc_attr( $columns );

    if ( $products->have_posts() ) :
        ?>

        <?php woocommerce_product_loop_start(); ?>

        <?php
        while ( $products->have_posts() ) :
        $products->the_post();
        ?>

        <?php wc_get_template_part( 'content', 'product' ); ?>

        <?php
        endwhile; // end of the loop.
        ?>

        <?php woocommerce_product_loop_end(); ?>

        <?php
    endif;

    wp_reset_postdata();

    return '<div class="woocommerce columns-' . esc_attr( $columns ) . '">' . ob_get_clean() . '</div>';
    }

    /**
     * List top rated products on sale by vendor
     *
     * @access public
     *
     * @param array|string $atts The shortcode attributes.
     *
     * @return string
     */
    public static function top_rated_products( $atts ) {

    global $woocommerce_loop;

    extract( // phpcs:ignore
        shortcode_atts(
            array(
				'vendor'   => '',
				'per_page' => '12',
				'columns'  => '4',
				'orderby'  => 'title',
				'order'    => 'asc',
            ),
            $atts
        )
    );

    $meta_query = WC()->query->get_meta_query();

    $args = array(
        'post_type'           => 'product',
        'author'              => self::get_vendor( $vendor ),
        'post_status'         => 'publish',
        'ignore_sticky_posts' => 1,
        'orderby'             => $orderby,
        'order'               => $order,
        'posts_per_page'      => $per_page,
        'meta_query'          => $meta_query,
    );

    ob_start();

    add_filter( 'posts_clauses', array( 'WC_Shortcodes', 'order_by_rating_post_clauses' ) );

    $args     = apply_filters_deprecated( 'wcv_shortcode_products_query', array( $args, $atts ), '2.3.0', 'wcvendors_shortcode_products_query' );
    $products = new WP_Query( apply_filters( 'wcvendors_shortcode_products_query', $args, $atts ) );

    remove_filter( 'posts_clauses', array( 'WC_Shortcodes', 'order_by_rating_post_clauses' ) );

    $woocommerce_loop['columns'] = esc_attr( $columns );

    if ( $products->have_posts() ) :
        ?>

        <?php woocommerce_product_loop_start(); ?>

        <?php
        while ( $products->have_posts() ) :
        $products->the_post();
        ?>

        <?php wc_get_template_part( 'content', 'product' ); ?>

        <?php
        endwhile; // end of the loop.
        ?>

        <?php woocommerce_product_loop_end(); ?>

        <?php
        wp_reset_postdata();
    endif;

    return '<div class="woocommerce columns-' . esc_attr( $columns ) . '">' . ob_get_clean() . '</div>';
    }

    /**
     * List best selling products on sale per vendor
     *
     * @access public
     *
     * @param array $atts The shortcode attributes.
     *
     * @return string
     */
    public static function best_selling_products( $atts ) {

    global $woocommerce_loop;

    extract( // phpcs:ignore
        shortcode_atts(
            array(
				'vendor'   => '',
				'per_page' => '12',
				'columns'  => '4',
            ),
            $atts
        )
    );

    $args = array(
        'post_type'           => 'product',
        'post_status'         => 'publish',
        'author'              => self::get_vendor( $vendor ),
        'ignore_sticky_posts' => 1,
        'posts_per_page'      => $per_page,
        'meta_key'            => 'total_sales',
        'orderby'             => 'meta_value_num',
        'meta_query'          => array(
			array(
				'key'     => '_visibility',
				'value'   => array( 'catalog', 'visible' ),
				'compare' => 'IN',
			),
        ),
    );

    ob_start();

    $args     = apply_filters_deprecated( 'wcv_shortcode_products_query', array( $args, $atts ), '2.3.0', 'wcvendors_shortcode_products_query' );
    $products = new WP_Query( apply_filters( 'wcvendors_shortcode_products_query', $args, $atts ) );

    $woocommerce_loop['columns'] = esc_attr( $columns );

    if ( $products->have_posts() ) :
        ?>

        <?php woocommerce_product_loop_start(); ?>

        <?php
        while ( $products->have_posts() ) :
        $products->the_post();
        ?>

        <?php wc_get_template_part( 'content', 'product' ); ?>

        <?php
        endwhile; // end of the loop.
        ?>

        <?php woocommerce_product_loop_end(); ?>

        <?php
    endif;

    wp_reset_postdata();

    return '<div class="woocommerce columns-' . esc_attr( $columns ) . '">' . ob_get_clean() . '</div>';
    }

    /**
     * List products in a category shortcode
     *
     * @access public
     *
     * @param array|string $atts The shortcode attributes.
     *
     * @return string
     */
    public static function product_category( $atts ) {

    global $woocommerce_loop;

    extract( // phpcs:ignore
        shortcode_atts(
            array(
				'vendor'   => '',
				'per_page' => '12',
				'columns'  => '4',
				'orderby'  => 'title',
				'order'    => 'desc',
				'category' => '',  // Slugs.
				'operator' => 'IN', // Possible values are 'IN', 'NOT IN', 'AND'.
            ),
            $atts
        )
    );

    if ( ! $category ) {
        return '';
    }

    // Default ordering args.
    $ordering_args = WC()->query->get_catalog_ordering_args( $orderby, $order );

    $args = array(
        'post_type'           => 'product',
        'post_status'         => 'publish',
        'author'              => self::get_vendor( $vendor ),
        'ignore_sticky_posts' => 1,
        'orderby'             => $ordering_args['orderby'],
        'order'               => $ordering_args['order'],
        'posts_per_page'      => $per_page,
        'tax_query'           => array(
			array(
				'taxonomy' => 'product_visibility',
				'field'    => 'name',
				'terms'    => 'exclude-from-catalog',
				'operator' => 'NOT IN',
			),
			array(
				array(
					'taxonomy' => 'product_cat',
					'terms'    => array_map( 'sanitize_title', explode( ',', $category ) ),
					'field'    => 'slug',
					'operator' => $operator,
				),
			),
        ),
    );

    if ( isset( $ordering_args['meta_key'] ) ) {
        $args['meta_key'] = $ordering_args['meta_key'];
    }

    ob_start();

    $args     = apply_filters_deprecated( 'wcv_shortcode_products_query', array( $args, $atts ), '2.3.0', 'wcvendors_shortcode_products_query' );
    $products = new WP_Query( apply_filters( 'wcvendors_shortcode_products_query', $args, $atts ) );

    $woocommerce_loop['columns'] = esc_attr( $columns );

    if ( $products->have_posts() ) :
        ?>

        <?php woocommerce_product_loop_start(); ?>

        <?php
        while ( $products->have_posts() ) :
        $products->the_post();
        ?>

        <?php wc_get_template_part( 'content', 'product' ); ?>

        <?php
        endwhile; // end of the loop.
        ?>

        <?php woocommerce_product_loop_end(); ?>

        <?php
    endif;

    wc_reset_loop();
    wp_reset_postdata();

    $return = '<div class="woocommerce columns-' . esc_attr( $columns ) . '">' . ob_get_clean() . '</div>';

    // Remove ordering query arguments.
    WC()->query->remove_ordering_args();

    return $return;
    }

    /**
     * Get vendors with products
     *
     * Get vendors with public or private products.
     *
     * @param object $query The query object.
     */
    public function vendors_with_products( $query ) {

    global $wpdb;

    if ( isset( $query->query_vars['query_id'] ) && 'vendors_with_products' === $query->query_vars['query_id'] ) {
        $query->query_from  = $query->query_from . ' LEFT OUTER JOIN (
                  SELECT post_author, COUNT(*) as post_count
                  FROM ' . $wpdb->prefix . 'posts
                  WHERE post_type = "product" AND (post_status = "publish" OR post_status = "private")
                  GROUP BY post_author
              ) p ON (' . $wpdb->prefix . 'users.ID = p.post_author)';
        $query->query_where = $query->query_where . ' AND post_count  > 0 ';
    }
    }

    /**
     * List of vendors
     *
     * @param array $atts shortcode attributes.
     * @version 2.5 Fix pagination conflict with display mode
     * @return string
     */
    public function wcv_vendorslist( $atts ) {

        // phpcs:disable
        $html                     = '';
        $list_display_type_option = get_option( 'wcvendors_display_vendors_list_type', 'grid' );
        $list_display_type        = isset( $_GET['display_mode'] ) ? sanitize_text_field( $_GET['display_mode'] ) : $list_display_type_option;
        $search_term              = isset( $_GET['search'] ) ? sanitize_text_field( $_GET['search'] ) : '';
        // phpcs:enable
        if ( isset( $atts['show_products'] ) ) {
            wc_deprecated_argument(
                'wcv_vendorslist',
                '2.1.17',
                sprintf(
                // translators: %s: version number.
                    __(
                        'The "show_products" argument will be removed in version %s. Please update to use "has_products".',
                        'wcvendors-pro'
                    ),
                    '3.0.0'
                )
            );
            $atts['has_products'] = $atts['show_products'];
            unset( $atts['show_products'] );
        }

        extract( // phpcs:ignore
            shortcode_atts(
                array(
                    'orderby'      => 'registered',
                    'order'        => 'ASC',
                    'per_page'     => '12',
                    'columns'      => '4',
                    'has_products' => 'no',
                ),
                $atts
            )
        );
        $meta_query = array(
            'relation' => 'AND',
            array(
                'key'     => 'pv_shop_slug',
                'value'   => '',
                'compare' => '>',
            ),
        );
        if ( $search_term ) {
            $meta_query[] = array(
                array(
                    'key'     => 'pv_shop_name',
                    'value'   => $search_term,
                    'compare' => 'LIKE',
                ),
            );
        }

        $paged  = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
        $offset = ( $paged - 1 ) * $per_page;

        // Hook into the user query to modify the query to return users that have at least one product.
        if ( 'yes' === $has_products ) {
            add_action( 'pre_user_query', array( $this, 'vendors_with_products' ) );
        }

        // Get all vendors.
        $vendor_total_args = array(
            'role'       => 'vendor',
            'meta_query' => $meta_query,
            'orderby'    => $orderby,
            'order'      => $order,
        );

        if ( 'yes' === $has_products ) {
            $vendor_total_args['query_id'] = 'vendors_with_products';
        }

        $vendor_query = new WP_User_Query( $vendor_total_args );
        $all_vendors  = $vendor_query->get_results();

        // Get the paged vendors.
        $vendor_paged_args = array(
            'role'       => 'vendor',
            'meta_query' => $meta_query,
            'orderby'    => $orderby,
            'order'      => $order,
            'offset'     => $offset,
            'number'     => $per_page,
            'fields'     => 'all_with_meta',
        );

        if ( 'yes' === $has_products ) {
            $vendor_paged_args['query_id'] = 'vendors_with_products';
        }

        $vendor_paged_query = new WP_User_Query( $vendor_paged_args );
        $paged_vendors      = $vendor_paged_query->get_results();

        // Pagination calcs.
        $total_vendors       = count( $all_vendors );
        $total_vendors_paged = count( $paged_vendors );
        $total_pages         = ceil( $total_vendors / $per_page );

        ob_start();
        wc_get_template(
            'vendor-list.php',
            array(
                'display_mode'  => $list_display_type,
                'search_term'   => $search_term,
                'vendors_count' => $total_vendors_paged,
                'vendors_list'  => $paged_vendors,

            ),
            'wc-vendors/front/',
            WCV_PLUGIN_DIR . 'templates/front/'
        );

        $html .= ob_get_clean();

        if ( $total_vendors > $total_vendors_paged ) {
            $html        .= '<div class="woocommerce-pagination">';
            $current_page = max( 1, get_query_var( 'paged' ) );
            $big          = 999999999; // need an unlikely integer.
            $html        .= paginate_links(
                array(
                    'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                    'format'    => 'page/%#%/',
                    'current'   => $current_page,
                    'total'     => $total_pages,
                    'prev_next' => false,
                    'type'      => 'list',
                )
            );
            $html        .= '</div>';
        }

        return $html;
    }


    /**
     * Output a sold by link
     *
     * @param string|array $atts The shortcode attributes.
     *
     * @return string|void
     */
    public function wcv_sold_by( $atts ) {

        if ( ! is_product() ) {
            return;
        }

        $atts = shortcode_atts(
            array(
                'vendor_id'     => get_the_author_meta( 'ID' ),
            'sold_by_label'     => __( get_option( 'wcvendors_label_sold_by' ), 'wc-vendors' ), // phpcs:ignore
            'sold_by_separator' => __( get_option( 'wcvendors_label_sold_by_separator' ), 'wc-vendors' ), // phpcs:ignore
            ),
            $atts,
            'wcv_sold_by'
        );
        extract( $atts ); // phpcs:ignore 

        $sold_by = wcv_get_sold_by_link( $vendor_id, 'wcvendors_cart_sold_by_meta' );

        $cart_sold_by_label = apply_filters(
            'wcvendors_cart_sold_by_meta',
            $sold_by_label,
            get_the_ID(),
            $vendor_id
        );

        $sold_by_meta_separator = apply_filters(
            'wcvendors_cart_sold_by_meta_separator',
            $sold_by_separator,
            get_the_ID(),
            $vendor_id
        );

        return $cart_sold_by_label . '&nbsp;' . $sold_by_meta_separator . '&nbsp;' . $sold_by . '<br/>';
    }
}
