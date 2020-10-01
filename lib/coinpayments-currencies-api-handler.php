<?php
if (!defined('ABSPATH')) {
    exit;
}

class Coinpayments_Currencies_API_Handler
{

    const API_URL = 'https://api.coinpayments.net';
    const API_VERSION = '1';

    const API_CURRENCIES_ACTION = 'currencies';
    const CRYPTO_TYPE = 'crypto';

    /**
     * @return mixed
     * @throws Exception
     */
    public function get_coin_crypto_currencies()
    {

        $params = array(
            'types' => self::CRYPTO_TYPE,
        );
        $items = array();

        $listData = $this->get_coin_currencies($params);
        if (!empty($listData['items'])) {
            $items = $listData['items'];
        }

        return $items;
    }

    public function get_coin_currencies($params = array())
    {
        return $this->send_request(self::API_CURRENCIES_ACTION, $params);
    }

    /**
     * @param $action
     * @return string
     */
    protected function get_api_url($action)
    {
        return sprintf('%s/api/v%s/%s', self::API_URL, self::API_VERSION, $action);
    }

    /**
     * @param $api_action
     * @param null $params
     * @return bool|mixed
     * @throws Exception
     */
    protected function send_request($api_action, $params = null)
    {

        $response = false;

        $api_url = $this->get_api_url($api_action);
        try {

            $curl = curl_init();

            $options = array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
            );

            $headers = array(
                'Content-Type: application/json',
            );

            $options[CURLOPT_HTTPHEADER] = $headers;

            if (!empty($params)) {
                $api_url .= '?' . http_build_query($params);
            }

            $options[CURLOPT_URL] = $api_url;

            curl_setopt_array($curl, $options);

            $response = json_decode(curl_exec($curl), true);

            curl_close($curl);

        } catch (Exception $e) {

        }
        return $response;
    }

}
