<?php
/**
 * Plugin Name: Simple PayPal Recurring Donations
 * Plugin URI:  https://github.com/yknivag/SPRDonationsPlugin
 * Description: Plugin for accepting recurring PayPal donations via a simple shortcode
 * Version:     0.0.1
 * Author:      yknivag
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: sprdonateplugin
 */

define('SPRDNTPLGN_SITE_HOME_URL', home_url());

// Initialization of the plugin function
if ( ! function_exists ( 'sprdntplgn_plugin_init' ) ) {
	function sprdntplgn_plugin_init() {
		global $sprdntplgn_options;
		// Internationalization, first(!)
		load_plugin_textdomain( 'sprdonateplugin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
		if ( ! is_admin() || ( is_admin() && isset( $_GET['page'] ) && $_GET['page'] == 'sprdntplgn_plugin' ) ) {
			sprdntplgn_register_settings();
		}
	}
}

// Adding admin plugin settings page function
if ( ! function_exists( 'add_sprdntplgn_admin_menu' ) ) {
	function add_sprdntplgn_admin_menu() {
		add_menu_page( __( 'SPR Dontations Plugin', 'sprdonateplugin' ), __( 'SPR Dontations Plugin', 'sprdonateplugin' ), 'manage_options', 'sprdntplgn_plugin', 'sprdntplgn_settings_page', 'dashicons-heart');
		//call register settings function
	}
}

// Initialization plugin settings function
if ( ! function_exists( 'sprdntplgn_register_settings' ) ) {
	function sprdntplgn_register_settings() {
		global $wpdb, $sprdntplgn_options;
		$sprdntplgn_option_defaults = array(
			'sprdntplgn_paypal_email'       => ''
		);
		// install the option defaults
		if ( is_multisite() ) {
			if ( ! get_site_option( 'sprdntplgn_options' ) ) {
				add_site_option( 'sprdntplgn_options', $sprdntplgn_option_defaults, '', 'yes' );
			}
		} else {
			if ( ! get_option( 'sprdntplgn_options' ) )
				add_option( 'sprdntplgn_options', $sprdntplgn_option_defaults, '', 'yes' );
		}
		// get options from the database
		if ( is_multisite() )
			$sprdntplgn_options = get_site_option( 'sprdntplgn_options' ); // get options from the database
		else
			$sprdntplgn_options = get_option( 'sprdntplgn_options' );// get options from the database
		// array merge incase this version has added new options
		$sprdntplgn_options = array_merge( $sprdntplgn_option_defaults, $sprdntplgn_options );
		update_option( 'sprdntplgn_options', $sprdntplgn_options );
	}
}
// Admin plugin settings page content function
if ( ! function_exists( 'sprdntplgn_settings_page' ) ) {
	function sprdntplgn_settings_page() {
	    	    
		global $sprdntplgn_options;
		$message = '';
		if( isset( $_POST['sprdntplgn_submit'] ) && check_admin_referer( plugin_basename(__FILE__), 'sprdntplgn_nonce_name' ) ) {
                    
                    //Save paypal email address
                    if ( isset( $_POST['sprdntplgn_paypal_account'] ) ) {
                            $sprdntplgn_paypal_account = sanitize_email( $_POST['sprdntplgn_paypal_account'] );
                            if ( is_email( $sprdntplgn_paypal_account ) ) {
                                    $sprdntplgn_options['sprdntplgn_paypal_email'] = $sprdntplgn_paypal_account;
                            } else {
                                    $error_message = __( 'Email is incorrect', 'sprdonateplugin' );
                            }
                    }

                    //Save options
                    $sprdntplgn_options['sprdntplgn_item_name'] = sanitize_text_field($_POST["sprdntplgn_item_name"]);
                    $sprdntplgn_options['sprdntplgn_payment_currency'] = sanitize_text_field($_POST["sprdntplgn_payment_currency"]);
                    $sprdntplgn_options['sprdntplgn_currency_symbol'] = sanitize_text_field($_POST["sprdntplgn_currency_symbol"]);
                    $sprdntplgn_options['sprdntplgn_return_url'] = sanitize_text_field(trim($_POST["sprdntplgn_return_url"]));
                    $sprdntplgn_options['sprdntplgn_cancel_return'] = sanitize_text_field(trim($_POST["sprdntplgn_cancel_return"]));
                    $sprdntplgn_options['sprdntplgn_pw_label'] = sanitize_text_field($_POST["sprdntplgn_pw_label"]);
                    $sprdntplgn_options['sprdntplgn_pm_label'] = sanitize_text_field($_POST["sprdntplgn_pm_label"]);
                    $sprdntplgn_options['sprdntplgn_py_label'] = sanitize_text_field($_POST["sprdntplgn_py_label"]);
                    $sprdntplgn_options['sprdntplgn_pw_label_long'] = sanitize_text_field($_POST["sprdntplgn_pw_label_long"]);
                    $sprdntplgn_options['sprdntplgn_pm_label_long'] = sanitize_text_field($_POST["sprdntplgn_pm_label_long"]);
                    $sprdntplgn_options['sprdntplgn_py_label_long'] = sanitize_text_field($_POST["sprdntplgn_py_label_long"]);

                    $message = __( 'Settings saved' , 'sprdonateplugin' );
                    update_option( 'sprdntplgn_options', $sprdntplgn_options );
		}
                
                
                //$sprdntplgn_options = get_option('sprdntplgn_options');
                $sprdntplgn_payment_currency = isset($sprdntplgn_options['sprdntplgn_payment_currency']) ? $sprdntplgn_options['sprdntplgn_payment_currency'] : 'GBP';
                $sprdntplgn_currency_symbol = isset($sprdntplgn_options['sprdntplgn_currency_symbol']) ? $sprdntplgn_options['sprdntplgn_currency_symbol'] : '£';
                $sprdntplgn_return_url = isset($sprdntplgn_options['sprdntplgn_return_url']) ? $sprdntplgn_options['sprdntplgn_return_url'] : '';
                $sprdntplgn_cancel_return = isset($sprdntplgn_options['sprdntplgn_cancel_return']) ? $sprdntplgn_options['sprdntplgn_cancel_return'] : '';
                $sprdntplgn_pw_label = isset($sprdntplgn_options['sprdntplgn_pw_label']) ? $sprdntplgn_options['sprdntplgn_pw_label'] : '(p/w)';
                $sprdntplgn_pm_label = isset($sprdntplgn_options['sprdntplgn_pm_label']) ? $sprdntplgn_options['sprdntplgn_pm_label'] : '(p/m)';
                $sprdntplgn_py_label = isset($sprdntplgn_options['sprdntplgn_py_label']) ? $sprdntplgn_options['sprdntplgn_py_label'] : '(p/a)';
                $sprdntplgn_pw_label_long = isset($sprdntplgn_options['sprdntplgn_pw_label_long']) ? $sprdntplgn_options['sprdntplgn_pw_label_long'] : 'per week';
                $sprdntplgn_pm_label_long = isset($sprdntplgn_options['sprdntplgn_pm_label_long']) ? $sprdntplgn_options['sprdntplgn_pm_label_long'] : 'per month';
                $sprdntplgn_py_label_long = isset($sprdntplgn_options['sprdntplgn_py_label_long']) ? $sprdntplgn_options['sprdntplgn_py_label_long'] : 'per year';
                
		?>
		<div class="wrap">
		    <h2><?php esc_html_e( 'SPR Dontations Plugin', 'sprdonateplugin' ); ?></h2>
		    
		    <div id="poststuff"><div id="post-body">			
			<?php if ( $message != '' && isset( $_POST['sprdntplgn_submit'] ) && is_email( sanitize_email( $_POST['sprdntplgn_paypal_account'] ) ) ) { ?>
				<div class="updated fade">
					<p><strong><?php echo esc_html( $message ); ?></strong></p>
				</div>
			<?php } elseif ( '' != $error_message && ! is_email( sanitize_email( $_POST['sprdntplgn_paypal_account'] ) ) ) { ?> 
				<div class="error">
					<p><strong><?php echo esc_html( $error_message ); ?></strong></p>
				</div>
			<?php } ?>
                        
			<div class="postbox">
    			<h3 class="hndle"><label for="title"><?php esc_html_e( 'Quick Usage Guide', 'sprdonateplugin' ); ?></label></h3>
    			<div class="inside">
    			    <div class="sprdntplgn_description_shortcode_block">
    				    <p><?php esc_html_e( 'You can use the [sprdntplgn] shortcode in a WordPress post, page or sidebar text widget to show the recurring donation form. With no options, it will show the bespoke recurring donation form.', 'sprdonateplugin' ); ?></p>
    				    <p><?php esc_html_e( 'Alternatively, you can use the following parameters to customise the form/widget.', 'sprdonateplugin' ); ?></p>
    				    <dl>
    				        <dt>item_name</dt>
    				        <dd>
    				            <?php esc_html_e( 'This will show as the description at PayPal, make it something people will recognise.', 'sprdonateplugin' ); ?><br />
    				            e.g. <code>[sprdntplgn item_name="Donations to My Charity"]</code><br />
    				            <?php esc_html_e( 'Specifying this in the shortcode overides any default description set below.', 'sprdonateplugin' ); ?>
    				        </dd>
    				        <dt>bespoke</dt>
    				        <dd>
    				            <code>[none|simple|advanced]</code><br />
    				            <dl>
    				                <dt>none</dt>
    				                <dd><?php esc_html_e( 'No option will be given for the customer to provide their own amount, only preset amounts will be shown.', 'sprdonateplugin' ); ?>  <?php esc_html_e( 'If no presets are specified, no form will be shown.', 'sprdonateplugin' ); ?></dd>
    				                <dt>simple</dt>
    				                <dd><?php esc_html_e( 'The customer may specify only an amount.  That amount will be taken every month.', 'sprdonateplugin' ); ?></dd>
    				                <dt>advanced</dt>
    				                <dd><?php esc_html_e( 'The default. This option allows the customer to specify how much, how often and for how long they will be billed.', 'sprdonateplugin' ); ?></dd>
    				            </dl>
    				        </dd>
    				        <dt>presets</dt>
    				        <dd>
    				            <?php esc_html_e( 'These create buttons which offer shortcuts to dontaing preset amounts over a preset time.', 'sprdonateplugin' ); ?><br />
    				            e.g. <code>[sprdtnplgn presets="5.00:M|7.50:M|10.00:M"]</code>
    				            <?php esc_html_e( 'would create 3 buttons, one for 5.00 per month, one for 7.50 per month and one for 10,00 per month.', 'sprdonateplugin' ); ?><br />
    				            <?php esc_html_e( 'The letter may be "W", "M" or "Y" for "per week", "per month" or "per year".', 'sprdonateplugin' ); ?>
    				            <?php esc_html_e( 'If you wish to use only preset buttons remember to set', 'sprdonateplugin' ); ?> <code>bespoke=false</code>
    				        </dd>
    				        <dt>one-off</dt>
    				        <dd>
    				            <code>[1|0]</code>
    				            <?php esc_html_e( 'Defaults to "0".', 'sprdonateplugin' ); ?><br />
    				            <?php esc_html_e( 'When set to "1" offers the customer a chance to make a one-off payment of an amount of their choice.', 'sprdonateplugin' ); ?>
    				        </dd>
    				    </dl>
    				    <p><?php esc_html_e( 'All other settings are global and specified below.', 'sprdonateplugin' ); ?></p>
    			    </div>
    			</div>
			</div>
			
			<div class="postbox">
    			<h3 class="hndle"><label for="title">Plugin Settings</label></h3>
    			<div class="inside">			
        			<form id="sprdntplgn_settings_form" method='post' action=''>
        			    <input type='hidden' id='sprdnt_tab_paypal' name='sprdnt_tab_paypal' value='1' />
        			    <fieldset>
        			        <legend><?php esc_html_e('Basic Settings', 'sprdonateplugin' ); ?></legend>
        				    <label>
        				        <?php esc_html_e( 'Your PayPal Account Email Address', 'sprdonateplugin' ); ?>
        				        <input type='text' name='sprdntplgn_paypal_account' size='70' id='sprdntplgn_paypal_account' value="<?php if ( '' != $sprdntplgn_options['sprdntplgn_paypal_email'] ) esc_html( sanitize_email( $sprdntplgn_options['sprdntplgn_paypal_email'] ) ); ?>" placeholder ="<?php esc_html_e( 'The donation will go to this PayPal account.', 'sprdonateplugin' ); ?>" />
        				    </label><br />
        				    <label>
        				        <?php esc_html_e( 'Default Description', 'sprdonateplugin' ); ?> (<?php esc_html_e( 'May be overidden in the shortcode', 'sprdonateplugin' ); ?>)
        				        <input type='text' name='sprdntplgn_item_name' size='70' id='sprdntplgn_item_name' value="<?php if ( '' != $sprdntplgn_options['sprdntplgn_item_name'] ) echo esc_html( $sprdntplgn_options['sprdntplgn_item_name'] ); ?>" placeholder ="<?php esc_html_e( 'Donation to My Charity.', 'sprdonateplugin' ); ?>" />
        				    </label><br />
        				    <label>
        				        <?php esc_html_e( 'Currency Code', 'sprdonateplugin' ); ?> (<?php esc_html_e( 'All donations will be paid in this currency.', 'sprdonateplugin' ); ?>)
                                <select id="sprdntplgn_payment_currency" name="sprdntplgn_payment_currency">
                                    <option value="EUR" <?php echo ($sprdntplgn_payment_currency == 'EUR') ? 'selected="selected"' : ''; ?>>Euros (EUR)</option>
                                    <option value="GBP" <?php echo ($sprdntplgn_payment_currency == 'GBP') ? 'selected="selected"' : ''; ?>>Pounds Sterling (GBP)</option>
                                    <option value="USD" <?php echo ($sprdntplgn_payment_currency == 'USD') ? 'selected="selected"' : ''; ?>>US Dollars (USD)</option>
                                    <option value="AUD" <?php echo ($sprdntplgn_payment_currency == 'AUD') ? 'selected="selected"' : ''; ?>>Australian Dollars (AUD)</option>
                                    <option value="BRL" <?php echo ($sprdntplgn_payment_currency == 'BRL') ? 'selected="selected"' : ''; ?>>Brazilian Real (BRL)</option>
                                    <option value="CAD" <?php echo ($sprdntplgn_payment_currency == 'CAD') ? 'selected="selected"' : ''; ?>>Canadian Dollars (CAD)</option>
                                    <option value="CNY" <?php echo ($sprdntplgn_payment_currency == 'CNY') ? 'selected="selected"' : ''; ?>>Chinese Yuan (CNY)</option>
                                    <option value="CZK" <?php echo ($sprdntplgn_payment_currency == 'CZK') ? 'selected="selected"' : ''; ?>>Czech Koruna (CZK)</option>
                                    <option value="DKK" <?php echo ($sprdntplgn_payment_currency == 'DKK') ? 'selected="selected"' : ''; ?>>Danish Krone (DKK)</option>
                                    <option value="HKD" <?php echo ($sprdntplgn_payment_currency == 'HKD') ? 'selected="selected"' : ''; ?>>Hong Kong Dollar (HKD)</option>
                                    <option value="HUF" <?php echo ($sprdntplgn_payment_currency == 'HUF') ? 'selected="selected"' : ''; ?>>Hungarian Forint (HUF)</option>
                                    <option value="INR" <?php echo ($sprdntplgn_payment_currency == 'INR') ? 'selected="selected"' : ''; ?>>Indian Rupee (INR)</option>
                                    <option value="IDR" <?php echo ($sprdntplgn_payment_currency == 'IDR') ? 'selected="selected"' : ''; ?>>Indonesia Rupiah (IDR)</option>
                                    <option value="ILS" <?php echo ($sprdntplgn_payment_currency == 'ILS') ? 'selected="selected"' : ''; ?>>Israeli Shekel (ILS)</option>
                                    <option value="JPY" <?php echo ($sprdntplgn_payment_currency == 'JPY') ? 'selected="selected"' : ''; ?>>Japanese Yen (JPY)</option>
                                    <option value="MYR" <?php echo ($sprdntplgn_payment_currency == 'MYR') ? 'selected="selected"' : ''; ?>>Malaysian Ringgits (MYR)</option>
                                    <option value="MXN" <?php echo ($sprdntplgn_payment_currency == 'MXN') ? 'selected="selected"' : ''; ?>>Mexican Peso (MXN)</option>
                                    <option value="NZD" <?php echo ($sprdntplgn_payment_currency == 'NZD') ? 'selected="selected"' : ''; ?>>New Zealand Dollar (NZD)</option>
                                    <option value="NOK" <?php echo ($sprdntplgn_payment_currency == 'NOK') ? 'selected="selected"' : ''; ?>>Norwegian Krone (NOK)</option>
                                    <option value="PHP" <?php echo ($sprdntplgn_payment_currency == 'PHP') ? 'selected="selected"' : ''; ?>>Philippine Pesos (PHP)</option>
                                    <option value="PLN" <?php echo ($sprdntplgn_payment_currency == 'PLN') ? 'selected="selected"' : ''; ?>>Polish Zloty (PLN)</option>
                                    <option value="SGD" <?php echo ($sprdntplgn_payment_currency == 'SGD') ? 'selected="selected"' : ''; ?>>Singapore Dollar (SGD)</option>
                                    <option value="ZAR" <?php echo ($sprdntplgn_payment_currency == 'ZAR') ? 'selected="selected"' : ''; ?>>South African Rand (ZAR)</option>
                                    <option value="KRW" <?php echo ($sprdntplgn_payment_currency == 'KRW') ? 'selected="selected"' : ''; ?>>South Korean Won (KRW)</option>
                                    <option value="SEK" <?php echo ($sprdntplgn_payment_currency == 'SEK') ? 'selected="selected"' : ''; ?>>Swedish Krona (SEK)</option>
                                    <option value="CHF" <?php echo ($sprdntplgn_payment_currency == 'CHF') ? 'selected="selected"' : ''; ?>>Swiss Franc (CHF)</option>
                                    <option value="TWD" <?php echo ($sprdntplgn_payment_currency == 'TWD') ? 'selected="selected"' : ''; ?>>Taiwan New Dollars (TWD)</option>
                                    <option value="THB" <?php echo ($sprdntplgn_payment_currency == 'THB') ? 'selected="selected"' : ''; ?>>Thai Baht (THB)</option>
                                    <option value="TRY" <?php echo ($sprdntplgn_payment_currency == 'TRY') ? 'selected="selected"' : ''; ?>>Turkish Lira (TRY)</option>
                                    <option value="VND" <?php echo ($sprdntplgn_payment_currency == 'VND') ? 'selected="selected"' : ''; ?>>Vietnamese Dong (VND)</option>
                                    <option value="RUB" <?php echo ($sprdntplgn_payment_currency == 'RUB') ? 'selected="selected"' : ''; ?>>Russian Ruble (RUB)</option>
                                </select>
                            </label><br />
                            <label>
                                <?php esc_html_e( 'Currency Symbol', 'sprdonateplugin' ); ?> (<?php esc_html_e( 'This symbol is shown next to the monetary values.', 'sprdonateplugin' ); ?>)
                                <input type='text' name='sprdntplgn_currency_symbol' size='10' id='sprdntplgn_currency_symbol' value="<?php if ( '' != $sprdntplgn_currency_symbol ) echo esc_html( $sprdntplgn_currency_symbol ); else echo '£'; ?>" />
                            </label>
                        </fieldset>
                        <fieldset>
                            <legend><?php esc_html_e( 'Return behaviour', 'sprdonateplugin' ); ?></legend>
                            <p><?php esc_html_e( 'Once subscription set up is complete (or abandonned) at PayPal, the customer returns to one of these two URLs.  If none are specified, the customer returns to your homepage.  The "Return URL" should be your thank you page.', 'sprdonateplugin' ); ?></p>
                            <label>
                                <?php esc_html_e( 'Return URL', 'sprdonateplugin' ); ?> (<?php esc_html_e( 'PayPal will send the user to this page after the payment.', 'sprdonateplugin' ); ?>)
                                <input type='text' name='sprdntplgn_return_url' size='70' id='sprdntplgn_return_url' value="<?php echo esc_html( $sprdntplgn_return_url ); ?>" />
                            </label><br />
                            <label>
                                <?php esc_html_e( 'Cancel URL', 'sprdonateplugin' ); ?> (<?php esc_html_e( 'PayPal will send the user to this page if the user clicks on the cancel link on the PayPal checkout page.', 'sprdonateplugin' ); ?>)
                                <input type='text' name='sprdntplgn_cancel_return' size='70' id='sprdntplgn_cancel_return' value="<?php echo esc_html( $sprdntplgn_cancel_return ); ?>" />
                            </label>
                        </fieldset>
                        <fieldset>
                            <legend><?php esc_html_e( 'Labels', 'sprdonateplugin' ); ?></legend>
                            <p><?php esc_html_e( 'The short labels are used on buttons, the long labels are used on the advanced bespoke form.', 'sprdonateplugin' ); ?></p>
                            <table>
                                <tr>
                                    <th>&nbsp;</th>
                                    <th><?php esc_html_e( 'Short Label', 'sprdonateplugin' ); ?></th>
                                    <th><?php esc_html_e( 'Long Label', 'sprdonateplugin' ); ?></th>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e( 'Weekly', 'sprdonateplugin' ); ?></th>
                                    <td><input type="text" name="sprdntplgn_pw_label" size="30" id="sprdntplgn_pw_label" value="<?php if ( '' !=  $sprdntplgn_pw_label ) echo esc_html( $sprdntplgn_pw_label ); else echo '(p/w)'; ?>" /></td>
                                    <td><input type="text" name="sprdntplgn_pw_label_long" size="30" id="sprdntplgn_pw_label_long" value="<?php if ( '' !=  $sprdntplgn_pw_label_long ) echo esc_html( $sprdntplgn_pw_label_long ); else echo 'per week'; ?>" /></td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e( 'Monthly', 'sprdonateplugin' ); ?></th>
                                    <td><input type="text" name="sprdntplgn_pm_label" size="30" id="sprdntplgn_pm_label" value="<?php if ( '' !=  $sprdntplgn_pm_label ) echo esc_html( $sprdntplgn_pm_label ); else echo '(p/m)'; ?>" /></td>
                                    <td><input type="text" name="sprdntplgn_pm_label_long" size="30" id="sprdntplgn_pm_label_long" value="<?php if ( '' !=  $sprdntplgn_pm_label_long ) echo esc_html( $sprdntplgn_pm_label_long ); else echo 'per month'; ?>" /></td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e( 'Annually', 'sprdonateplugin' ); ?></th>
                                    <td><input type="text" name="sprdntplgn_py_label" size="30" id="sprdntplgn_py_label" value="<?php if ( '' !=  $sprdntplgn_py_label ) echo esc_html( $sprdntplgn_py_label ); else echo '(p/a)'; ?>" /></td>
                                    <td><input type="text" name="sprdntplgn_py_label_long" size="30" id="sprdntplgn_py_label_long" value="<?php if ( '' !=  $sprdntplgn_py_label_long ) echo esc_html( $sprdntplgn_py_label_long ); else echo 'per year'; ?>" /></td>
                                </tr>
                            </table>
                        </fieldset>
                        <p><input type="submit" name="sprdntplgn_submit" value="<?php esc_html_e( 'Save changes', 'sprdonateplugin' ); ?>" class="button-primary" /></p>
                        <?php wp_nonce_field( plugin_basename( __FILE__ ), 'sprdntplgn_nonce_name' ); ?>
                    </form>
    			</div>
			</div>
			
		    </div></div><!-- End of poststuff and postbody -->
		
		</div><!-- end of wrap -->
	<?php 
	}
}

// Enqueue plugins scripts and styles function
if ( ! function_exists( 'sprdntplgn_enqueue_scripts' ) ) {
	function sprdntplgn_enqueue_scripts() {
		wp_enqueue_script( 'sprdntplgn_script', plugins_url( 'js/script.js' , __FILE__ ), array( 'jquery' ) );
		wp_enqueue_style( 'sprdntplgn_style', plugins_url( 'css/style.css' , __FILE__ ) );
	}
}

// Plugin form content function
if ( ! function_exists ( 'sprdntplgn_show_form' ) ) {
	function sprdntplgn_show_form( $atts ) { 
		global $sprdntplgn_options;
                
		$sprdntplgn_atts = shortcode_atts( array(
            'item_name' => '',
            'bespoke' => 'advanced',
            'presets' => '',
            'one-off' => 0,
		), $atts );
                
        // Set Currency code
            $sprdntplgn_currency_code = ( '' != $sprdntplgn_options['sprdntplgn_payment_currency'] ) ? esc_html( $sprdntplgn_options['sprdntplgn_payment_currency'] ) : 'GBP';

        // Set Currency Symbol
            $sprdntplgn_currency_symbol = ( '' != $sprdntplgn_options['sprdntplgn_currency_symbol'] ) ? esc_html( $sprdntplgn_options['sprdntplgn_currency_symbol'] ) : '£';

        // Set Return URL
            $sprdntplgn_return_url = ( '' != $sprdntplgn_options['sprdntplgn_return_url'] ) ? esc_url( $sprdntplgn_options['sprdntplgn_return_url'] ) : esc_url( SPRDNTPLGN_SITE_HOME_URL );

        // Set Cancel Return URL
            $sprdntplgn_cancel_return = ( '' != $sprdntplgn_options['sprdntplgn_cancel_return'] ) ? esc_url( $sprdntplgn_options['sprdntplgn_cancel_return'] ) : esc_url( SPRDNTPLGN_SITE_HOME_URL );
            
        // Set Description
            $sprdntplgn_item_name = ( '' != $sprdntplgn_options['sprdntplgn_item_name'] ) ? esc_html( $sprdntplgn_options['sprdntplgn_item_name'] ) : "Donation to " . get_bloginfo( 'name' );
            if ( '' != $sprdntplgn_atts['item_name'] ) {
                $sprdntplgn_item_name = esc_html( sanitize_text_field( $sprdntplgn_atts['item_name'] ) );
            }

        // Escape stored values
            $sprdntplgn_paypal_email = esc_html( sanitize_email( $sprdntplgn_options['sprdntplgn_paypal_email'] ) );

        // Labels
            $sprdntplgn_pw_label = isset( $sprdntplgn_options['sprdntplgn_pw_label'] ) ? esc_html( $sprdntplgn_options['sprdntplgn_pw_label'] ) : '(p/w)';
            $sprdntplgn_pm_label = isset( $sprdntplgn_options['sprdntplgn_pm_label'] ) ? esc_html( $sprdntplgn_options['sprdntplgn_pm_label'] ) : '(p/m)';
            $sprdntplgn_py_label = isset( $sprdntplgn_options['sprdntplgn_py_label'] ) ? esc_html( $sprdntplgn_options['sprdntplgn_py_label'] ) : '(p/a)';
            $sprdntplgn_pw_label_long = isset( $sprdntplgn_options['sprdntplgn_pw_label_long'] ) ? esc_html( $sprdntplgn_options['sprdntplgn_pw_label_long'] ) : 'per week';
            $sprdntplgn_pm_label_long = isset( $sprdntplgn_options['sprdntplgn_pm_label_long'] ) ? esc_html( $sprdntplgn_options['sprdntplgn_pm_label_long'] ) : 'per month';
            $sprdntplgn_py_label_long = isset( $sprdntplgn_options['sprdntplgn_py_label_long'] ) ? esc_html( $sprdntplgn_options['sprdntplgn_py_label_long'] ) : 'per year';
        

        // Begin form
		ob_start(); ?>
		<div class="sprdntplgn sprdntplgn_container"> <?php

		// Check for preset settings as these are displayed first
		if ( '' != $sprdntplgn_atts['presets'] ) { ?>
		    <p class="sprdntplgn sprdntplgn_separator"><?php esc_html_e( 'Choose a preset amount', 'sprdonateplugin' ); ?></p>
		    <div class="sprdntplgn sprdntplgn_presets_container">
		    <?php
		    // There are presets, split them up into arrays and process them
		    // eg [sprdntplgn presets="5.00:M|7.50:M|10.00:M"]
		    $sprdntplgn_presets = explode( "|", $sprdntplgn_atts['presets'] );
		    foreach ( $sprdntplgn_presets as $sprdntplgn_preset ) {
		        $sprdntplgn_preset_details = explode ( ":", $sprdntplgn_preset );
		        switch ( $sprdntplgn_preset_details[1] ) {
		            case "W":
		                $sprdntplgn_preset_label = $sprdntplgn_pw_label;
		                $sprdntplgn_preset_label_long = $sprdntplgn_pw_label_long;
		                break;
	                case "M":
	                    $sprdntplgn_preset_label = $sprdntplgn_pm_label;
	                    $sprdntplgn_preset_label_long = $sprdntplgn_pm_label_long;
		                break;
		            case "Y":
		                $sprdntplgn_preset_label = $sprdntplgn_py_label;
		                $sprdntplgn_preset_label_long = $sprdntplgn_py_label_long;
		                break;
	                default:
	                    $sprdntplgn_preset_label = $sprdntplgn_pm_label;
	                    $sprdntplgn_preset_label_long = $sprdntplgn_pm_label_long;
	                    $sprdntplgn_preset_details[1] = "M";
		                break;
		        } ?>
		        <div class="sprdntplgn sprdntplgn_preset">
		            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
		                <input type="hidden" name="cmd" value="_xclick-subscriptions">
		                <input type="hidden" name="business" value="<?php echo $sprdntplgn_paypal_email; ?>" />
					    <input type="hidden" name="item_name" value="<?php echo $sprdntplgn_item_name; ?>">
					    <input type="hidden" name="no_note" value="1">
					    <input type="hidden" name="currency_code" value="<?php echo $sprdntplgn_currency_code; ?>">
                        <input type="hidden" name="return" value="<?php echo $sprdntplgn_return_url; ?>">
                        <input type="hidden" name="cancel_return" value="<?php echo $sprdntplgn_cancel_return; ?>" />
                        <input type="hidden" name="a3" value="<?php echo esc_html( $sprdntplgn_preset_details[0] ); ?>">
                        <input type="hidden" name="t3" value="<?php echo esc_html( $sprdntplgn_preset_details[1] ); ?>">
    		            <input type="hidden" name="p3" value="1">
    		            <input type="hidden" name="src" value="1">
    		            <input class="sprdntplgn sprdntplgn_preset_button" type="submit" name="submit"  value="<?php echo $sprdntplgn_currency_symbol . esc_html( $sprdntplgn_preset_details[0] ) . ' ' . $sprdntplgn_preset_label; ?>" title="<?php esc_html_e( 'Donate', 'sprdonateplugin' ); echo ' ' . $sprdntplgn_currency_symbol . esc_html( $sprdntplgn_preset_details[0] ) . ' ' . $sprdntplgn_preset_label_long; ?>" aria-label="<?php esc_html_e(  'Donate', 'sprdonateplugin'  ); echo ' ' . $sprdntplgn_currency_symbol . esc_html( $sprdntplgn_preset_details[0] ) . ' ' . $sprdntplgn_preset_label_long; ?>"/>
				        <img alt="PayPal - The safer, easier way to pay online!" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
				    </form>
		        </div>
		    <?php } ?>
		    </div>
		<?php }
		
		// Check if there are both presets AND a bespoke form for the separator
		if ( '' != $sprdntplgn_atts['presets'] && 'none' != $sprdntplgn_atts['bespoke'] ) {
		    ?><div class="sprdntplgn sprdntplgn_separator">
                <p class="sprdntplgn sprdntplgn_separator">
		            <span>&mdash; <?php esc_html_e( 'or', 'sprdonateplugin' ); ?> &mdash;<br /></span>
		            <?php
		            if ( 'simple' == $sprdntplgn_atts['bespoke'] ) { ?>
		                <span><?php esc_html_e( 'Give an amount of your choice per month', 'sprdonateplugin' ); ?></span>
		            <?php } else { ?>
		                <span><?php esc_html_e( 'Give an amount of your choice, as often as you choose', 'sprdonateplugin' ); ?></span>
		            <?php } ?>
	            </p>
		    </div><?php
		}

		// Check which bespoke form to display
		if ( 'none' != $sprdntplgn_atts['bespoke'] ) {
		    if ( '' == $sprdntplgn_atts['presets'] ) {
		        if ( 'simple' == $sprdntplgn_atts['bespoke'] ) { ?>
		            <p class="sprdntplgn sprdntplgn_separator"><?php esc_html_e( 'Give an amount of your choice per month', 'sprdonateplugin' ); ?></p>
		        <?php } elseif ( 'advanced' == $sprdntplgn_atts['bespoke'] ) { ?>
		            <p class="sprdntplgn sprdntplgn_separator"><?php esc_html_e( 'Give an amount of your choice, as often as you choose', 'sprdonateplugin' ); ?></p>
		        <?php }
		    } ?>
		    <div class="sprdntplgn sprdntplgn_bespoke_container">
		        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
		            <input type="hidden" name="cmd" value="_xclick-subscriptions">
		            <input type="hidden" name="business" value="<?php echo $sprdntplgn_paypal_email; ?>" />
					<input type="hidden" name="item_name" value="<?php echo $sprdntplgn_item_name; ?>">
					<input type="hidden" name="no_note" value="1">
					<input type="hidden" name="currency_code" value="<?php echo $sprdntplgn_currency_code; ?>">
                    <input type="hidden" name="return" value="<?php echo $sprdntplgn_return_url; ?>">
                    <input type="hidden" name="cancel_return" value="<?php echo $sprdntplgn_cancel_return; ?>" />
                    <input class="sprdntplgn sprdntplgn_bespoke_advanced_fixed_p3" type="hidden" name="p3" value="1">
                    <div class="sprdntplgn sprdntplgn sprdntplgn_bespoke_display_items">
                        <div class="sprdntplgn sprdntplgn_section_item">
                            <div class="sprdntplgn sprdntplgn_amount_box">
	                            <span class="sprdntplgn sprdntplgn_currency"><?php echo $sprdntplgn_currency_symbol; ?></span>
	                            <input class="sprdntplgn sprdntplgn_amount sprdntplgn_bespoke_amount" lang="en" type="number" step="0.01" min="1" name="a3" title="<?php esc_html_e( 'Enter donation amount', 'sprdonateplugin' );?>" aria-label="<?php esc_html_e( 'Enter donation amount', 'sprdonateplugin' );?>" />
                            </div>
                        </div>
    		<?php if ( 'simple' == $sprdntplgn_atts['bespoke'] ) { ?>
                    <?php echo $sprdntplgn_pm_label_long; ?>
	                <input type="hidden" name="t3" value="M">
    		        <input type="hidden" name="src" value="1">
    		<?php } elseif ( 'advanced' == $sprdntplgn_atts['bespoke'] ) { ?>
    		            <div class="sprdntplgn sprdntplgn_section_item">
	                        <select class="sprdntplgn sprdntplgn_bespoke_advanced_t3" id="sprdntplgn_bespoke_advanced_t3" name="t3" title="<?php esc_html_e( 'Select frequency of donation', 'sprdonateplugin' ); ?>" aria-label="<?php esc_html_e( 'Select frequency of donation', 'sprdonateplugin' ); ?>">
	                            <option value="W"><?php echo $sprdntplgn_pw_label_long; ?></option>
	                            <option value="M" selected><?php echo $sprdntplgn_pm_label_long; ?></option>
	                            <option value="Y"><?php echo $sprdntplgn_py_label_long; ?></option>
	                        </select>
                        </div>
                    </div>
                    <div class="sprdntplgn sprdntplgn sprdntplgn_bespoke_display_items">
                        <div class="sprdntplgn sprdntplgn_section_item">
                            <select class="sprdntplgn sprdntplgn_bespoke_advanced_src" id="sprdntplgn_bespoke_advanced_src" name="src" title="<?php esc_html_e( 'Donate indefinitely? Or for a pre-determined time?', 'sprdonateplugin' ); ?>" aria-label="<?php esc_html_e( 'Donate indefinitely? Or for a pre-determined time?', 'sprdonateplugin' ); ?>">
                                <option value="1" selected><?php esc_html_e( 'indefinitely', 'sprdonateplugin' ); ?></option>
                                <option value="0"><?php esc_html_e( 'for', 'sprdonateplugin' ); ?></option>
                            </select>
                        </div>
                        <div class="sprdntplgn sprdntplgn_section_item">
                            <select class="sprdntplgn sprdntplgn_bespoke_advanced_p3" id="sprdntplgn_bespoke_advanced_p3" name="p3" title="<?php esc_html_e( 'Select the maximum number of payments', 'sprdonateplugin' ); ?>" aria-label="<?php esc_html_e( 'Select the maximum number of payments', 'sprdonateplugin' ); ?>">
                                <option value="1" selected><?php esc_html_e( '1', 'sprdonateplugin' ); ?> <?php esc_html_e( 'payment', 'sprdonateplugin' ); ?></option>
                                <option value="2"><?php esc_html_e( '2', 'sprdonateplugin' ); ?> <?php esc_html_e( 'payments', 'sprdonateplugin' ); ?></option>
                                <option value="3"><?php esc_html_e( '3', 'sprdonateplugin' ); ?> <?php esc_html_e( 'payments', 'sprdonateplugin' ); ?></option>
                                <option value="4"><?php esc_html_e( '4', 'sprdonateplugin' ); ?> <?php esc_html_e( 'payments', 'sprdonateplugin' ); ?></option>
                                <option value="5"><?php esc_html_e( '5', 'sprdonateplugin' ); ?> <?php esc_html_e( 'payments', 'sprdonateplugin' ); ?></option>
                                <option value="6"><?php esc_html_e( '6', 'sprdonateplugin' ); ?> <?php esc_html_e( 'payments', 'sprdonateplugin' ); ?></option>
                                <option value="7"><?php esc_html_e( '7', 'sprdonateplugin' ); ?> <?php esc_html_e( 'payments', 'sprdonateplugin' ); ?></option>
                                <option value="8"><?php esc_html_e( '8', 'sprdonateplugin' ); ?> <?php esc_html_e( 'payments', 'sprdonateplugin' ); ?></option>
                                <option value="9"><?php esc_html_e( '9', 'sprdonateplugin' ); ?> <?php esc_html_e( 'payments', 'sprdonateplugin' ); ?></option>
                                <option value="10"><?php esc_html_e( '10', 'sprdonateplugin' ); ?> <?php esc_html_e( 'payments', 'sprdonateplugin' ); ?></option>
                                <option value="11"><?php esc_html_e( '11', 'sprdonateplugin' ); ?> <?php esc_html_e( 'payments', 'sprdonateplugin' ); ?></option>
                                <option value="12"><?php esc_html_e( '12', 'sprdonateplugin' ); ?> <?php esc_html_e( 'payments', 'sprdonateplugin' ); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="sprdntplgn sprdntplgn sprdntplgn_bespoke_display_items">
    		<?php } ?>
    		            <div class="sprdntplgn sprdntplgn_section_item">
    		                <input class="sprdntplgn sprdntplgn_bespoke_button" type="submit" name="submit"  value="<?php esc_html_e( 'Donate', 'sprdonateplugin' ); ?>" title="<?php esc_html_e( 'Make your donation', 'sprdonateplugin' ); ?>" aria-label="<?php esc_html_e( 'Make your donation', 'sprdonateplugin' ); ?>" />
				            <img alt="PayPal - The safer, easier way to pay online!" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
			            </div>
		            </div>
    		    </form>
    		</div> <?php
		}
		
		// Check if there is EITHER presets OR a bespoke form AND a one-off for the separator
		if ( ( '' != $sprdntplgn_atts['presets'] || 'none' != $sprdntplgn_atts['bespoke'] ) && 1 == $sprdntplgn_atts['one-off'] ) {
		    ?><div class="sprdntplgn_separator">
		        <p class="sprdntplgn sprdntplgn_separator">
		            <span>&mdash; <?php esc_html_e( 'or', 'sprdonateplugin' ); ?> &mdash;<br /></span>
		            <span><?php esc_html_e( 'Make a one-off donation', 'sprdonateplugin' ); ?></span>
	            </p>
		    </div><?php
		}
		
		// Check if there is a one-off to dispaly
		if ( 1 == $sprdntplgn_atts['one-off'] ) { ?>
	        <?php if ( '' == $sprdntplgn_atts['presets'] && 'none' == $sprdntplgn_atts['bespoke'] ) { ?>
	            <p class="sprdntplgn sprdntplgn_separator"><?php esc_html_e( 'Make a one-off donation', 'sprdonateplugin' ); ?></p> <?php
	        } ?>		
		    <div class="sprdntplgn sprdntplgn_off-off_container">
		        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
		            <input type="hidden" name="cmd" value="_donations">
					<input type="hidden" name="business" value="<?php echo $sprdntplgn_paypal_email; ?>">
                    <input type="hidden" name="item_name" value="<?php echo $sprdntplgn_item_name; ?>">
					<input type="hidden" name="currency_code" value="<?php echo $sprdntplgn_currency_code; ?>">
                    <input type="hidden" name="return" value="<?php echo $sprdntplgn_return_url; ?>">
                    <input type="hidden" name="cancel_return" value="<?php echo $sprdntplgn_cancel_return; ?>" />
					<input type="hidden" name="no_note" value="0">
					<div class="sprdntplgn sprdntplgn_one-off_display_items">
					    <div class="sprdntplgn sprdntplgn_section_item">
					        <div class="sprdntplgn sprdntplgn_amount_box">
	                            <span class="sprdntplgn sprdntplgn_currency"><?php echo $sprdntplgn_currency_symbol; ?></span>
	                            <input class="sprdntplgn sprdntplgn_amount sprdntplgn_one-off_amount" lang="en" type="number" step="0.01" min="1" name="amount" id="sprdntplgn_one-off_amount" title="<?php esc_html_e( 'Enter donation amount', 'sprdonateplugin' );?>" aria-label="<?php esc_html_e( 'Enter donation amount', 'sprdonateplugin' );?>" />
                            </div>
                        </div>
                        <div class="sprdntplgn sprdntplgn_section_item">
	                        <input class="sprdntplgn sprdntplgn_one-off_button" type="submit" name="submit"  value="<?php esc_html_e( 'Donate', 'sprdonateplugin' ); ?>" title="<?php esc_html_e( 'Make one time donation', 'sprdonateplugin' ); ?>" aria-label="<?php esc_html_e( 'Make one time donation', 'sprdonateplugin' ); ?>" />
					        <img alt="PayPal - The safer, easier way to pay online!" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
				        </div>
					</div>
		        </form>
		    </div>
		<?php
		}
		
		?> </div>

		<?php $content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}

register_activation_hook( __FILE__, 'sprdntplgn_register_settings' );

add_action( 'init', 'sprdntplgn_plugin_init' );
add_action( 'admin_init', 'sprdntplgn_plugin_init' );
add_action( 'admin_menu', 'add_sprdntplgn_admin_menu' );
add_action( 'admin_enqueue_scripts', 'sprdntplgn_enqueue_scripts' );
add_action( 'wp_enqueue_scripts', 'sprdntplgn_enqueue_scripts' );
add_shortcode( 'sprdntplgn', 'sprdntplgn_show_form' );
add_filter( 'widget_text', 'do_shortcode' );
