<?php
    define('_JEXEC', 1);

    require('../../../configuration.php');

    mb_internal_encoding('utf-8');

    $config = new JConfig();
    $conn = mysql_connect($config->host, $config->user, $config->password);

    mysql_select_db($config->db, $conn);

    $sql = "SET CHARACTER SET utf8";
    mysql_set_charset('utf8');

    // 讀出後台參數
    $sql = "SELECT payment_params FROM ".$config->dbprefix."hikashop_payment WHERE payment_type='pay2go'";
    $pay2go_config_params = mysql_fetch_row(mysql_query($sql)); 

    // 去掉大括號前端字串
    $split_brackets = strstr($pay2go_config_params[0],"{"); 

    // 根據分號切開參數 (第一維度)
    $split_semicolon = split(";", $split_brackets); 

    // 根據冒號切開參數 (第二維度)
    $split_real = array();

    for ($i = 0 ; $i <= count($split_semicolon)-1 ; $i++){
        $split_real[$i] = split(":", $split_semicolon[$i]);  
    }

    /**
     * 實際需要參數 : 根據參數的屬性與其屬性值存入一為陣列作對應,且去掉 " 符號
     */
    $pay2go_configs = array();
    for ($i=0 ; $i<count($split_real) ; $i+=2){
        $pay2go_configs[str_replace('"', '', $split_real[$i][2])] = str_replace('"', '', $split_real[$i+1][2]);
    }

    // 交易狀態
    $result = $_POST;

    // 取得該筆交易資料
    $sql = "SELECT * FROM ".$config->dbprefix."hikashop_order WHERE order_id = " . $result['MerchantOrderNo'];
    $order_info = mysql_fetch_array(mysql_query($sql), MYSQL_ASSOC); 

    // 取時間做檔名 (YYYYMMDD)
    $file_name = date('Ymd', time()) . '.txt';

    // 檔案路徑
    $file = $config->log_path . "/" . $file_name;

    $fp = fopen($file, 'a');

    // 是否有資料
    if (!empty($order_info)){

        // 1. 檢查交易狀態
        if($result['Status'] == 'SUCCESS'){

            // 2. 檢查交易總金額
            if($order_info['order_full_price'] == $result['Amt']){    

                /**
                 *  3. 檢查 checkCode
                 */                    
                $check = array(
                    "MerchantID" => $result['MerchantID'],
                    "Amt" => $result['Amt'],
                    "MerchantOrderNo" => $result['MerchantOrderNo'],
                    "TradeNo" => $result['TradeNo']  
                );                   
                
                ksort($check);

                $check_str = http_build_query($check);    
                
                /**
                 * 是否有設定參數
                 */
                $checkCode = '';

                if(!isset($pay2go_configs['Pay2goMPG_hash_key']) || !isset($pay2go_configs['Pay2goMPG_hash_iv'])){
                    $content = $result['MerchantOrderNo'] . ': Hash Setting Errpr';
                    fwrite($fp, $content . "\n");  
                    fclose($fp);
                    echo $content;
                    die;
                } else {
                    $checkCode = 'HashIV=' . $pay2go_configs['Pay2goMPG_hash_iv'] . '&' . $check_str . '&HashKey=' . $pay2go_configs['Pay2goMPG_hash_key'];
                }                
                
                $checkCode = strtoupper(hash("sha256", $checkCode));    
                
                // 如果三次驗證都通過
                if($checkCode == $result['CheckCode']){
                                        
                    if($order_info['order_status'] != $pay2go_configs['Pay2goMPG_finish_status_id']){

                        $today = time();

                        // 修改訂單狀態
                        $update_sql = updateSql($result['MerchantOrderNo'], $pay2go_configs['Pay2goMPG_finish_status_id'], $today, $config->dbprefix);
                        mysql_query($update_sql);

                        // 新增歷史紀錄
                        $insert_sql = addSql($result, $pay2go_configs['Pay2goMPG_finish_status_id'], $today, $config->dbprefix);
                        mysql_query($insert_sql);                        
                        
                    }                        

                } else {
                    $content = $result['MerchantOrderNo'] . ': ERROR_3';
                    fwrite($fp, $content . "\n");  
                    fclose($fp);
                    echo $content;
                    die;
                }                
                
            } else {

                $content = $result['MerchantOrderNo'] . ': ERROR_2';
                fwrite($fp, $content . "\n");  
                fclose($fp);
                echo $content;
                die;                 
                
            }

        } else {

            $content = $result['MerchantOrderNo'] . ': ERROR_1';
            echo $content;

            fwrite($fp, $content . "\n");  
            
            // 修改訂單狀態 && 新增歷史紀錄 (Only Credit or WebAtm)
            if(in_array($result['PaymentType'], array('CREDIT', 'WEBATM'))){

                $today = time();
                
                // 修改訂單狀態
                $update_sql = updateSql($result['MerchantOrderNo'], $pay2go_configs['Pay2goMPG_fail_status_id'], $today, $config->dbprefix);
                mysql_query($update_sql);                
                
                // 新增歷史紀錄
                $insert_sql = addSql($result, $pay2go_configs['Pay2goMPG_fail_status_id'], $today, $config->dbprefix);
                mysql_query($insert_sql);
                
            }

            fclose($fp);
            die;            
            
        }

    } else {
        fwrite($fp, $result['MerchantOrderNo'] . ": DataError\n");
    }

    fclose($fp);
    die;
    
    /**
     * 更新語法
     * 
     * @param string $order_id      更新資料
     * @param string $update_status 更新狀態
     * @param int    $today         更新時間
     * @param string $prefix        DB 前置
     */    
    function updateSql($order_id, $update_status, $today, $prefix)
    {
        $sql  = "UPDATE " . $prefix . "hikashop_order SET";
        $sql .= " order_status = '" . $update_status . "'";
        $sql .= ", order_modified = " . $today;
        $sql .= " WHERE order_id = " . $order_id;        

        return $sql;
    }
    
    /**
     * 新增歷史語法
     * 
     * @param string  $returnResult  pay2go 回傳資料
     * @param string  $add_status    新增狀態
     * @param int     $today         更新時間
     * @param string  $prefix        DB 前置
     */    
    function addSql($returnResult, $add_status, $today, $prefix)
    {
        $sql  = "INSERT INTO " . $prefix . "hikashop_history SET";
        $sql .= " history_order_id = " . $returnResult['MerchantOrderNo'];
        $sql .= ", history_created = " . $today;
        $sql .= ", history_ip = 'pay2go'";
        $sql .= ", history_new_status = '" . $add_status . "'";
        $sql .= ", history_reason = '" . getComment($returnResult);
        $sql .= ($returnResult['Status'] != 'SUCCESS') ? "錯誤訊息: " . $returnResult['Message'] . "'"  : "'";
        $sql .= ", history_type = 'callback'";       

        return $sql;
    }    

    /**
     * 訂單備註
     * 
     * @param array $returnResult  pay2go 回傳資料
     */
    function getComment($returnResult)
    {
        $result = '';
        
        $paymentTransform = array(
            'CREDIT'    =>  '信用卡',
            'WEBATM'    =>  'WebATM',
            'VACC'      =>  'ATM轉帳',
            'CVS'       =>  '超商代碼繳費',
            'BARCODE'   =>  '條碼繳費',
        );
        
        switch ($returnResult['PaymentType']){
            
            case 'CREDIT':
                
                $result .= '繳費方式: '.$paymentTransform[$returnResult['PaymentType']].'<br />';
                $result .= '銀行回應碼: ' . $returnResult['RespondCode'] . '<br />';
                $result .= '銀行授權碼: ' . $returnResult['Auth'] . '<br />';
                $result .= '卡號前六碼: ' . $returnResult['Card6No'] . '<br />';
                $result .= '卡號末四碼: ' . $returnResult['Card4No'] . '<br />';
                break;
            
            case 'WEBATM':
            case 'VACC':
                
                $result .= '繳費方式: '.$paymentTransform[$returnResult['PaymentType']].'<br />';
                $result .= '付款人金融機構代碼: ' . $returnResult['PayBankCode'] . '<br />';
                $result .= '付款人金融機構帳號末五碼: ' . $returnResult['PayerAccount5Code'] . '<br />';                
                break;
            
            case 'CVS':
                
                $result .= '繳費方式: '.$paymentTransform[$returnResult['PaymentType']].'<br />';
                $result .= '繳費代碼: ' . $returnResult['CodeNo'] . '<br />';
                break;                
                
            case 'BARCODE':
                
                $result .= '繳費方式: '.$paymentTransform[$returnResult['PaymentType']].'<br />';
                $result .= '第一段條碼: ' . $returnResult['Barcode_1'] . '<br />';
                $result .= '第二段條碼: ' . $returnResult['Barcode_2'] . '<br />';
                $result .= '第三段條碼: ' . $returnResult['Barcode_3'] . '<br />';
                break;			
			            
            default :
                break;
        }
                
        return $result;
    }    
    
?>