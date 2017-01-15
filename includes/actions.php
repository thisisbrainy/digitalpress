<?php

/* Custom CSS in Login */
add_action('login_enqueue_scripts', function() {

	wp_enqueue_style('dp-login', plugin_dir_url(__FILE__) . '../assets/css/dp-login.css');

});

/* 7 day trial! */
add_action('init', function() {

	$site_id = get_current_blog_id();

	if(get_blog_option($site_id, 'dp_days', 0) == 0 && !get_blog_option($site_id, 'dp_days_trial_7')) {

		add_blog_option($site_id, 'dp_days', 7);
		add_blog_option($site_id, 'dp_days_trial_7', 1);

	}

});

/* Purchase Runtime */
add_action('init', function() {

	if(!empty($_POST['site-id']) && !empty($_GET['dp']) && $_GET['dp'] === 'purchase-runtime') {

		$nonce = $_POST['payment-method-nonce'];
		$runtime = (int) $_POST['dp-runtime-days'];
		$site_id = (int) $_POST['site-id'];
		$back_to = $_POST['back-to'];
		$amount = (int) $runtime * 0.25;

		$result = Braintree_Transaction::sale([
			'amount' => (string) $amount,
			'paymentMethodNonce' => $nonce,
			'options' => [
				'submitForSettlement' => true
			]
		]);

		if($result->success) {

			$current_runtime = (int) get_blog_option($site_id, 'dp_days', 0);
			$new_runtime = $current_runtime + $runtime;

			update_blog_option($site_id, 'dp_days', $new_runtime);

			// send receipt to email
			$headers[] = 'From: DigitalPress <support@digitalpress.co>';
			$headers[] = 'Content-Type: text/html; charset=UTF-8';
			$user_email = wp_get_current_user()->user_email;
			$website = get_blog_details($site_id)->blogname;
			$year = date('Y');
			$month = date('m');
			$day = date('d');
			$email_message = "You just purchased $runtime days of Runtime for your website $website.<br><br>
-----------------------------------------------------<br>
DigitalPress Receipt - $year-$month-$day<br><br>

Amount: €$amount<br><br>

DigitalPress<br>
Laeva 2, 4th floor<br>
Tallinn, Estonia, 10111<br>
-----------------------------------------------------<br><br>
Thank you for using DigitalPress!
";

			wp_mail($user_email, 'Your DigitalPress Receipt', $email_message, $headers);

			wp_safe_redirect($back_to . '&dp-message=You have added ' . $runtime . ' runtime days to this website.');

		} else if($result->transaction) {

			echo 'Error: ';
			echo $result->transaction->processorResponseCode . ' - ';
			echo $result->transaction->processorResponseText . '<br><br>';
			echo 'If this makes no sense to you, get in touch with our support via support@digitalpress.co.';

		} else {

			echo 'Something went wrong. Get in touch with our support via support@digitalpress.co.';

		}

		die();

	}

});

/* Cron service
 * -1 runtime day on all sites
 */
add_action('init', function() {

	if(!empty($_GET['service']) && $_GET['service'] === 'dpruntime') {

		$sites = get_sites();

		foreach($sites as $site) {

			$days = (int) get_blog_option($site->blog_id, 'dp_days', 0);

			if($days == 0) {

				// nada

			} else {

				$days = $days - 1;
				update_blog_option($site->blog_id, 'dp_days', $days);

				if($days <= 5) {

					$headers[] = 'From: DigitalPress <support@digitalpress.co>';
					$headers[] = 'Content-Type: text/html; charset=UTF-8';
					$user_email = get_blog_option($site->blog_id, 'admin_email');
					$website = get_blog_details($site->blog_id)->blogname;
					$website_url = get_blog_option($site->blog_id, 'siteurl') . '/wp-admin/';
					$email_message = "Your website $website is running low on Runtime!<br>
This means that your website has only $days day(s) left till it will be hidden from visitors. <br><a href='$website_url'>Sign in</a> to purchase more Runtime.";

					wp_mail($user_email, 'Your Website is running low on Runtime!', $email_message, $headers);

					
				}

			}

		}

		die();

	}

});

