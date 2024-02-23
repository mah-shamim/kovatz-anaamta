<?php

namespace ElementsKit\Modules\Google_Sheet_Elementor_Pro_Form;

use Elementor\Controls_Manager;

defined('ABSPATH') || exit;

class Init
{
    /**
     * @var mixed
     */
    public $admin_settings;

    public function __construct()
    {
        if (!class_exists('\ElementorPro\Core\App\App')) {
            return;
        }

        $this->admin_settings = \ElementsKit_Lite\Libs\Framework\Classes\Utils::instance()->get_option('user_data', []);

        add_action('elementskit/admin/settings_sections/before', [$this, 'google_sheet_access_token']);

        add_action('elementor/element/form/section_form_options/after_section_end', [$this, 'form_control']);

        add_action('elementor_pro/forms/new_record', [$this, 'new_record'], 10, 2);
    }

    public function google_sheet_access_token()
    {
        if (!empty($_GET['code'])) {
            $this->get_access_token();
        }
    }

    /**
     * @param $settings
     * @return mixed
     */
    public function form_control($settings)
    {
        $settings->start_controls_section(
            'ekit_section_form_fields',
            [
                'label' => esc_html__('Google Sheet', 'elementskit')
            ]
        );

        $settings->add_control(
            'ekit_google_sheet_enable',
            [
                'label'        => esc_html__('Enable', 'elementskit'),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__('Yes', 'elementskit'),
                'label_off'    => esc_html__('No', 'elementskit'),
                'return_value' => 'yes',
                'default'      => 'no'
            ]
        );

        $settings->add_control(
            'ekit_google_sheet_name',
            [
                'label'       => esc_html__('Name', 'elementskit'),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => esc_html__('Enter your sheet name', 'elementskit'),
                'description' => esc_html__('Note: A google sheet will be generated with the given name and submissions will be stored there.', 'elementskit'),
                'condition'   => [
                    'ekit_google_sheet_enable' => 'yes'
                ],
                'label_block' => true
            ]
        );

        $settings->add_control(
            'ekit_google_sheet_help_text',
            [
                'type'            => Controls_Manager::RAW_HTML,
                'raw'             => sprintf(esc_html__('Google sheet %s', 'elementskit'), '<a href="' . admin_url('admin.php?page=elementskit#v-elementskit-usersettings') . '" target="blank">Settings</a>'),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                'condition'       => [
                    'ekit_google_sheet_enable' => 'yes'
                ]
            ]
        );

        $settings->end_controls_section();
        return $settings;

    }

    /**
     * @param $record
     * @param $form
     */
    public function new_record($record, $form)
    {
        $form_settings = $form->get_current_form()['settings'];
        if ($form_settings['ekit_google_sheet_enable'] == 'yes') {
            if (!empty($this->admin_settings['google']['client_id']) && !empty($this->admin_settings['google']['client_secret'])) {

                $google_sheet = new Google_Sheet([
                    'id'   => $form_settings['id'],
                    'name' => empty($form_settings['ekit_google_sheet_name']) ? $form_settings['form_name'] : $form_settings['ekit_google_sheet_name'],
                    'data' => $record->get_formatted_data()
                ], $this->admin_settings);

                $google_sheet->insert();
            }
        }
    }

    private function get_access_token()
    {
        $code = $_GET['code'];

        $url = 'https://accounts.google.com/o/oauth2/token';

        $params = [
            "code"          => $code,
            "client_id"     => (!isset($this->admin_settings['google']['client_id'])) ? '' : ($this->admin_settings['google']['client_id']),
            "client_secret" => (!isset($this->admin_settings['google']['client_secret'])) ? '' : ($this->admin_settings['google']['client_secret']),
            "redirect_uri"  => admin_url('admin.php?page=elementskit'),
            "grant_type"    => "authorization_code",
            "access_type"   => "offline"
        ];

        $response = wp_remote_post($url, [
            'method' => 'POST',
            'body'   => $params
        ]);

        $response_body        = wp_remote_retrieve_body($response);
        $response_body_decode = json_decode($response_body, true);

        if (!empty($response_body_decode['access_token'])) {
            $response_body_decode['generated_at'] = time();
            update_option(Google_Sheet::ACCESS_TOKEN_KEY, json_encode($response_body_decode));
        }
        wp_redirect(admin_url('admin.php?page=elementskit#v-elementskit-usersettings'));
        exit;
    }

    /**
     * @return mixed
     */
    public static function get_code_url()
    {
        $user_data = \ElementsKit_Lite\Libs\Framework\Classes\Utils::instance()->get_option('user_data', []);
        $url       = "https://accounts.google.com/o/oauth2/auth";
        $params    = [
            "response_type"   => "code",
            "client_id"       => (!isset($user_data['google']['client_id'])) ? '' : ($user_data['google']['client_id']),
            "redirect_uri"    => admin_url('admin.php?page=elementskit'),
            'scope'           => 'https://www.googleapis.com/auth/spreadsheets',
            'approval_prompt' => 'force',
            'access_type'     => 'offline'
        ];
        $request_to = $url . '?' . http_build_query($params);
        return $request_to;
    }
}
