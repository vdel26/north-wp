<?php

// -----------------------------------------
// semplice
// admin/editor/modules/gallery/module.php
// -----------------------------------------

if(!class_exists('sm_gallery')) {
	class sm_gallery {

		public $output;

		// constructor
		public function __construct() {
			// define output
			$this->output = array(
				'html' => '',
				'css'  => '',
			);
		}

		// output editor
		public function output_editor($values, $id) {

			// default for editor
			$this->output['html'] = '<img class="is-content" src="' . get_template_directory_uri() . '/assets/images/admin/placeholders/gallery.png' . '" alt="gallery-placeholder">';

			// return output
			return $this->output;
		}

		// output frtonend
		public function output_frontend($values, $id) {
			
			// output
			$output = '';
			$cover = array(
				'css' 		 => '',
				'class' 	 => '',
				'object-fit' => '',
			);
			$gallery_size = 'true';

			// attributes
			extract( shortcode_atts(
				array(
					'images'				=> '',
					'width'					=> 'grid-width',
					'cover_mode'			=> 'disabled',
					'autoplay'				=> false,
					'adaptive_height'		=> 'true',
					'animation_status'		=> 'enabled',
					'animation'				=> 'sgs-crossfade',
					'timeout' 				=> 4000,
					'arrows_visibility'		=> 'true',
					'pagination_visibility'	=> 'false',
					'arrows_color'			=> '#ffffff',
					'pagination_color'		=> '#000000',
					'pagination_position'	=> 'below',
					'infinite'				=> 'false',
				), $values['options'] )
			);
			
			// autoplay?
			if($autoplay == 'true' && is_numeric($timeout)) {
				$autoplay = $timeout;
			} else {
				$autoplay = 'false';
			}

			// animation status
			if($animation_status == 'disabled') {
				$animation = 'sgs-nofade';
			}

			$images = $values['content']['xl'];
			
			if(is_array($images)) {

				// cover class and css
				if($cover_mode == 'enabled') {
					// set up cover
					$cover = array(
						'css' 		 => ' ' . $values['section_element'] . ' .row { min-height: 100vh !important; } ' . $values['section_element'] . ' .column-content { height: 100%; }',
						'class' 	 => ' sgs-cover',
						'object-fit' => ' data-object-fit="cover"',
					);
					// set gallery sizing to false
					$gallery_size = 'false';
				}

				$output .= '<div id="gallery-' . $id . '" class="is-content semplice-gallery-slider ' . $animation . ' pagination-' . $pagination_position . ' sgs-pagination-' . $pagination_visibility . $cover['class'] . '">';

				foreach($images as $image) {
				
					$img = wp_get_attachment_image_src($image, 'full');
					
					$output .= '<div class="sgs-slide ' . $width . '">';
					$output .= '<img src="' . $img[0] . '" alt="gallery-image"' . $cover['object-fit'] . ' />';
					$output .= '</div>';
				}
				
				$output .= '</div>';

				// custom css for nav and pagination
				$this->output['css'] = '#gallery-' . $id . ' .flickity-prev-next-button .arrow { fill: ' . $arrows_color . ' !important; }#gallery-' . $id . ' .flickity-page-dots .dot { background: ' . $pagination_color . ' !important; }' . $cover['css'];
				
				$output .='
					<script>
						(function($) {
							$(document).ready(function () {
								$("#gallery-' . $id . '").flickity({
									autoPlay: ' . $autoplay . ',
									adaptiveHeight: ' . $adaptive_height . ',
									prevNextButtons: ' . $arrows_visibility . ',
									pageDots: ' . $pagination_visibility . ',
									wrapAround: ' . $infinite . ',
									setGallerySize: ' . $gallery_size . ',
									percentPosition: true,
									imagesLoaded: true,
									arrowShape: { 
										x0: 10,
										x1: 60, y1: 50,
										x2: 65, y2: 45,
										x3: 20
									},
									pauseAutoPlayOnHover: false,
								});
							});
						})(jQuery);
					</script>
				';
			} else {
				$output .= '<div class="empty-gallery">Your gallery has no images yet.</div>';
			}

			// save output
			$this->output['html'] = $output;

			return $this->output;
		}
	}
	// instance
	$this->module['gallery'] = new sm_gallery;
}