/* Clear cache once an hour */
add_action('init', function() {

	if(!empty($_GET['service']) && $_GET['service'] === 'dpcache') {

		$cache = ORM::for_table('dp_cache')->find_many();

		foreach($cache as $c) {

			$c->delete();

		}

	}

});

add_action('manage_sites_custom_column', function($column, $blog_id) {

	global $wpdb;

	if($column == 'site_id') {

		echo $blog_id;

	}

	if($column == 'runtime') {

		echo get_blog_option($blog_id, 'dp_days', 0) . ' days';

	}

	return $column;

}, 10, 3);

/* Replace dashboard */
add_action('admin_init', function() {

	global $pagenow;

	if($pagenow === 'index.php') {

		#$url = 
		#header('Location: ' . $_SERVER['REQUEST_URI'] . '')
	}

});

/* Redirect to new dashboard */
add_action('current_screen', function($screen) {

	if($screen->id == 'dashboard') {

		wp_safe_redirect(admin_url('admin.php?page=dp-dashboard'));
		exit();

	}

});

/* Redirect to Your Websites on login */
add_action('login_redirect', function($redirect_to, $request, $user) {

	return 'https://digitalpress.co/your-websites/';

}, 10, 3);

/* Remove welcome panel */
remove_action('welcome_panel', 'wp_welcome_panel');

/* Add Dashboard page */
add_action('admin_menu', function() {

	add_menu_page(__('Dashboard', 'dp_dashboard'), 'Dashboard', 'manage_options', 'dp-dashboard', 'dp_dashboard_admin', 'dashicons-dashboard', 2);

});

/* Load scripts and styles */
add_action('admin_enqueue_scripts', function($hook) {

		wp_register_style('pressify-style', plugin_dir_url(__FILE__) . '../assets/css/style.css', [], time());

		if(!empty($_GET['page']) && $_GET['page'] === 'dp-dashboard') {

			wp_register_script('braintree', 'https://js.braintreegateway.com/js/braintree-2.24.1.min.js', array(), false, true);
			wp_register_script('dp-dashboard-js', plugin_dir_url(__FILE__) . '../assets/js/dp-dashboard.js', array(), false, true);

		}
		
		wp_enqueue_style('pressify-style');
		wp_enqueue_script('braintree');
		wp_enqueue_script('dp-dashboard-js');

});

/* Remove My Sites from Dashboard submenu */
add_action('admin_menu', function() {

	remove_submenu_page('index.php', 'my-sites.php');

}, 999);

/* Remove Page Builder settings */
add_action('admin_menu', function() {

	remove_submenu_page('options-general.php', 'siteorigin_panels');

}, 999);

/* Remove SSL Insecure Content from settings */
add_action('admin_menu', function() {

	remove_submenu_page('options-general.php', 'ssl-insecure-content-fixer');

}, 999);

/* Remove SSL Tests from Tools */
add_action('admin_menu', function() {

	remove_submenu_page('tools.php', 'ssl-insecure-content-fixer-tests');

}, 999);

/* Remove Dashboard from Menu */
add_action('admin_menu', function() {

	remove_menu_page('index.php');

});

/* Remove WP logo */
add_action('admin_bar_menu', function($wp_admin_bar) {

	$wp_admin_bar->remove_node('wp-logo');

}, 999);

/* Remove My Sites */
add_action('admin_bar_menu', function($wp_admin_bar) {

	$wp_admin_bar->remove_node('my-sites');

}, 999);

/* Add Your Websites */
add_action('admin_bar_menu', function($wp_admin_bar) {

	$wp_admin_bar->add_node([
		'id' => 'your-websites',
		'title' => 'Your Websites',
		'href' => 'https://digitalpress.co/your-websites/',
		'class' => 'your-websites'
	]);

}, 5);

/* Add Network */
add_action('admin_bar_menu', function($wp_admin_bar) {

	if(is_super_admin()) {

		$wp_admin_bar->add_node([
			'id' => 'network',
			'title' => 'Network',
			'href' => network_admin_url(),
			'class' => 'network'
		]);

	}

}, 10);

/* Dashboard: get values */
if(!empty($_GET['action']) && $_GET['action'] === 'dp-dashboard-get-values') {

	header('Content-Type: application/json');

	echo dp_dashboard_get_values();

	die();

}