<?php
namespace ElementsKit\Modules\Copy_Paste_Cross_Domain;

defined('ABSPATH') || exit;

/**
 * Class: Initiate Copy-Paste-Cross-Domain module
 */
class Init {
    
    private $dir;
    private $url;

    public function __construct() {

        // get current directory path
        $this->dir = dirname(__FILE__) . '/';

        // get current module's url
        $this->url = \ElementsKit::plugin_url() . 'modules/copy-paste-cross-domain/';

        // enqueue styles and scripts
        add_action('elementor/editor/after_enqueue_scripts', [$this, 'editor_scripts']);

        // include all necessary files
        $this->include_files();

    }

    /**
     * Include necessary classes for the module
     *
     * @return void
     */
    public function include_files() {
        include $this->dir . 'ajax.php';

        new \Elementskit_Copy_Paste_Ajax();
    }

    /**
     * Enqueue and add javascript codes for this module
     *
     * @return void
     */
    public function editor_scripts() {

        if (is_admin()) {

            wp_enqueue_script('ekit-xd-copy-paste-editor-defer', $this->url . 'assets/js/ekit-copy-paste.js', ['elementor-editor'], \ElementsKit::version(), true);

            wp_localize_script('ekit-xd-copy-paste-editor-defer', 'ekit_cp_xd', [
                'ajaxurl'  => admin_url('admin-ajax.php'),
                'adminurl' => admin_url('admin.php'),
                'message'  => [
                    'copy'        => esc_html__('Element copied successfully!', 'elementskit'),
                    'import_wait' => esc_html__('Processing media import! Please wait', 'elementskit'),
                    'paste'       => esc_html__('Element pasted successfully!', 'elementskit'),
                    'error'       => esc_html__('Something went wrong!', 'elementskit'),
                    'empty_copy'  => esc_html__('No copied element found!', 'elementskit'),
                    'storage_key' => 'ekit-cross-domain-key',
                ],
            ]);

        }

    }
}
