<?php 
namespace ElementsKit\Modules\Header_Footer;

defined( 'ABSPATH' ) || exit;

class Cpt_Hooks {
    public static $instance = null;

    public function __construct() {

		add_action( 'admin_init', [ $this, 'add_author_support_to_column' ], 10 );
		add_filter( 'manage_elementskit_template_posts_columns', [ $this, 'set_columns' ] );
        add_action( 'manage_elementskit_template_posts_custom_column', [ $this, 'render_column' ], 10, 2 );
        add_filter( 'parse_query', [ $this, 'query_filter' ] );
    }

    public function add_author_support_to_column() {
        add_post_type_support( 'elementskit_template', 'author' ); 
    }

	/**
	 * Set custom column for template list.
	 */
	public function set_columns( $columns ) {

		$date_column = $columns['date'];
		$author_column = $columns['author'];

		unset( $columns['date'] );
		unset( $columns['author'] );

		$columns['type'] = esc_html__( 'Type', 'elementskit' );
		$columns['condition'] = esc_html__( 'Conditions', 'elementskit' );
		$columns['date']      = $date_column;
		$columns['author']      = $author_column;

		return $columns;
	}

    /**
     * Render Column
     *
     * Enqueue js and css to frontend.
     *
     * @since 1.0.0
     * @access public
     */
	public function render_column( $column, $post_id ) {
		switch ( $column ) {
            case 'type':
            
				$type = get_post_meta( $post_id, 'elementskit_template_type', true );
                $active = get_post_meta( $post_id, 'elementskit_template_activation', true );
                
                echo ucfirst($type) . (($active == 'yes') 
                ? ( '<span class="ekit-headerfooter-status ekit-headerfooter-status-active">'. esc_html__('Active', 'elementskit') .'</span>' ) 
                : ( '<span class="ekit-headerfooter-status ekit-headerfooter-status-inactive">'. esc_html__('Inactive', 'elementskit') .'</span>' ));

				break;
            case 'condition':

                $cond = [
                    'condition_a' => get_post_meta($post_id, 'elementskit_template_condition_a', true),
                    'condition_singular' => get_post_meta($post_id, 'elementskit_template_condition_singular', true),
                    'condition_singular_id' => get_post_meta($post_id, 'elementskit_template_condition_singular_id', true),
                ];

                echo ucwords( str_replace('_', ' ',
                    $cond['condition_a']  
                    . (($cond['condition_a'] == 'singular')
                        ? (($cond['condition_singular'] != '' )
                            ? (' > ' . $cond['condition_singular'] 
                            . (($cond['condition_singular_id'] != '')
                                ? ' > ' . $cond['condition_singular_id']
                                : ''))
                            : '')
                        : '')
                ));

				break;
		}
    }
    

    public function  query_filter($query) {
        global $pagenow;
        $current_page = isset( $_GET['post_type'] ) ? $_GET['post_type'] : '';

        if ( 
            is_admin() 
            && 'elementskit_template' == $current_page 
            && 'edit.php' == $pagenow   
            && isset( $_GET['elementskit_type_filter'] ) 
            && $_GET['elementskit_type_filter'] != ''
            && $_GET['elementskit_type_filter'] != 'all'
        ){
            $type = $_GET['elementskit_type_filter'];
            $query->query_vars['meta_key'] = 'elementskit_template_type';
            $query->query_vars['meta_value'] = $type;
            $query->query_vars['meta_compare'] = '=';
        }
    }


    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}