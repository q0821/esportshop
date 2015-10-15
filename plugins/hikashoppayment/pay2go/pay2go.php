<?php
/**
 * @package		HikaShop for Joomla!
 * @version		1.5.8
 * @author		hikashop.com
 * @copyright	(C) 2010-2012 HIKARI SOFTWARE. All rights reserved.
 * @license		GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?>

<?php
class plgHikashoppaymentPay2go extends hikashopPaymentPlugin
{
    private $hashKey, $hashIV;
    
    var $accepted_currencies = array(
            'EUR','USD','GBP','HKD','SGD','JPY','CAD','AUD','CHF','DKK',
            'SEK','NOK','ILS','MYR','NZD','TRY','AED','MAD','QAR','SAR',
            'TWD','THB','CZK','HUF','SKK','EEK','BGN','PLN','ISK','INR',
            'LVL','KRW','ZAR','RON','HRK','LTL','JOD','OMR','RSD','TND','CNY',
    );
        
	  var $multiple = true;
    var $name = 'pay2go';
	  var $pluginConfig = array(
            'Pay2go_merchant_id'    => array('商店代號(Merchant ID)', 'input'),
            'Pay2goMPG_hash_key'    => array('加密傳送KEY (Hash Key)', 'input'),
            'Pay2goMPG_hash_iv'     => array('加密傳送IV (Hash IV)', 'input'),
            'Pay2goMPG_finish_status_id'    => array('完成付款狀態(Order Finish Status)', 'orderstatus'),
            'Pay2goMPG_fail_status_id'      => array('匯款失敗狀態(Credit or WebAtm Order Fail Status)', 'orderstatus'),
            'Pay2goMPG_test_mode'   => array('測試模式(Test_Mode)', 'boolean','0'),
	  );
        
    function onAfterOrderConfirm(&$order,&$methods,$method_id) {

        // 組成 html
        $this->payment_html = $this->_getPaymentHtml($order);

        // 是否為測試模式
        $this->htmlAction = ($methods[$order->order_payment_id]->payment_params->Pay2goMPG_test_mode) ? 'https://capi.pay2go.com/MPG/mpg_gateway' : 'https://api.pay2go.com/MPG/mpg_gateway';

        // pay2go 需要傳遞參數
        $this->params = $this->_getPay2goParams($order, $methods[$method_id]);

        return $this->showPage('end');
	  }
        
    /**
     * 組成 html
     *
     * @param obj $order_info 訂購的相關資訊
     */
    protected function _getPaymentHtml($order_info) {
        $result = array(
            'httpDomain'    =>  JURI::root(),
            'order_id'      =>  $order_info->order_id,
            'order_number'  =>  $order_info->order_number,
            'order_payment_method'  =>  "智付寶付款",
            'customer_email'        =>  $order_info->customer->user_email,
            'shipping_address'      =>  $this->_getAddress(isset($order_info->cart->shipping_address) ? $order_info->cart->shipping_address : $order_info->shipping_address),
            'products'      =>  $this->_getProducts(isset($order_info->cart->products) ? $order_info->cart->products : $order_info->products, $order_info->order_tax_info),
        );

        return $result;
    }

    /**
     * 取得地址資訊
     *
     * @param obj $address_info 地址資訊
     */
    protected function _getAddress ($address_info) {

        $result = array(
            'title'     =>  $address_info->address_title,
            'name'      =>  $address_info->address_lastname . ' ' .$address_info->address_firstname,
            'street'    =>  $address_info->address_street,
            'street'    =>  $address_info->address_street,
            'post_code' =>  $address_info->address_post_code,
            'city'      =>  $address_info->address_city,
            'state'     =>  $address_info->address_state,
            'country'   =>  $address_info->address_country,
            'telephone' =>  $address_info->address_telephone
        );

        return $result;
    }

    /**
     * 取得訂單貨物資訊
     *
     * @param obj   $product_infos 訂單貨物資訊
     * @param array $tax_info      稅率資訊
     */
    protected function _getProducts ($product_infos, $tax_info) {

        $result = array(
            'total_price'   =>  0,
            'total_tax'     =>  $tax_info[key($tax_info)]->tax_amount,
            'tax_name'      =>  key($tax_info)
        );

        foreach ($product_infos as $key => $obj){

            $result['orders'][$key] = array();
            $result['orders'][$key]['product_name']       =   $obj->order_product_name;
            $result['orders'][$key]['product_quantity']   =   $obj->order_product_quantity;
            $result['orders'][$key]['product_price']      =   $obj->order_product_price;
            $result['orders'][$key]['product_tax']        =   $obj->order_product_tax;

            // 計算總額
            $result['total_price']  += $obj->order_product_quantity * $obj->order_product_price + $obj->order_product_quantity * $obj->order_product_tax;
        }

        return $result;
    }

    /**
     * pay2go 需要傳遞參數
     *
     * @param obj $order_info   訂購的相關資訊
     * @param obj $pay2goConfig pay2go的相關資訊
     *
     * @return array
     */
    protected function _getPay2goParams($order_info, $pay2goConfig) {

        $this->hashKey = $pay2goConfig->payment_params->Pay2goMPG_hash_key;
        $this->hashIV = $pay2goConfig->payment_params->Pay2goMPG_hash_iv;

        $result = array(
            'MerchantID'        =>  $pay2goConfig->payment_params->Pay2go_merchant_id,
            'RespondType'       =>  'String',
            'TimeStamp'         =>  time(),
            'Version'           =>  '1.1',
            'MerchantOrderNo'   =>  $order_info->order_id,
            'Amt'               =>  intval($order_info->order_full_price),
            'ItemDesc'          =>  'Pay2go Order',
            'Email'             =>  $order_info->cart->customer->user_email,
            'LoginType'         =>  '0',

            'ReturnURL'         =>  JURI::root() . "index.php/hikashop-menu-for-products-listing/order",
//              'NotifyURL'         =>  JURI::root() . "plugins/hikashoppayment/pay2go/callback.php",
            'NotifyURL'         =>  JURI::root() . "index.php?option=com_hikashop&ctrl=checkout&task=notify&notif_payment=pay2go&tmpl=component&lang=tw",
            'ClientBackURL'     =>  JURI::root() . "index.php/hikashop-menu-for-products-listing/order",

        );

        // 取得檢查碼
        $result['CheckValue']       =   $this->_getCheckValue($result);

        return $result;
    }

    function onPaymentNotification(&$statuses) {
        $vars = array();
        $data = array();
        $filter = JFilterInput::getInstance();
        foreach($_REQUEST as $key => $value) {
            $key = $filter->clean($key);
            if(preg_match('#^[0-9a-z_-]{1,30}$#i', $key) && !preg_match('#^cmd$#i', $key)) {
                $value = JRequest::getString($key);
                $vars[$key] = $value;
                $data[] = $key . '=' . urlencode($value);
            }
        }
        $data = implode('&', $data) . '&cmd=_notify-validate';

        $order_id = (int)@$vars['MerchantOrderNo'];
        $dbOrder = $this->getOrder($order_id);
        // 取得付款方式參數
        $this->loadPaymentParams($dbOrder);
        if(empty($this->payment_params)){
            return false;
        }

        if($this->payment_params->Pay2goMPG_test_mode){
            echo print_r($vars, true) . "\r\n\r\n";
        }

        if(empty($dbOrder)) {
            echo '找不到訂單記錄 ' . @$vars['MerchantOrderNo'];
            return false;
        }

        if($this->payment_params->Pay2goMPG_test_mode) {
            echo print_r($dbOrder, true) . "\r\n\r\n";
        }

        $order_id = $dbOrder->order_id;

        // 1. 檢查交易狀態
        if($vars['Status'] == 'SUCCESS'){

            // 2. 檢查交易總金額
            if($dbOrder->order_full_price == $vars['Amt']){

                /**
                 *  3. 檢查 checkCode
                 */
                $check = array(
                  "MerchantID" => $vars['MerchantID'],
                  "Amt" => $vars['Amt'],
                  "MerchantOrderNo" => $vars['MerchantOrderNo'],
                  "TradeNo" => $vars['TradeNo']
                );

                ksort($check);

                $check_str = http_build_query($check);
                /**
                 * 是否有設定參數
                 */
                $checkCode = '';
                if(!isset($this->payment_params->Pay2goMPG_hash_key) || !isset($this->payment_params->Pay2goMPG_hash_iv)){
                    $content = $vars['MerchantOrderNo'] . ': Hash Setting Errpr';
                    echo $content;
                    return false;
                } else {
                    $checkCode = 'HashIV=' . $this->payment_params->Pay2goMPG_hash_iv . '&' . $check_str . '&HashKey=' . $this->payment_params->Pay2goMPG_hash_key;
                }

                $checkCode = strtoupper(hash("sha256", $checkCode));

                // 如果三次驗證都通過
                if($checkCode == $vars['CheckCode']){
                    $content = $vars['MerchantOrderNo'] . ': SUCCESS';
                    echo $content;

                    $history = new stdClass();
                    $history->notified=1;
                    $history->amount=@$vars['Amt'];
                    $history->data = ob_get_clean();

                    $order_status = $this->payment_params->Pay2goMPG_finish_status_id;
                    $this->modifyOrder($order_id,$order_status,$history,null);
                    return true;
                } else {
                    $content = $vars['MerchantOrderNo'] . ': ERROR_3';
                    echo $content;
                    return false;
                }


            } else {
                $content = $vars['MerchantOrderNo'] . ': ERROR_2';
                echo $content;
                return false;
            }

        } else {
            $content = $vars['MerchantOrderNo'] . ': ERROR_1';
            echo $content;

            $order_status = $this->payment_params->Pay2goMPG_fail_status_id;
            $this->modifyOrder($order_id, $order_status, false, null);
            return false;
        }

    }

    /**
     * 取得檢查碼
     *
     * @param array  $params    訂單參數
     *
     * @return string checkValue
     */
    protected function _getCheckValue($params)
    {
        // 要重新排序的參數
        $sortArray = array(
            'MerchantID' => $params['MerchantID'],
            'TimeStamp' => $params['TimeStamp'],
            'MerchantOrderNo' => $params['MerchantOrderNo'],
            'Version' => $params['Version'],
            'Amt' => $params['Amt'],
        );

        ksort($sortArray);

        $check_merstr = http_build_query($sortArray);

        $checkValue_str = 'HashKey=' . $this->hashKey . '&' . $check_merstr . '&HashIV=' . $this->hashIV;

        return strtoupper(hash("sha256", $checkValue_str));
    }
     
	function onPaymentConfigurationSave(&$element){
            return true;
	}

	function getPaymentDefaultValues(&$element) {
            $element->payment_name='pay2go';
            $element->payment_description='Pay2go Description';
            $element->payment_images='None';
	}

}
