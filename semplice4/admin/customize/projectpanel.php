<?php

// -----------------------------------------
// semplice
// /admin/projectpanel.php
// -----------------------------------------

if(!class_exists('projectpanel')) {
	class projectpanel {

		// constructor
		public function __construct() {}

		// output
		public function output() {

			return array(
				'css' => semplice_project_panel_css(false),
				'content' => semplice_project_panel_html(false, false),
			);
		}
	}

	// instance
	$this->customize['projectpanel'] = new projectpanel;
}

?>