<?php

namespace ElementsKit\Modules\Facebook_Messenger;

use ElementsKit_Lite\Libs\Framework\Attr;

class Init {

	/**
	 * @var string - current directory path
	 */
	private $dir;

	/**
	 * @var string - current module's url
	 */
	private $url;


	public function __construct() {


		$this->dir = dirname(__FILE__) . '/';

		$this->url = \ElementsKit::plugin_url() . 'modules/sticky-content/';


		/**
		 * action hooks
		 */
		add_action('wp_footer', [$this, 'load_modules_script'], 100);

	}


	public function load_modules_script() {

		$data = Attr::instance()->utils->get_option('user_data', []);

		if(empty($data['fbm_module']['pg_id'])) {

			return;
		}

		$color          = empty($data['fbm_module']['txt_color']) ? '#3b5998' : $data['fbm_module']['txt_color'];
		$l_in_greeting  = empty($data['fbm_module']['l_in']) ? 'Hi! user' : $data['fbm_module']['l_in'];
		$l_out_greeting = empty($data['fbm_module']['l_out']) ? 'Hi! guest' : $data['fbm_module']['l_out'];

		$lang = get_locale();
		$lang = empty($lang) ? 'en_US' : $lang;
		?>

        <!-- Load Facebook SDK for JavaScript -->
        <div id="fb-root"></div>
        <script>
            window.fbAsyncInit = function () {
                FB.init({
                    xfbml: true,
                    version: 'v8.0'
                });
            };

            (function (d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if(d.getElementById(id)) return;
                js = d.createElement(s);
                js.id = id;
                js.src = 'https://connect.facebook.net/<?php echo $lang ?>/sdk/xfbml.customerchat.js';
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));</script>

        <!-- Your Chat Plugin code -->
        <div class="fb-customerchat"
             data-attribution="setup_tool"
             data-page_id="<?php echo intval($data['fbm_module']['pg_id']) ?>"
             data-theme_color="<?php echo esc_attr($color) ?>"
             data-logged_in_greeting="<?php echo esc_attr($l_in_greeting) ?>"
             data-logged_out_greeting="<?php echo esc_attr($l_out_greeting) ?>"
			 data-greeting_dialog_display="<?php echo isset($data['fbm_module']['is_open']) ? 'show' : 'hide'; ?>">
        </div>

		<?php
	}
}