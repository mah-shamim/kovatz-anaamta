<?php
namespace Elementor;

defined( 'ABSPATH' ) || exit;

class ElementsKit_Particles {
    private $url   = '';

    public function __construct() {

        // get current module's url
		$this->url = \ElementsKit::plugin_url() . 'modules/particles/';

		add_action( 'elementor/element/section/section_advanced/after_section_end', [$this, 'register_particles_controls'] );
		add_action( 'elementor/frontend/before_render', [ $this, 'section_before_render' ], 1 );

        // flex container support
        add_action( 'elementor/element/container/section_layout/after_section_end', [ $this, 'register_particles_controls' ] );    
	}

    public function register_particles_controls($element) {
        $element->start_controls_section(
            'ekit_particles_section',
            [
                'tab' => Controls_Manager::TAB_ADVANCED,
                'label' => esc_html__( 'Elementskit Particles', 'elementskit' ),
            ]
        );

        $element->add_control(
            'ekit_particles_enable',
            [
                'label' => esc_html__( 'Enable Particles', 'elementskit' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'elementskit' ),
                'label_off' => esc_html__( 'No', 'elementskit' ),
                'return_value' => 'yes'
            ]
        );

        $element->add_control(
			'ekit_particles_options',
			[
				'label' => esc_html__( 'Choose Format', 'elementskit' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'preset',
				'options' => [
					'preset' => esc_html__( 'Preset', 'elementskit' ),
					'file' => esc_html__( 'Upload File', 'elementskit' ),
					'json' => esc_html__( 'Enter Json Code', 'elementskit' ),
				],
                'condition' => [
                    'ekit_particles_enable' => 'yes',
                ]
			]
		);

        $element->add_control(
			'ekit_particles_preset',
			[
				'label' => esc_html__( 'Preset Style', 'elementskit' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => esc_html__( 'Default', 'elementskit' ),
					'nasa' => esc_html__( 'Nasa', 'elementskit' ),
					'bubble' => esc_html__( 'Bubble', 'elementskit' ),
					'snow' => esc_html__( 'Snow', 'elementskit' ),
					'nayan' => esc_html__( 'Nayan', 'elementskit' ),
				],
                'condition' => [
                    'ekit_particles_enable' => 'yes',
                    'ekit_particles_options' => 'preset'
                ]
			]
		);

        $element->add_control(
            'ekit_particles_json',
            [
                'label' => esc_html__( 'Particle JSON', 'elementskit' ),
                'type' => Controls_Manager::TEXTAREA,
                'rows' => 10,
				'description' => __( 'Put the particles JSON code by generating from <a href="https://vincentgarreau.com/particles.js/" target="_blank">Here!</a>', 'elementskit' ),
                'placeholder' => esc_html__( 'Type your description here', 'elementskit' ),
                'condition' => [
                    'ekit_particles_enable' => 'yes',
                    'ekit_particles_options' => 'json'
                ]
            ]
        );

        $element->add_control(
            'ekit_particles_file',
            [
                'label' => esc_html__( 'Upload Json File', 'elementskit' ),
				'type' => Controls_Manager::MEDIA,
                'media_types' =>  ['application/json'],
				'description' => __( 'Generate & Download particles JSON file from <a href="https://vincentgarreau.com/particles.js/" target="_blank">Here!</a>', 'elementskit' ),
                'condition' => [
                    'ekit_particles_enable' => 'yes',
                    'ekit_particles_options' => 'file'
                ]
            ]
        );

        $element->add_control(
            'ekit_particles_z_index',
            [
                'label' => esc_html__('Z-index', 'elementskit'),
                'type' => Controls_Manager::NUMBER,
                'default' => 0,
                'condition' => [
                    'ekit_particles_enable' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}}.ekit-particles .ekit-particles-wrapper' => 'z-index: {{VALUE}};',
                ]
            ]
        );

        $element->add_control(
			'ekit_particles_json_url',
			[
				'label' => esc_html__( 'Particles Module JSON URL', 'elementskit' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => $this->url . 'assets/json/',
			]
		);

        $element->end_controls_section();
    }

    public function section_before_render($element) {
        if ('section' === $element->get_name() || 'container' === $element->get_name()) {
            $settings = $element->get_settings_for_display();
            extract($settings);

            if ($ekit_particles_enable == 'yes') {      
                $element->add_render_attribute( '_wrapper', ['data-ekit-particles-enable' => $ekit_particles_enable, 'class' => 'ekit-particles'] );

                if ($ekit_particles_options == 'json' && !empty($ekit_particles_json)) {
                    $element->add_render_attribute( '_wrapper',
                        [
                            'data-ekit-particles' => preg_replace('/\s*/m', '', $ekit_particles_json),
                            'data-ekit-particles-type' => 'json',
                        ]
                    );
                } elseif ($ekit_particles_options == 'file' && isset($ekit_particles_file['url'])) {
                    $element->add_render_attribute( '_wrapper',
                        [
                            'data-ekit-particles' => $ekit_particles_file['url'],
                            'data-ekit-particles-type' => 'file',
                        ]
                    );
                } elseif ($ekit_particles_options == 'preset') {
                    $element->add_render_attribute( '_wrapper',
                        [
                            'data-ekit-particles' => $this->url . 'assets/json/' . $ekit_particles_preset . '.json',
                            'data-ekit-particles-type' => 'preset',
                        ]
                    );
                }
            }
        }
    }
}