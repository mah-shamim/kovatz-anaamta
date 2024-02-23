<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Motion_Text_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class ElementsKit_Widget_Motion_Text extends Widget_Base {
	use \ElementsKit_Lite\Widgets\Widget_Notice;

    public $base;

	public function get_name() {
        return Handler::get_name();
    }

    public function get_title() {
        return Handler::get_title();
    }

    public function get_icon() {
        return Handler::get_icon();
    }

    public function get_categories() {
        return Handler::get_categories();
    }

	public function get_keywords() {
		return Handler::get_keywords();
	}

    public function get_help_url() {
        return 'https://wpmet.com/doc/motion-text/';
    }

	protected function register_controls() {
		$this->start_controls_section(
			'ekit_motion_text_content_tab',
			[
				'label' => esc_html__( 'Content', 'elementskit' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'ekit_motion_text_content_text',
			[
				'label' => esc_html__( 'Title', 'elementskit' ),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 5,
				'default' => esc_html__( 'Default description', 'elementskit' ),
				'placeholder' => esc_html__( 'Type your title here', 'elementskit' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'ekit_motion_text_sub_title_tag',
			[
				'label' => esc_html__( 'Title HTML Tag', 'elementskit' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h2',
			]
		);

		$this->add_control(
			'ekit_motion_text_sub_title_website_link',
			[
				'label' => esc_html__( 'Link', 'elementskit' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://wpmet.com', 'elementskit' ),
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_responsive_control(
			'ekit_motion_text_sub_title_text_align',
			[
				'label' => esc_html__( 'Alignment', 'elementskit' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'elementskit' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementskit' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'elementskit' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .elementskit_motion_text_wraper' => 'text-align: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'ekit_motion_enable_switch',
			[
				'label' => esc_html__( 'Enable Animation', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit' ),
				'label_off' => esc_html__( 'Hide', 'elementskit' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_control(
			'ekit_motion_text_motions',
			[
				'label' => esc_html__( 'Motion Animation', 'elementskit' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' => 'None',
					'RevealInTop' => 'Reveal In Top',
					'RevealInBottom' => 'Reveal In Bottom',
					'RevealInLeft' => 'Reveal In Left',
					'RevealInRight' => 'Reveal In Right',
					'RainDrop' => 'Rain Drop',
					'lightning' => 'Lightning',
					'JoltZoom' => 'Jolt Zoom',
					'Magnify' => 'Magnify',
					'Beat' => 'Beat',
					'FadeIn' => 'Fade In',
					'FadeInLeft' => 'Fade In Left',
					'FadeInRight' => 'Fade In Right',
					'FadeInTop' => 'Fade In Up',
					'FadeInBottom' => 'Fade In Down',
					'oaoRotateIn' => 'One after One Rotate In',
					'oaoRotateXIn' => 'One after One Rotate X In',
					'oaoRotateYIn' => 'One after One Rotate Y In'
				],
				'default' => 'none',
				'condition' => [
					'ekit_motion_enable_switch' => 'yes'
				]
			]
		);

		$this->add_control(
			'ekit_motion_text_motions_spilit',
			[
				'label' => esc_html__( 'Spilit Text Animation', 'elementskit' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'no_spilit',
				'options' => [
					'no_spilit'  => esc_html__( 'No Spilit', 'elementskit' ),
					'char_based' => esc_html__( 'Letter Based', 'elementskit' ),
				],
				'condition' => [
					'ekit_motion_text_motions!' => ['RevealInTop', 'RevealInBottom', 'RevealInLeft', 'RevealInRight', 'oaoFadeIn', 'oaoFadeOut', 'oaoFlyIn', 'oaoFlyOut', 'oaoRotateIn', 'oaoRotateOut', 'oaoRotateXIn', 'oaoRotateXOut', 'oaoRotateYIn', 'oaoRotateYOut', 'none'],
					'ekit_motion_enable_switch' => 'yes'
				]
			]
		);

		$this->add_control(
			'ekit_motion_text_animation_duration_char_based',
			[
				'label' => esc_html__( 'Animation Duration By Charecter (in s)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => .5,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit_animation.ekit_char_based .ekit-letter' => 'animation-duration: {{SIZE}}s;',
				],
				'condition' => [
					'ekit_motion_text_motions_spilit' => 'char_based',
					'ekit_motion_text_motions!' => ['RevealInTop', 'RevealInBottom', 'RevealInLeft', 'RevealInRight', 'oaoFadeIn', 'oaoFadeOut', 'oaoFlyIn', 'oaoFlyOut', 'oaoRotateIn', 'oaoRotateOut', 'oaoRotateXIn', 'oaoRotateXOut', 'oaoRotateYIn', 'oaoRotateYOut', 'none'],
					'ekit_motion_enable_switch' => 'yes'
				]
			]
		);

		$this->add_control(
			'ekit_motion_text_animation_delay_char_based',
			[
				'label' => esc_html__( 'Animation Delay By Charecter (in ms)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'condition' => [
					'ekit_motion_text_motions_spilit' => 'char_based',
					'ekit_motion_text_motions!' => ['RevealInTop', 'RevealInBottom', 'RevealInLeft', 'RevealInRight', 'oaoFadeIn', 'oaoFadeOut', 'oaoFlyIn', 'oaoFlyOut', 'oaoRotateIn', 'oaoRotateOut', 'oaoRotateXIn', 'oaoRotateXOut', 'oaoRotateYIn', 'oaoRotateYOut', 'none'],
					'ekit_motion_enable_switch' => 'yes'
				]
			]
		);

		$this->add_control(
			'ekit_motion_text_animation_duration_no_spilit',
			[
				'label' => esc_html__( 'Animation Duration (in s)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => .5,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit_animation' => 'animation-duration: {{SIZE}}s;',
				],
				'condition' => [
					'ekit_motion_text_motions_spilit' => 'no_spilit',
					'ekit_motion_text_motions!' => ['RevealInTop', 'RevealInBottom', 'RevealInLeft', 'RevealInRight', 'none'],
					'ekit_motion_enable_switch' => 'yes'
				]
			]
		);

		$this->add_control(
			'ekit_motion_text_animation_duration_reveal',
			[
				'label' => esc_html__( 'Animation Duration Reveal (in s)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => .5,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .background_reveal_anim .elementkit_background_reveal_bg' => 'animation-duration: {{SIZE}}s;',
				],
				'condition' => [
					'ekit_motion_text_motions' => ['RevealInTop', 'RevealInBottom', 'RevealInLeft', 'RevealInRight',],
					'ekit_motion_enable_switch' => 'yes'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_motion_text_style_tab',
			[
				'label' => esc_html__( 'Style', 'elementskit' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ekit_motion_text_style_title_color',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .ekit_motion_text_title' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit_motion_text_title > a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_motion_text_style_title_content_typography',
				'label' => esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit_motion_text_title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'ekit_motion_text_style_title_text_shadow',
				'label' => esc_html__( 'Text Shadow', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit_motion_text_title',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_motion_reveal_background',
				'label' => esc_html__( 'Background', 'elementskit' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [
					'image'
				],
				'selector' => '{{WRAPPER}} .background_reveal_anim .elementkit_background_reveal_bg',
				'condition' => [
					'ekit_motion_text_motions' => ['RevealInTop', 'RevealInBottom', 'RevealInLeft', 'RevealInRight',],
					'ekit_motion_enable_switch' => 'yes'
				]
			]
		);

		$this->end_controls_section();

		$this->insert_pro_message();
	}

	protected function render( ) {
        echo '<div class="ekit-wid-con" >';
            $this->render_raw();
        echo '</div>';
    }

    protected function render_raw( ) {
		$settings = $this->get_settings();

		// Sanitize Title Tag
		$options_ekit_motion_text_sub_title_tag = array_keys([
			'h1' => 'H1',
			'h2' => 'H2',
			'h3' => 'H3',
			'h4' => 'H4',
			'h5' => 'H5',
			'h6' => 'H6',
			'div' => 'div',
			'span' => 'span',
			'p' => 'p',
		]);
		$title_tag = \ElementsKit_Lite\Utils::esc_options($settings['ekit_motion_text_sub_title_tag'], $options_ekit_motion_text_sub_title_tag, 'h2');

		$title_text = $settings['ekit_motion_text_content_text'];
		$url = $settings['ekit_motion_text_sub_title_website_link']['url'];
		if ( ! empty( $settings['ekit_motion_text_sub_title_website_link']['url'] ) ) {
			$this->add_link_attributes( 'button', $settings['ekit_motion_text_sub_title_website_link'] );
		}
		// motion main class
		$motion_main_class = '';
		// anim class
		$anim_motion_class = '';
		// reveal anim skiping
		$reveal_anim_skip = '/^Reveal/';
		$background_reveal_wraper_class = '';
		// text spilit or not class add
		$ekit_text_spilit_class = '';
		// one by one anim skiping
		$onebyone_anim_skip = '/^oao/';
		$ekit_text_spilit_one_by_one_class = '';
		if ($settings['ekit_motion_enable_switch'] == 'yes' && $settings['ekit_motion_text_motions'] != 'none') {
			// declear anim motion class
			$anim_motion_class = 'ekit-'.$settings['ekit_motion_text_motions'];
			// declear motion main class
			$motion_main_class = 'ekit_animation';
			// reveal anim skip
			if (preg_match($reveal_anim_skip, $settings['ekit_motion_text_motions'])) {
				$background_reveal_wraper_class = 'background_reveal_anim';
			}
			// reveal anim match
			if (!(preg_match($reveal_anim_skip, $settings['ekit_motion_text_motions']))) {
				$ekit_text_spilit_class = 'ekit_'.$settings['ekit_motion_text_motions_spilit'];
			}
			// one by one preg match
			if(preg_match($onebyone_anim_skip, $settings['ekit_motion_text_motions'])){
				$ekit_text_spilit_one_by_one_class = 'ekit_char_based';
			}
		}
		$animation_delay_char_based = '';
		if (($settings['ekit_motion_text_motions_spilit'] == 'char_based') && $settings['ekit_motion_enable_switch'] == 'yes' && $settings['ekit_motion_text_motions'] != 'none') {
			$animation_delay_char_based = $settings['ekit_motion_text_animation_delay_char_based']['size'];
		}
		?>
		<div class="elementskit_motion_text_wraper">
			<?php if($title_text != '') : ?>
			<div class="ekit_motion_text_inner_wraper <?php echo esc_attr($background_reveal_wraper_class); ?>" >
			<?php
				echo '<'. $title_tag .' class="ekit_motion_text_title '.$motion_main_class.'  '.$ekit_text_spilit_class.' '.$ekit_text_spilit_one_by_one_class.'" data-ekit-animation-delay-s="10" data-animate-class="'. esc_attr( $anim_motion_class ) .'">';
				if ($url !== '') {
					echo '<a '. $this->get_render_attribute_string( 'button' ) .'><span class="ekit_motion_text" data-ekit-animation-delay="'.esc_attr($animation_delay_char_based).'">'.\ElementsKit_Lite\Utils::kses( $title_text ).'</span></a>';
				} else {
					echo '<span class="ekit_motion_text" data-ekit-animation-delay="'.esc_attr($animation_delay_char_based).'">'.\ElementsKit_Lite\Utils::kses( $title_text ).'</span>';
				} ?>
				<?php echo '</'. $title_tag .'>';
			?>
			<?php if(preg_match($reveal_anim_skip, $settings['ekit_motion_text_motions'])) : ?>
			<div class="elementkit_background_reveal_bg"></div>
			<?php endif;?>
			<?php endif; ?>
			</div>
		</div>

		<?php
	}
}
