<?php

/*
Plugin Name: Contact Form 7 - PayPal Add-on
Plugin URI: https://wpplugin.org/paypal/
Description: Integrates PayPal with Contact Form 7
Author: Scott Paterson
Author URI: https://wpplugin.org
License: GPL2
Version: 1.3
*/

/*  Copyright 2014-2015 Scott Paterson

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/



// plugin variable: cf7pp



//  plugin functions
register_activation_hook( __FILE__, "cf7pp_activate" );
register_deactivation_hook( __FILE__, "cf7pp_deactivate" );
register_uninstall_hook( __FILE__, "cf7pp_uninstall" );

function cf7pp_activate() {

	// remove ajax from contact form 7 to allow for php redirects
	function wp_config_put( $slash = '' ) {
		$config = file_get_contents (ABSPATH . "wp-config.php");
		$config = preg_replace ("/^([\r\n\t ]*)(\<\?)(php)?/i", "<?php define('WPCF7_LOAD_JS', false);", $config);
		file_put_contents (ABSPATH . $slash . "wp-config.php", $config);
	}

	if ( file_exists (ABSPATH . "wp-config.php") && is_writable (ABSPATH . "wp-config.php") ){
		wp_config_put();
	}
	else if (file_exists (dirname (ABSPATH) . "/wp-config.php") && is_writable (dirname (ABSPATH) . "/wp-config.php")){
		wp_config_put('/');
	}
	else {
		?>
		<div class="error">
			<p><?php _e( 'wp-config.php is not writable, please make wp-config.php writable - set it to 0777 temporarily, then set back to its original setting after this plugin has been activated.', 'my-text-domain' ); ?></p>
		</div>
		<?php
		exit;
	}
	
	// write initical options
	$cf7pp_options = array(
		'currency'    => '25',
		'language'    => '3',
		'liveaccount'    => '',
		'sandboxaccount'    => '',
		'mode' => '2',
		'cancel'    => '',
		'return'    => '',
		'tax'    => '',
		'tax_rate' => ''
	);

add_option("cf7pp_options", $cf7pp_options);

	
}

function cf7pp_deactivate() {

	function wp_config_delete( $slash = '' ) {
		$config = file_get_contents (ABSPATH . "wp-config.php");
		$config = preg_replace ("/( ?)(define)( ?)(\()( ?)(['\"])WPCF7_LOAD_JS(['\"])( ?)(,)( ?)(0|1|true|false)( ?)(\))( ?);/i", "", $config);
		file_put_contents (ABSPATH . $slash . "wp-config.php", $config);
	}

	if (file_exists (ABSPATH . "wp-config.php") && is_writable (ABSPATH . "wp-config.php")) {
		wp_config_delete();
	}
	else if (file_exists (dirname (ABSPATH) . "/wp-config.php") && is_writable (dirname (ABSPATH) . "/wp-config.php")) {
		wp_config_delete('/');
	}
	else if (file_exists (ABSPATH . "wp-config.php") && !is_writable (ABSPATH . "wp-config.php")) {
		?>
		<div class="error">
			<p><?php _e( 'wp-config.php is not writable, please make wp-config.php writable - set it to 0777 temporarily, then set back to its original setting after this plugin has been deactivated.', 'my-text-domain' ); ?></p>
		</div>
		<button onclick="goBack()">Go Back and try again</button>
		<script>
		function goBack() {
			window.history.back();
		}
		</script>
		<?php
		exit;
	}
	else if (file_exists (dirname (ABSPATH) . "/wp-config.php") && !is_writable (dirname (ABSPATH) . "/wp-config.php")) {
		?>
		<div class="error">
			<p><?php _e( 'wp-config.php is not writable, please make wp-config.php writable - set it to 0777 temporarily, then set back to its original setting after this plugin has been deactivated.', 'my-text-domain' ); ?></p>
		</div>
		<button onclick="goBack()">Go Back and try again</button>
		<script>
		function goBack() {
			window.history.back();
		}
		</script>
		<?php
		exit;
	}
	else {
		?>
		<div class="error">
			<p><?php _e( 'wp-config.php is not writable, please make wp-config.php writable - set it to 0777 temporarily, then set back to its original setting after this plugin has been deactivated.', 'my-text-domain' ); ?></p>
		</div>
		<button onclick="goBack()">Go Back and try again</button>
		<script>
		function goBack() {
			window.history.back();
		}
		</script>
		<?php
		exit;
	}

	delete_option("cf7pp_my_plugin_notice_shown");
	
}

function cf7pp_uninstall() {
}


// display activation notice

add_action('admin_notices', 'cf7pp_my_plugin_admin_notices');

function cf7pp_my_plugin_admin_notices() {
if (!get_option('cf7pp_my_plugin_notice_shown')) {
echo "<div class='updated'><p><a href='admin.php?page=cf7pp_admin_table'>Click here to view the plugin settings</a>.</p></div>";
update_option("cf7pp_my_plugin_notice_shown", "true");
}
}


function wpecpp_plugin_settings_link($links)
{
unset($links['edit']);

$forum_link   = '<a target="_blank" href="https://wordpress.org/support/plugin/contact-form-7-paypal-add-on">' . __('Support', 'PTP_LOC') . '</a>';
$premium_link = '<a target="_blank" href="https://wpplugin.org/contact-form-7-paypal-add-on/">' . __('Pro Version', 'PTP_LOC') . '</a>';
$settings = '<a href="admin.php?page=cf7pp_admin_table">' . __('Settings', 'PTP_LOC') . '</a>';
$edit = '<a href="plugin-editor.php?file=contact-form-7-paypal-add-on/paypal.php">' . __('Edit', 'PTP_LOC') . '</a>';
array_push($links, $edit);
array_push($links, $settings);
array_push($links, $forum_link);
array_push($links, $premium_link);
return $links; 
}

$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'wpecpp_plugin_settings_link' );






// check to make sure contact form 7 is installed and active
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {





	// add paypal menu under contact form 7 menu
	add_action( 'admin_menu', 'cf7pp_admin_menu', 20 );

	function cf7pp_admin_menu() {
		$addnew = add_submenu_page( 'wpcf7',
			__( 'PayPal Settings', 'contact-form-7' ),
			__( 'PayPal Settings', 'contact-form-7' ),
			'wpcf7_edit_contact_forms', 'cf7pp_admin_table',
			'cf7pp_admin_table' );
	}


	// hook into contact form 7 - before send
	add_action('wpcf7_before_send_mail', 'cf7pp_before_send_mail');

	function cf7pp_before_send_mail( $cf7 ) {
	}
	
	
	// hook into contact form 7 - after send
	add_action('wpcf7_mail_sent', 'cf7pp_after_send_mail');

	function cf7pp_after_send_mail( $cf7 ) {
		
		global $postid;
		
		$postid = $cf7->id();
		
		$enable = get_post_meta( $postid, "_cf7pp_enable", true);
		$email = get_post_meta( $postid, "_cf7pp_email", true);
		
		if ($enable == "1") {
			if ($email == "2") {
			
				include_once ('includes/redirect.php');
				
				exit;
			
			}
		}
		
	}
	
	
	
	// hook into contact form 7 form
	add_action('wpcf7_admin_after_additional_settings', 'cf7pp_admin_after_additional_settings');


	function cf7pp_editor_panels ( $panels ) {
		
		$new_page = array(
			'PayPal' => array(
				'title' => __( 'PayPal', 'contact-form-7' ),
				'callback' => 'cf7pp_admin_after_additional_settings'
			)
		);
		
		$panels = array_merge($panels, $new_page);
		
		return $panels;
		
	}
	add_filter( 'wpcf7_editor_panels', 'cf7pp_editor_panels' );


	function cf7pp_admin_after_additional_settings( $cf7 ) {
		
		$post_id = sanitize_text_field($_GET['post']);
		
		$enable = get_post_meta($post_id, "_cf7pp_enable", true);
		$name = get_post_meta($post_id, "_cf7pp_name", true);
		$price = get_post_meta($post_id, "_cf7pp_price", true);
		$id = get_post_meta($post_id, "_cf7pp_id", true);
		$email = get_post_meta($post_id, "_cf7pp_email", true);
		
		if ($enable == "1") { $checked = "CHECKED"; } else { $checked = ""; }
		
		if ($email == "1") { $before = "SELECTED"; $after = ""; } elseif ($email == "2") { $after = "SELECTED"; $before = ""; } else { $before = ""; $after = ""; }
		
		$admin_table_output = "";
		$admin_table_output .= "<form>";
		$admin_table_output .= "<div id='additional_settings-sortables' class='meta-box-sortables ui-sortable'><div id='additionalsettingsdiv' class='postbox'>";
		$admin_table_output .= "<div class='handlediv' title='Click to toggle'><br></div><h3 class='hndle ui-sortable-handle'><span>PayPal Settings</span></h3>";
		$admin_table_output .= "<div class='inside'>";
		
		$admin_table_output .= "<div class='mail-field'>";
		$admin_table_output .= "<input name='enable' value='1' type='checkbox' $checked>";
		$admin_table_output .= "<label>Enable PayPal on this form</label>";
		$admin_table_output .= "</div>";
		
		$admin_table_output .= "<br /><table><tr><td>Item Description: </td></tr><tr><td>";
		$admin_table_output .= "<input type='text' name='name' value='$name'> </td><td> (Optional, if left blank customer will be able to enter their own description at checkout)</td></tr><tr><td>";
		
		$admin_table_output .= "Item Price: </td></tr><tr><td>";
		$admin_table_output .= "<input type='text' name='price' value='$price'> </td><td> (Optional, if left blank customer will be able to enter their own price at checkout. Format: for $2.99, enter 2.99)</td></tr><tr><td>";
		
		$admin_table_output .= "Item ID / SKU: </td></tr><tr><td>";
		$admin_table_output .= "<input type='text' name='id' value='$id'> </td><td> (Optional)</td></tr><tr><td>";
		
		//$admin_table_output .= "Email before or after payment: <br />";
		//$admin_table_output .= "<select name='email'><option value='1' $before>Before</option><option value='2' $after>After</option></select>";
		$admin_table_output .= "<input type='hidden' name='email' value='2'>";
		
		$admin_table_output .= "<input type='hidden' name='post' value='$post_id'>";
		
		$admin_table_output .= "</td></tr></table></form>";
		$admin_table_output .= "</div>";
		$admin_table_output .= "</div>";
		$admin_table_output .= "</div>";

		echo $admin_table_output;
		
	}
	
	// hook into contact form 7 admin form save
	add_action('wpcf7_save_contact_form', 'cf7pp_save_contact_form');

	function cf7pp_save_contact_form( $cf7 ) {
		
			$post_id = sanitize_text_field($_POST['post']);
			
			if (!empty($_POST['enable'])) {
				$enable = sanitize_text_field($_POST['enable']);
				update_post_meta($post_id, "_cf7pp_enable", $enable);
			} else {
				update_post_meta($post_id, "_cf7pp_enable", 0);
			}
			
			$name = sanitize_text_field($_POST['name']);
			update_post_meta($post_id, "_cf7pp_name", $name);
			
			$price = sanitize_text_field($_POST['price']);
			update_post_meta($post_id, "_cf7pp_price", $price);
			
			$id = sanitize_text_field($_POST['id']);
			update_post_meta($post_id, "_cf7pp_id", $id);
			
			$email = sanitize_text_field($_POST['email']);
			update_post_meta($post_id, "_cf7pp_email", $email);
			
			
			
			
	}

	
	
	
	// admin table
	
	function cf7pp_admin_table() {
	
	if ( !current_user_can( "manage_options" ) )  {
	wp_die( __( "You do not have sufficient permissions to access this page." ) );
	}

		?>
		
		<form method='post' action='<?php $_SERVER["REQUEST_URI"]; ?>'>
		
		
		<?php
		// save and update options
		if (isset($_POST['update'])) {
		
		
			$options['currency'] = sanitize_text_field($_POST['currency']);
			$options['language'] = sanitize_text_field($_POST['language']);
			$options['liveaccount'] = sanitize_text_field($_POST['liveaccount']);
			$options['sandboxaccount'] = sanitize_text_field($_POST['sandboxaccount']);
			$options['mode'] = sanitize_text_field($_POST['mode']);
			$options['cancel'] = sanitize_text_field($_POST['cancel']);
			$options['return'] = sanitize_text_field($_POST['return']);
			$options['tax'] = sanitize_text_field($_POST['tax']);
			$options['tax_rate'] = sanitize_text_field($_POST['tax_rate']);
			
			update_option("cf7pp_options", $options);
			
			echo "<br /><div class='updated'><p><strong>"; _e("Settings Updated."); echo "</strong></p></div>";
			
		}
		
		
		$options = get_option('cf7pp_options');
		foreach ($options as $k => $v ) { $value[$k] = $v; }
		
		$siteurl = get_site_url();
		
		?>
		
		
		<table width='70%'><tr><td>
		<div class='wrap'><h2>Contact Form 7 - PayPal Settings</h2></div><br /></td><td><br />
		<input type='submit' name='btn2' class='button-primary' style='font-size: 17px;line-height: 28px;height: 32px;float: right;' value='Save Settings'>
		</td></tr></table>
		
		<table width='100%'><tr><td width='70%'>
		
		<div style="background-color:#333333;padding:8px;color:#eee;font-size:12pt;font-weight:bold;">
		&nbsp; Usage
		</div><div style="background-color:#fff;border: 1px solid #E5E5E5;padding:5px;"><br />
		
		If you go to your list of contact forms, choose one, then look at the bottom of the page you will see a new section called PayPal Settings. Here you can 
		setup individual settings for that specific contact form.
		
		<br /><br />
		
		On this page, you can setup your general PayPal settings which will be used for all contact forms.
		
		<br /><br />
		
		Once you have PayPal enabled on a form, you will receive an email as soon as the customer submits the form. Then after they have paid, you should receive a payment
		notification from PayPal with the details of the transaction.
		
		<br /><br />
		
		</div><br /><br />
		
		
		<div style="background-color:#333333;padding:8px;color:#eee;font-size:12pt;font-weight:bold;">
		&nbsp; Language & Currency
		</div><div style="background-color:#fff;border: 1px solid #E5E5E5;padding:5px;"><br />
		
		<b>Language:</b>
		<select name="language">
		<option <?php if ($value['language'] == "1") { echo "SELECTED"; } ?> value="1">Danish</option>
		<option <?php if ($value['language'] == "2") { echo "SELECTED"; } ?> value="2">Dutch</option>
		<option <?php if ($value['language'] == "3") { echo "SELECTED"; } ?> value="3">English</option>
		<option <?php if ($value['language'] == "4") { echo "SELECTED"; } ?> value="4">French</option>
		<option <?php if ($value['language'] == "5") { echo "SELECTED"; } ?> value="5">German</option>
		<option <?php if ($value['language'] == "6") { echo "SELECTED"; } ?> value="6">Hebrew</option>
		<option <?php if ($value['language'] == "7") { echo "SELECTED"; } ?> value="7">Italian</option>
		<option <?php if ($value['language'] == "8") { echo "SELECTED"; } ?> value="8">Japanese</option>
		<option <?php if ($value['language'] == "9") { echo "SELECTED"; } ?> value="9">Norwgian</option>
		<option <?php if ($value['language'] == "10") { echo "SELECTED"; } ?> value="10">Polish</option>
		<option <?php if ($value['language'] == "11") { echo "SELECTED"; } ?> value="11">Portuguese</option>
		<option <?php if ($value['language'] == "12") { echo "SELECTED"; } ?> value="12">Russian</option>
		<option <?php if ($value['language'] == "13") { echo "SELECTED"; } ?> value="13">Spanish</option>
		<option <?php if ($value['language'] == "14") { echo "SELECTED"; } ?> value="14">Swedish</option>
		<option <?php if ($value['language'] == "15") { echo "SELECTED"; } ?> value="15">Simplified Chinese -China only</option>
		<option <?php if ($value['language'] == "16") { echo "SELECTED"; } ?> value="16">Traditional Chinese - Hong Kong only</option>
		<option <?php if ($value['language'] == "17") { echo "SELECTED"; } ?> value="17">Traditional Chinese - Taiwan only</option>
		<option <?php if ($value['language'] == "18") { echo "SELECTED"; } ?> value="18">Turkish</option>
		<option <?php if ($value['language'] == "19") { echo "SELECTED"; } ?> value="19">Thai</option>
		</select>
		
		PayPal currently supports 18 languages.
		<br /><br />
		
		<b>Currency:</b> 
		<select name="currency">
		<option <?php if ($value['currency'] == "1") { echo "SELECTED"; } ?> value="1">Australian Dollar - AUD</option>
		<option <?php if ($value['currency'] == "2") { echo "SELECTED"; } ?> value="2">Brazilian Real - BRL</option> 
		<option <?php if ($value['currency'] == "3") { echo "SELECTED"; } ?> value="3">Canadian Dollar - CAD</option>
		<option <?php if ($value['currency'] == "4") { echo "SELECTED"; } ?> value="4">Czech Koruna - CZK</option>
		<option <?php if ($value['currency'] == "5") { echo "SELECTED"; } ?> value="5">Danish Krone - DKK</option>
		<option <?php if ($value['currency'] == "6") { echo "SELECTED"; } ?> value="6">Euro - EUR</option>
		<option <?php if ($value['currency'] == "7") { echo "SELECTED"; } ?> value="7">Hong Kong Dollar - HKD</option> 	 
		<option <?php if ($value['currency'] == "8") { echo "SELECTED"; } ?> value="8">Hungarian Forint - HUF</option>
		<option <?php if ($value['currency'] == "9") { echo "SELECTED"; } ?> value="9">Israeli New Sheqel - ILS</option>
		<option <?php if ($value['currency'] == "10") { echo "SELECTED"; } ?> value="10">Japanese Yen - JPY</option>
		<option <?php if ($value['currency'] == "11") { echo "SELECTED"; } ?> value="11">Malaysian Ringgit - MYR</option>
		<option <?php if ($value['currency'] == "12") { echo "SELECTED"; } ?> value="12">Mexican Peso - MXN</option>
		<option <?php if ($value['currency'] == "13") { echo "SELECTED"; } ?> value="13">Norwegian Krone - NOK</option>
		<option <?php if ($value['currency'] == "14") { echo "SELECTED"; } ?> value="14">New Zealand Dollar - NZD</option>
		<option <?php if ($value['currency'] == "15") { echo "SELECTED"; } ?> value="15">Philippine Peso - PHP</option>
		<option <?php if ($value['currency'] == "16") { echo "SELECTED"; } ?> value="16">Polish Zloty - PLN</option>
		<option <?php if ($value['currency'] == "17") { echo "SELECTED"; } ?> value="17">Pound Sterling - GBP</option>
		<option <?php if ($value['currency'] == "18") { echo "SELECTED"; } ?> value="18">Russian Ruble - RUB</option>
		<option <?php if ($value['currency'] == "19") { echo "SELECTED"; } ?> value="19">Singapore Dollar - SGD</option>
		<option <?php if ($value['currency'] == "20") { echo "SELECTED"; } ?> value="20">Swedish Krona - SEK</option>
		<option <?php if ($value['currency'] == "21") { echo "SELECTED"; } ?> value="21">Swiss Franc - CHF</option>
		<option <?php if ($value['currency'] == "22") { echo "SELECTED"; } ?> value="22">Taiwan New Dollar - TWD</option>
		<option <?php if ($value['currency'] == "23") { echo "SELECTED"; } ?> value="23">Thai Baht - THB</option>
		<option <?php if ($value['currency'] == "24") { echo "SELECTED"; } ?> value="24">Turkish Lira - TRY</option>
		<option <?php if ($value['currency'] == "25") { echo "SELECTED"; } ?> value="25">U.S. Dollar - USD</option>
		</select>
		PayPal currently supports 25 currencies.
		<br /><br /></div>
		
		
		<br /><br /><div style="background-color:#333333;padding:8px;color:#eee;font-size:12pt;font-weight:bold;">
		&nbsp; PayPal Account </div><div style="background-color:#fff;border: 1px solid #E5E5E5;padding:5px;"><br />
		
		
		<b>Live Account: </b><input type='text' name='liveaccount' value='<?php echo $value['liveaccount']; ?>'> Required
		<br />Enter a valid Merchant account ID (strongly recommend) or PayPal account email address. All payments will go to this account.
		<br /><br />You can find your Merchant account ID in your PayPal account under Profile -> My business info -> Merchant account ID
		
		<br /><br />If you don't have a PayPal account, you can sign up for free at <a target='_blank' href='https://paypal.com'>PayPal</a>. <br /><br />
		
		<b>Sandbox Account: </b><input type='text' name='sandboxaccount' value='<?php echo $value['sandboxaccount']; ?>'> Optional
		<br />Enter a valid sandbox PayPal account email address. A Sandbox account is a PayPal accont with fake money used for testing. This is useful to make sure your PayPal account and settings are working properly being going live.
		<br /><br />To create a Sandbox account, you first need a Developer Account. You can sign up for free at the <a target='_blank' href='https://www.paypal.com/webapps/merchantboarding/webflow/unifiedflow?execution=e1s2'>PayPal Developer</a> site. <br /><br />
		
		Once you have made an account, create a Sandbox Business and Personal Account <a target='_blank' href='https://developer.paypal.com/webapps/developer/applications/accounts'>here</a>. Enter the Business acount email on this page and use the Personal account username and password to buy something on your site as a customer. <br /><br /><br />
		
		<b>Sandbox Mode:</b>
		&nbsp; &nbsp; <input <?php if ($value['mode'] == "1") { echo "checked='checked'"; } ?> type='radio' name='mode' value='1'>On (Sandbox mode)
		&nbsp; &nbsp; <input <?php if ($value['mode'] == "2") { echo "checked='checked'"; } ?> type='radio' name='mode' value='2'>Off (Live mode)
		
		<br /><br /></div><br /><br />
		
		<input type='hidden' name='tax' value=''>
		<input type='hidden' name='tax_rate' value=''>
		
		<div style="background-color:#333333;padding:8px;color:#eee;font-size:12pt;font-weight:bold;">
		&nbsp; Other Settings
		</div><div style="background-color:#fff;border: 1px solid #E5E5E5;padding:5px;"><br />
		
		<b>Cancel URL: </b>
		<input type='text' name='cancel' value='<?php echo $value['cancel']; ?>'> Optional <br />
		If the customer goes to PayPal and clicks the cancel button, where do they go. Example: <?php echo $siteurl; ?>/cancel. Max length: 1,024. <br /><br />
		
		<b>Return URL: </b>
		<input type='text' name='return' value='<?php echo $value['return']; ?>'> Optional <br />
		If the customer goes to PayPal and successfully pays, where are they redirected to after. Example: <?php echo $siteurl; ?>/thankyou. Max length: 1,024. <br /><br />
		</div>
		
		<br />
		WP Plugin is an offical PayPal Partner. Various trademarks held by their respective owners.
		
		<input type='hidden' name='update'>
		</form>
		
		</td><td width="3%" valign="top">
		
		</td><td width="24%" valign="top">
		
		
		<div style="background-color:#333333;padding:8px;color:#eee;font-size:12pt;font-weight:bold;">
		&nbsp; Get the Pro Version
		</div>
		
		<div style="background-color:#fff;border: 1px solid #E5E5E5;padding:8px;">
		
		<center><label style="font-size:14pt;">Features: </label></center>
		 
		<br />
		<div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div> Link form item to quantity <br />
		<div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div> Link form item to price <br />
		<div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div> Link form items to option text fields <br />
		<div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div> Charge Tax <br />
		<div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div> Charge Shipping<br />
		<div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div> Add Quantity<br />
		<div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div> And More<br />
		<div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div> Further Plugin Development <br />
		
		<br />
		<center><a target='_blank' href="https://wpplugin.org/contact-form-7-paypal-add-on/" class='button-primary' style='font-size: 17px;line-height: 28px;height: 32px;'>Learn More</a></center>
		<br />
		</div>
		</td><td width="2%" valign="top">
		</td></tr></table>
		
		<?php
		
		
	}



} else {

	// give warning if contact form 7 is not active
	function cf7pp_my_admin_notice() {
		?>
		<div class="error">
			<p><?php _e( '<b>Contact Form 7 PayPal Add-on:</b> Contact Form 7 is not installed and / or active! ', 'my-text-domain' ); ?></p>
		</div>
		<?php
	}
	add_action( 'admin_notices', 'cf7pp_my_admin_notice' );

}


?>