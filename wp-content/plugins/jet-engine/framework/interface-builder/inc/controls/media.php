<?php
/**
 * Class for the building ui-media elements.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'CX_Control_Media' ) ) {

	/**
	 * Class for the building CX_Control_Media elements.
	 */
	class CX_Control_Media extends CX_Controls_Base {

		/**
		 * Default settings.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		public $defaults_settings = array(
			'id'                 => 'cx-ui-media-id',
			'name'               => 'cx-ui-media-name',
			'value'              => '',
			'multi_upload'       => true,
			'library_type'       => '', // image, video, sound
			'upload_button_text' => 'Choose Media',
			'required'           => false,
			'label'              => '',
			'class'              => '',
			'value_format'       => 'id', // id, url, both
		);

		/**
		 * Register control dependencies
		 *
		 * @return [type] [description]
		 */
		public function register_depends() {
			wp_enqueue_media();
		}

		/**
		 * Retrun scripts dependencies list for current control.
		 *
		 * @return array
		 */
		public function get_script_depends() {
			return array( 'jquery-ui-sortable' );
		}

		/**
		 * Render html CX_Control_Media.
		 *
		 * @since 1.0.0
		 */
		public function render() {

			$html = '';

			if ( ! current_user_can( 'upload_files' ) ) {
				return $html;
			}

			$class = implode( ' ',
				array(
					$this->settings['class'],
				)
			);

			$html .= '<div class="cx-ui-container ' . esc_attr( $class ) . '">';
				if ( '' != $this->settings['value'] ) {

					if ( is_array( $this->settings['value'] ) ) {
						$medias = $this->settings['value'];

						if ( 'both' === $this->settings['value_format'] ) {
							$this->settings['value'] = json_encode( $this->settings['value'] );
						} else {
							$this->settings['value'] = join( ',', $this->settings['value'] );
						}

					} else {

						if ( 'both' === $this->settings['value_format'] ) {
							$medias = json_decode( $this->settings['value'], true );
						} else {
							$this->settings['value'] = str_replace( ' ', '', $this->settings['value'] );
							$medias                  = explode( ',', $this->settings['value'] );
						}
					}

					if ( 'both' === $this->settings['value_format'] ) {
						$medias = isset( $medias['id'] ) ? array( $medias ) : $medias;
					}

				} else {
					$this->settings['value'] = '';
					$medias                  = array();
				}

					$media_ids = array();

					if ( '' !== $this->settings['label'] ) {
						$html .= '<label class="cx-label" for="' . esc_attr( $this->settings['id'] ) . '">' . wp_kses_post( $this->settings['label'] ) . '</label> ';
					}

					$html .= '<div class="cx-ui-media-wrap">';
						$html .= '<div  class="cx-upload-preview" >';
						$html .= '<div class="cx-all-images-wrap">';

							if ( is_array( $medias ) && ! empty( $medias ) ) {

								foreach ( $medias as $medias_key => $medias_value ) {

									switch ( $this->settings['value_format'] ) {
										case 'url':
											$media_url = $medias_value;
											$media_id  = attachment_url_to_postid( $media_url );

											break;

										case 'both':
											$media_id  = $medias_value['id'];
											$media_url = $medias_value['url'];

											break;

										default:
											$media_id  = $medias_value;
											$media_url = wp_get_attachment_url( $media_id );
									}

									$media_ids[] = $media_id;
									$media_title = get_the_title( $media_id );
									$mime_type   = get_post_mime_type( $media_id );
									$thumb       = '';
									$thumb_type  = 'icon';

									switch ( $mime_type ) {
										case 'image/jpeg':
										case 'image/png':
										case 'image/gif':
										case 'image/svg+xml':
										case 'image/webp':
											$img_src    = wp_get_attachment_image_src( $media_id, 'thumbnail' );
											$img_src    = $img_src[0];
											$thumb      = '<img src="' . esc_html( $img_src ) . '" alt="">';
											$thumb_type = 'image';
											break;

										case 'application/pdf':
											$thumb = '<span class="dashicons dashicons-media-document"></span>';
											break;

										case 'image/x-icon':
											$thumb = '<span class="dashicons dashicons-format-image"></span>';
											break;

										case 'video/mpeg':
										case 'video/mp4':
										case 'video/quicktime':
										case 'video/webm':
										case 'video/ogg':
											$thumb = '<span class="dashicons dashicons-format-video"></span>';
											break;

										case 'audio/mpeg':
										case 'audio/wav':
										case 'audio/ogg':
											$thumb = '<span class="dashicons dashicons-format-audio"></span>';
											break;
									}
									$html .= '<div class="cx-image-wrap cx-image-wrap--' . esc_attr( $thumb_type ) . '">';
										$html .= '<div class="inner">';
											$html .= '<div class="preview-holder" data-id-attr="' . esc_attr( $media_id ) . '" data-url-attr="' . esc_attr( $media_url ) . '">';
												$html .= '<div class="centered">';
													$html .= $thumb;
												$html .= '</div>';
											$html .= '</div>';
											$html .= '<span class="title">' . $media_title . '</span>';
											$html .= '<a class="cx-remove-image" href="#" title=""><i class="dashicons dashicons-no"></i></a>';
										$html .= '</div>';
									$html .= '</div>';
								}
							}
						$html .= '</div>';
					$html .= '</div>';
					$html .= '<div class="cx-element-wrap">';
						$html .= '<input type="hidden" id="' . esc_attr( $this->settings['id'] ) . '" class="cx-upload-input" name="' . esc_attr( $this->settings['name'] ) . '" value="' . esc_html( $this->settings['value'] ) . '" data-ids-attr="' . esc_html( join( ',', $media_ids ) ) . '" ' . $this->get_required() . '>';
						$html .= '<button type="button" class="upload-button cx-upload-button button-default_" value="' . esc_attr( $this->settings['upload_button_text'] ) . '" data-title="' . esc_attr( $this->settings['upload_button_text'] ) . '" data-multi-upload="' . esc_attr( $this->settings['multi_upload'] ) . '" data-library-type="' . esc_attr( $this->settings['library_type'] ) . '" data-value-format="' . esc_attr( $this->settings['value_format'] ) . '">' . esc_attr( $this->settings['upload_button_text'] ) . '</button>';
						$html .= '<div class="clear"></div>';
					$html .= '</div>';
				$html .= '</div>';
			$html .= '</div>';

			return $html;
		}
	}
}
