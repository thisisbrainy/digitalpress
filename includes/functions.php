<?php

/* Force HTTPS */
if($_SERVER['HTTP_HOST'] === 'digitalpress.co' && empty($_SERVER['HTTPS'])) {

	header('Location: https://digitalpress.co' . $_SERVER['REQUEST_URI']);

}

/* Cache */
function dp_cache_set($key, $value) {

	$value = json_encode($value);

	$cache_put = ORM::for_table('dp_cache')->create();
	$cache_put->c_key = $key;
	$cache_put->c_value = $value;
	$cache_put->save();
	
}

function dp_cache_get($key) {

	$cache_get = ORM::for_table('dp_cache')->where('c_key', $key)->find_one();

	if($cache_get) {

		return json_decode($cache_get->c_value, true);

	}

	return false;

}

function dp_dashboard_admin() {
	
	require __DIR__ . '/../admin.php';

}

// get latest post from main site
if(!function_exists('dp_main_site_latest_post')) {

	function dp_main_site_latest_post() {

		switch_to_blog(1);

		$posts = get_posts(['numberposts' => 1]);

		$latest_post = (object) [
			'title' => $posts[0]->post_title,
			'url' => get_permalink($posts[0]->ID),
			'date' => $posts[0]->post_date
		];

		restore_current_blog();
		
		return $latest_post;

	}

}

/* Get site stats */
if(!function_exists('dp_site_stats')) {

	function dp_site_stats($site_id = false) {

		if(!$site_id) {

			$site_id = get_current_blog_id();

		}

		$monthly_people = count(ORM::for_table('dp_stats')
			->where('site_id', $site_id)
			->where('is_unique', 1)
			->where('is_robot', 0)
			->where('month', date('m'))
			->where('year', date('Y'))
			->find_many());

		if(!$monthly_people) {

			$monthly_people = 0;

		}

		if(date('m') === '1') {

			$previous_month === '12';

		} else {

			$previous_month = ((int) date('m')) - 1;

		}

		if(date('m') === '1') {

			$previous_year = ((int) date('Y') - 1);

		} else {

			$previous_year = date('Y');

		}

		$previous_monthly_people = count(ORM::for_table('dp_stats')
			->where('site_id', $site_id)
			->where('is_unique', 1)
			->where('is_robot', 0)
			->where('month', $previous_month)
			->where('year', $previous_year)
			->find_many());

		if(!$previous_monthly_people) {

			$previous_monthly_people = 0;

		}

		$monthly_views = count(ORM::for_table('dp_stats')
			->where('site_id', $site_id)
			->where('is_robot', 0)
			->where('month', date('m'))
			->where('year', date('Y'))
			->find_many());

		if(!$monthly_views) {

			$monthly_views = 0;

		}

		$previous_monthly_views = count(ORM::for_table('dp_stats')
			->where('site_id', $site_id)
			->where('is_robot', 0)
			->where('month', $previous_month)
			->where('year', $previous_year)
			->find_many());

		if(!$previous_monthly_views) {

			$previous_monthly_views = 0;

		}

		return (object) [
			'monthly_views' => $monthly_views,
			'previous_monthly_views' => $previous_monthly_views,
			'monthly_people' => $monthly_people,
			'previous_monthly_people' => $previous_monthly_people
		];

	}

}

function dp_dashboard_get_values($site_id = false) {

	if(!$site_id) {

		$site_id = get_current_blog_id();

	}

	$values = [];

	// runtime days
	if(!get_blog_option($site_id, 'dp_days')) {

		add_blog_option($site_id, 'dp_days', 0);

	}

	$values['runtime'] = number_format((int) get_blog_option($site_id, 'dp_days', 0));

	// space
	$total_space = get_space_allowed();
	$used_space = get_space_used();
	$free_space = $total_space - $used_space;

	$values['space'] = round((100 - ($used_space / $total_space) * 100), 2);

	// views
	$values['views'] = number_format(dp_site_stats($site_id)->monthly_views);
	$values['previous_views'] = number_format(dp_site_stats($site_id)->previous_monthly_views);

	// people
	$values['people'] = number_format(dp_site_stats($site_id)->monthly_people);
	$values['previous_people'] = number_format(dp_site_stats($site_id)->previous_monthly_people);

	return json_encode($values);

}
