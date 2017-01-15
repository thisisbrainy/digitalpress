<?php

/* Overwrite lost password link because WooCommerce is an asshole */
add_filter('lostpassword_url', function() {

	return site_url('wp-login.php?action=lostpassword');

});

/* Show site ID in wpmu sites */
add_filter('wpmu_blogs_columns', function($columns) {

	$columns['site_id'] = 'ID';
	$columns['runtime'] = 'Runtime';

	return $columns;

});

/* Remove admin footer */
add_filter('admin_footer_text', function() {

	if(!is_network_admin()) {

		return '';

	}

});

add_filter('show_admin_bar', '__return_false');
