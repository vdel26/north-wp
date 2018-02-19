<?php

// -----------------------------------------
// semplice customize nav
// -----------------------------------------

function semplice_get_customize_nav($type, $where) {

	// vars
	$nav = '<nav><ul class="customize-list">';
	$count = 0;

	// settings
	$settings = array(
		'customize' => array(
			'grid'			=> 'Grid',
			'webfonts'		=> 'Webfonts',
			'typography'	=> 'Typography',
			'navigations'	=> 'Navigations',
			'thumbhover'	=> 'Thumb Hover',
			'transitions'	=> 'Transitions',
			'projectpanel'	=> 'Project Panel',
			'footer'		=> 'Footer',
			'blog'			=> 'Blog',
			'advanced'		=> 'Advanced',
		),
		'settings' => array(
			'general'		=> 'General',
		),
	);

	// iterate
	foreach ($settings[$type] as $setting => $name) {
		// active
		$active = '';
		if($count <= 0) {
			$active = ' class="active-setting"';
		}
		// link
		if($where == 'admin') {
			$link = '<a href="#' . $type . '/' . $setting . '">';
		} else {
			$link = '<a data-new-url="#' . $type . '/' . $setting . '" data-exit-mode="close" class="editor-action" data-action-type="popup" data-action="exit">';
		}
		// transitions icon
		$transitions_icon = '';
		if($setting == 'transitions') {
			$transitions_icon = ' transitions-icon';
		}
		$nav .= '
			<li data-setting="' . $setting . '"' . $active . '>
				' . $link . '
					<span class="icon' . $transitions_icon . '">' . get_svg('backend', '/icons/' . $type . '_' . $setting) . '</span>
					<span class="setting-name">' . $name . '</span>
				</a>
			</li>
		';
		// inc count
		$count++;
	}

	// return
	return $nav . '</ul></nav>';
}

// -----------------------------------------
// semplice update menu order
// -----------------------------------------

function semplice_update_menu_order($order) {
	
	// json decode order
	$order = json_decode($order, true);
	// get menu id
	$menu_name = 'Semplice Menu';
	$menu_object = wp_get_nav_menu_object($menu_name);
	$menu_id = $menu_object->term_id;

	if(null !== $order && is_array($order) && is_object($menu_object)) {
		foreach ($order as $i => $menu_item) {
			if($menu_item['id'] == 0) {
				// type
				$url = '';
				if($menu_item['type'] == 'custom') {
					$url = $menu_item['link'];
				}
				wp_update_nav_menu_item($menu_id, $menu_item['id'], array(
					'menu-item-title'  	 => __($menu_item['title']),
					'menu-item-position' => $i,
					'menu-item-status' 	 => 'publish',
					'menu-item-type'	 => $menu_item['type'],
					'menu-item-url'		 => $url,
				));
			} else {
				// args
				$args = array(
					'ID' => $menu_item['id'],
					'menu_order' => $i,
					'post_title' => $menu_item['title'],
				);
				wp_update_post($args);

				// update menu classes
				if(!empty($menu_item['classes'])) {
					update_post_meta($menu_item['id'], '_menu_item_classes', preg_split('/\s+/', $menu_item['classes']));
				} else {
					update_post_meta($menu_item['id'], '_menu_item_classes', '');
				}
				// update url
				if(!empty($menu_item['link'])) {
					update_post_meta($menu_item['id'], '_menu_item_url', $menu_item['link']);
				}
				// update target
				if(!empty($menu_item['target']) && $menu_item['target'] == '_blank') {
					update_post_meta($menu_item['id'], '_menu_item_target', $menu_item['target']);
				} else {
					update_post_meta($menu_item['id'], '_menu_item_target', '');
				}
			}
		}
	}
}

// -----------------------------------------
// add new menu item
// -----------------------------------------

function semplice_add_menu_item($item) {
	
	// get menu id
	$menu_name = 'Semplice Menu';
	$menu_object = wp_get_nav_menu_object($menu_name);
	
	// is menu there?
	if(!$menu_object) {
		$menu_id = wp_create_nav_menu($menu_name);
		// get menu localtions
		$locations = get_theme_mod('nav_menu_locations');
		// assign new menu
		$locations['semplice-main-menu'] = $menu_id;
		// set new menu
		set_theme_mod('nav_menu_locations', $locations);
	} else if($item['id'] == 0) {
		// menu id
		$menu_id = $menu_object->term_id;
	}

	// type
	$url = '';
	if($item['type'] == 'custom') {
		$url = $item['link'];
		$post_id = 0;
		$post_type = '';
	} else {
		$post_id = $item['post_id'];
		$post_type = $item['object_type'];
	}
	$new_item_id = wp_update_nav_menu_item($menu_id, $item['id'], array(
		'menu-item-object' => $post_type,
		'menu-item-object-id' => $post_id,
		'menu-item-title'  	 => __($item['title']),
		'menu-item-status' 	 => 'publish',
		'menu-item-type'	 => $item['type'],
		'menu-item-url'		 => $url,
		'menu-item-parent-id' => 0,
	));

	return $new_item_id;
}

// -----------------------------------------
// activate menu
// -----------------------------------------

function semplice_activate_menu() {
	// get menu id
	$menu_name = 'Semplice Menu';
	$menu_object = wp_get_nav_menu_object($menu_name);
	$menu_id = $menu_object->term_id;
	// get menu localtions
	$locations = get_theme_mod('nav_menu_locations');
	// is semplice active?
	if($locations['semplice-main-menu'] != $menu_id) {
		// assign new menu
		$locations['semplice-main-menu'] = $menu_id;
		// set new menu
		set_theme_mod('nav_menu_locations', $locations);
	}
}

// -----------------------------------------
// semplice get posts
// -----------------------------------------

function semplice_get_menus() {

	// semplice api
	global $admin_api;

	// get navigation json
	$navigations = json_decode(get_option('semplice_customize_navigations'), true);

	// output
	$output = array();

	// is array?
	if(is_array($navigations)) {
		// loop through navigations
		foreach ($navigations as $id => $nav) {
			// add content to output
			if($id == 'default') {
				$output[$id] = $navigations[$id];
			} else {
				$output[$id] = $admin_api->customize['navigations']->get('both', $id, false, true);
			}
		}
	}

	// set system default - gets loaded if there are any other navs
	$output['system_default'] = $admin_api->customize['navigations']->get('both', false, false, false);

	// return
	return $output;
}

?>