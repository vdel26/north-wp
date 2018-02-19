<?php

// -----------------------------------------
// grid
// -----------------------------------------

function semplice_grid($mode) {

	// css
	$output = '';
	$gutter = 0;
	$outer_padding = 30;
	$grid_width = 1170;

	// get custom grid values
	$grid = json_decode(get_option('semplice_customize_grid'), true);

	if(is_array($grid)) {

		// outer padding
		if(isset($grid['outer_padding']) && $mode == 'frontend') {
			$outer_padding = $grid['outer_padding'];
			// outer padding only for desktop
			$output .= '
				@media screen and (min-width: 1170px) {
					.container-fluid, .container, .admin-container {
						padding: 0 ' . round($outer_padding / 18, 5) . 'rem 0 ' . round($outer_padding / 18, 5) . 'rem;
					}
				}
			';
		}

		// grid width
		if(isset($grid['width'])) {
			// is < 1170?
			if($grid['width'] < 1170) {
				$grid_width = 1170;
			} else {
				$grid_width = $grid['width'];
			}	
		}

		// css
		$output .= '.container {
			max-width: ' . ($grid_width + ($outer_padding * 2)) . 'px;
		}';
		
		// mobile gutter
		if(isset($grid['responsive_gutter']) && is_numeric($grid['responsive_gutter'])) {
			$output .= semplice_get_grid_breakpoint($grid['responsive_gutter'], $mode, false);
		}

		// xl gutter 
		if(isset($grid['gutter']) && is_numeric($grid['gutter'])) {
			$output .= semplice_get_grid_breakpoint($grid['gutter'], $mode, true);
			$gutter = $grid['gutter'];
		}		
	}

	return $output;
}

// -----------------------------------------
// grid breakpoint
// -----------------------------------------

function semplice_get_grid_breakpoint($gutter, $mode, $is_desktop) {
	// media query open
	$output = '';
	// define prefix
	$prefix = '';
	if($mode == 'editor') {
		if($is_desktop) {
			$prefix = '[data-breakpoint="xl"]';
		}
		// row
		$output .= $prefix . ' .row {
			margin-left: -' . ($gutter / 2) . 'px;
			margin-right: -' . ($gutter / 2) . 'px;
		}';
		// column
		$output .= $prefix . ' .column, .grid-column {
			padding-left: ' . ($gutter / 2) . 'px;
			padding-right: ' . ($gutter / 2) . 'px;
		}';
	} else {
		if($is_desktop) {
			$prefix = '@media screen and (min-width: 1170px) {';
		} else {
			$prefix = '@media screen and (max-width: 1169px) {';
		}

		$output .= $prefix . ' .row {
			margin-left: -' . ($gutter / 2) . 'px;
			margin-right: -' . ($gutter / 2) . 'px;
		}';
		
		$output .= '.column, .grid-column {
			padding-left: ' . ($gutter / 2) . 'px;
			padding-right: ' . ($gutter / 2) . 'px;
		}';

		// close media query
		$output .= '}';
	}

	return $output;
}

// -----------------------------------------
// masonry
// -----------------------------------------

