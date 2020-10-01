<?php
/**
 * Plugin Name: Cryptocurrencies for Easy Digital Downloads
 * Plugin URI: https://www.coinpayments.net
 * Description: Adds common cryptocurrencies to Easy Digital Downloads
 * Version: 2.0.0
 * Author: CoinPayments.net
 * Author URI: https://www.coinpayments.net
 * License: GPL v2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

if (!defined('ABSPATH')) exit;


if (!class_exists('EDD_CoinPayments_Currencies')) {

    class EDD_CoinPayments_Currencies
    {

        public function __construct()
        {
            if (!class_exists('Easy_Digital_Downloads')) return;
            $this->filters();
        }


        public function coinpayments_extra_edd_currencies($currencies)
        {

            $coin_currencies = $this->get_coin_currencies();

            foreach ($coin_currencies as $currency) {
                $title = sprintf('%s (Coinpayments.NET)', $currency['name']);
                $currencies[$currency['symbol']] = $title;
            }

            return $currencies;
        }

        public function coinpayments_edd_currency_decimal_filter($decimals)
        {

            $currencies = array();
            $coin_currencies = $this->get_coin_currencies();
            foreach ($coin_currencies as $currency) {
                $currencies[$currency['symbol']] = $currency['decimalPlaces'];
            }

            $edd_currency = edd_get_currency();

            if (array_key_exists($edd_currency, $currencies)) {
                return $currencies[$edd_currency];
            }

            return $decimals;
        }

        function get_coin_currencies()
        {


            if (empty(wp_cache_get('coin_currencies'))) {
                $this->includes();
                $coinpayments = new Coinpayments_Currencies_API_Handler();
                try {
                    $coin_currencies = $coinpayments->get_coin_crypto_currencies();
                    wp_cache_set('coin_currencies', $coin_currencies);
                } catch (Exception $e) {
                }
            } else {
                $coin_currencies = wp_cache_get('coin_currencies');
            }
            return $coin_currencies;
        }

        protected function includes()
        {
            $path = trailingslashit(plugin_dir_path(__FILE__)) . 'lib';
            require_once trailingslashit($path) . 'coinpayments-currencies-api-handler.php';
        }

        protected function filters()
        {
            add_filter('edd_currencies', array($this, 'coinpayments_extra_edd_currencies'));
            add_filter('edd_format_amount_decimals', array($this, 'coinpayments_edd_currency_decimal_filter'));
        }
    }
}

function edd_coinpayments_currencies_load()
{
    return new EDD_CoinPayments_Currencies();
}

add_action('plugins_loaded', 'edd_coinpayments_currencies_load');
