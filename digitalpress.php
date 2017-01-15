<?php

/**
 * Plugin Name: DigitalPress
 * Plugin URI: https://digitalpress.co
 * Description: The core plugin for DigitalPress.co.
 * Version: 1.0
 * Author: DigitalPress
 * Author URI: https://digitalpress.co
 */

define('DP_DIR', __DIR__);

require_once DP_DIR . '/includes/j4mie/idiorm.php';
require_once DP_DIR . '/includes/Braintree/Braintree.php';

Braintree_Configuration::environment(DP_BRAINTREE_ENV);
Braintree_Configuration::merchantId(DP_BRAINTREE_MERCHANT_ID);
Braintree_Configuration::publicKey(DP_BRAINTREE_PUBLIC_KEY);
Braintree_Configuration::privateKey(DP_BRAINTREE_PRIVATE_KEY);

require DP_DIR . '/includes/connect.php';
require DP_DIR . '/includes/functions.php';
require DP_DIR . '/includes/actions.php';
require DP_DIR . '/includes/filters.php';