function semplice_masonry($id, $masonry_items, $hor_gutter, $ver_gutter, $add_to_css) {

	// vars
	$masonry = '';
	$masonry_css = '';

	// masonry css
	$masonry_css = '
		<style id="' . $id . '-style" type="text/css">
			#masonry-'. $id .'{ margin: auto -' . ($hor_gutter / 2) . 'px !important; } 
			.masonry-'. $id .'-item { margin: 0px; padding-left: ' . ($hor_gutter / 2) . 'px; padding-right: ' . ($hor_gutter / 2) . 'px; padding-bottom: ' . $ver_gutter . 'px; }
			' . $add_to_css . '
		</style>
	';

	$masonry_css = str_replace(array("\r","\n", "\t"),"",$masonry_css);

	// open masonry
	$masonry .= '
		<div id="masonry-' . $id . '" class="masonry">
			<div class="masonry-item-width"></div>
			' . $masonry_items . '
		</div>
	';

	// javascript
	$masonry .= '
		<script type="text/javascript">
			(function ($) {
				$(document).ready(function () {
					// delete old css if there
					$("#' . $id . '-style").remove();
					// add css to head
					$("head").append(\'' . $masonry_css . '\');
					// define container
					var $container = $("#masonry-' . $id . '");
					// make jquery object out of items
					var $items = $(".masonry-' . $id . '-item");

					// fire masmonry
					$container.masonry({
						itemSelector: ".masonry-' . $id . '-item",
						columnWidth: ".masonry-item-width",
						transitionDuration: 0,
						isResizable: true,
						percentPosition: true,
					});

					// show images
					showImages($container, $items);

					// load images and reveal if loaded
					function showImages($container, $items) {
						// get masonry
						var msnry = $container.data("masonry");
						// get item selector
						var itemSelector = msnry.options.itemSelector;
						// append items to masonry container
						//$container.append($items);
						$items.imagesLoaded().progress(function(imgLoad, image) {
							// get item
							var $item = $(image.img).parents(itemSelector);
							// fade in item
							// layout
							msnry.layout();
							// fade in item
							$item.css("opacity", 1);	
						});
					}
				});
			})(jQuery);
		</script>
	';

	// output
	return $masonry;
}

// -----------------------------------------
// project panel grid html
// -----------------------------------------

function semplice_project_panel_html($is_frontend, $post_id) {

	// vars
	$output = '';
	$element_type = 'div';

	// get project panel
	$projectpanel = json_decode(get_option('semplice_customize_projectpanel'), true);

	// attributes
	extract(shortcode_atts(
		array(
			'visibility'				=> 'visible',
			'images_per_row'			=> 2,
			'width'						=> 'container',
			'title_visibility'			=> 'visible',
			'meta_visibility'			=> 'both',
			'panel_title_font'			=> 'regular',
			'title_font'				=> 'regular',
			'category_font'				=> 'regular',
			'panel_label'				=> 'Selected Works',
			'gutter'					=> 'yes',
		), $projectpanel)
	);

	// get portfolio order
	$portfolio_order = json_decode(get_option('semplice_portfolio_order'));

	// get projects
	$projects = semplice_get_projects($portfolio_order, false);

	// items
	$thumbs = '';

	// are there any published projects
	if(!empty($projects)) {

		// counter

		foreach ($projects as $key => $project) {		
			// masonry items open
			$thumbs .= '
					<div class="pp-thumb column" data-xl-width="' . $images_per_row . '" data-sm-width="4" data-xs-width="6">
						<a href="' . $project['permalink'] . '" title="' . $project['post_title'] . '"><img src="' . $project['pp_thumbnail']['src'] . '" width="' . $project['pp_thumbnail']['width'] . '" height="' . $project['pp_thumbnail']['height'] . '"></a>
						<p class="pp-title"><a data-font="' . $title_font . '" href="' . $project['permalink'] . '" title="' . $project['post_title'] . '">' . $project['post_title'] . '</a><span data-font="' . $category_font . '">' . $project['project_type'] . '</span></p>
					</div>
			';
		}
	} else {
		$thumbs = '<div class="empty-portfolio"><img src="' . get_template_directory_uri() . '/assets/images/admin/noposts.svg" alt="no-posts"><h3>Looks like you have an empty Portfolio. Please note that only<br />published projects are visible in the project panel.</h3></div>';
	}

	// is visible? if not return nothing
	if($visibility != 'hidden' && true === $is_frontend && get_post_type($post_id) == 'project' || false === $is_frontend) {
		// html
		return '
			<section class="project-panel" data-pp-gutter="' . $gutter . '">
				<div class="' . $width . '" data-title-visibility="' . $title_visibility . '" data-meta-visibility="' . $meta_visibility . '">
					<div class="row">
						<div class="column" data-xl-width="12">
							<p class="panel-label" data-font="' . $panel_title_font . '">' . $panel_label . '</p>
						</div>
					</div>
					<div class="row pp-thumbs">
						' . $thumbs . '
					</div>
				</div>
			</' . $element_type . '>
		';
	} else {
		return '';
	}
}

// -----------------------------------------
// project panel grid css
// -----------------------------------------

function semplice_project_panel_css($is_frontend) {

	// get project panel
	$projectpanel = json_decode(get_option('semplice_customize_projectpanel'), true);

	// attributes
	extract(shortcode_atts(
		array(
			'background'				=> '#f5f5f5',
			'panel_padding'				=> '2.5rem',
			'panel_title_color'			=> '#000000',
			'panel_title_fontsize'		=> '1.777777777777778rem',
			'panel_title_text_transform'=> 'none',
			'panel_padding_left'		=> '0rem',
			'panel_padding_bottom'		=> '1.666666666666667rem',
			'panel_text_align'			=> 'left',
			'title_padding_bottom'		=> '1.666666666666667rem',
			'title_color'				=> '#000000',
			'title_fontsize'			=> '0.7222222222222222rem',
			'title_text_transform'		=> 'none',
			'title_padding_top'			=> '0.5555555555555556rem',
			'category_color'			=> '#999999',
			'category_fontsize'			=> '0.7222222222222222rem',
			'category_text_transform'	=> 'none',
		), $projectpanel)
	);

	// return css
	return '
		.project-panel {
			background: ' . $background . ';
			padding: ' . $panel_padding . ' 0rem;
		}
		.pp-thumbs {
			margin-bottom: -' . $title_padding_bottom . ';
		}
		#content-holder .panel-label, .projectpanel-preview .panel-label {
			color: ' . $panel_title_color . ';
			font-size: ' . $panel_title_fontsize . ';
			text-transform: ' . $panel_title_text_transform . ';
			padding-left: ' . $panel_padding_left . ';
			padding-bottom: ' . $panel_padding_bottom . ';
			text-align: ' . $panel_text_align . ';
			line-height: 1;
		}
		.project-panel .pp-title {
			padding: ' . $title_padding_top . ' 0rem ' . $title_padding_bottom . ' 0rem;
		}
		.project-panel .pp-title a {
			color: ' . $title_color . '; 
			font-size: ' . $title_fontsize . '; 
			text-transform: ' . $title_text_transform . ';
		} 
		.project-panel .pp-title span {
			color: ' . $category_color . ';
			font-size: ' . $category_fontsize . ';
			text-transform: ' . $category_text_transform . ';
		}
	';
}

?>