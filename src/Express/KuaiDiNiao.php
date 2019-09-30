<?php

namespace SpringExpress\Express;

use SpringExpress\Express\Contracts\ExpressInterface;

/**
 * Kuai di niao 
 * @author abel
 *
 */
class KuaiDiNiao extends Express implements ExpressInterface
{
    /**
     * Create order
     * {@inheritDoc}
     * @see \SpringExpress\Express\Contracts\ExpressInterface::createOrder()
     */
    public function createOrder($expressCode, $orderId, $sender, $receiver, $options = [])
    {
        //快递公司编码为空
        if (empty($expressCode)) {
            return ['status' => '0', 'message' => '快递公司编码为空'];
        }
        
        //订单验证
        if (empty($orderId)) {
            return ['status' => '0', 'message' => '订单编号不能为空'];
        }
        
        //收发件人验证
        $ret = $this->checkSenderOrReceiver($sender, 1);
        if ($ret['status'] == '0') {
            return $ret;
        }
        
        $ret = $this->checkSenderOrReceiver($receiver, 2);
        if ($ret['status'] == '0') {
            return $ret;
        }
        
        //$sender => $from
        $from = [
            'Company' => $sender['company'] ?? '',
            'Name' => $sender['name'], 
            'Mobile' => $sender['mobile'] ?? '',
            'Tel' => $sender['tel'] ?? '', 
            'ProvinceName' => rtrim($sender['province'], '市'), 
            'CityName' => $sender['city'], 
            'ExpAreaName' => $sender['district'],
            'Address' => $sender['address'],
            'PostCode' => $sender['post_code'] ?? '',
        ];
        
        //receiver => $to
        $to = [
            'Company' => $receiver['company'] ?? '',
            'Name' => $receiver['name'],
            'Mobile' => $receiver['mobile'] ?? '',
            'Tel' => $receiver['tel'] ?? '',
            'ProvinceName' => rtrim($receiver['province'], '市'),
            'CityName' => $receiver['city'],
            'ExpAreaName' => $receiver['district'],
            'Address' => $receiver['address'],
            'PostCode' => $receiver['post_code'] ?? '',
        ];
        
        //commodity
        $commodity = [];
        if (!empty($options['commodity'])) {
            if (isset($options['commodity']['name'])) {
                $commodity[] = ['GoodsName' => $options['commodity']['name']];
            } else {
                foreach ($options['commodity'] as $val) {
                    if (is_array($val) && !empty($val['name'])) {
                        $commodity[] = ['GoodsName' => $val['name']];
                    }
                }
            }
        }
        if (empty($commodity)) {
            $commodity[] = ['GoodsName' => '中药'];
        }
        
        //AddServices
        
        $requestData = [
            'ShipperCode' => $expressCode,
            'OrderCode' => $orderId,
            'PayType' => $options['pay_type'] ?? 1, //邮费支付方式:1-现付，2-到付，3-月结，4-第三方支付
            'ExpType' => $options['exp_type'] ?? 1, //快递类型：1-标准快件，其它量看文档
            'TransType' => $options['trans_type'] ?? 1, //运输方式 1-陆运  2-空运 不填默认为1
            'IsNotice' => $options['is_notice'] ?? 1, //是否通知快递员上门揽件 0-通知, 1-不通知 不填则默认为1
            'StartDate' => $options['start_date'] ?? '', //上门取件时间点, "yyyy-MM-dd HH:mm:ss"
            'EndDate' => $options['end_date'] ?? '', //上门取件时间点, "yyyy-MM-dd HH:mm:ss"
            'CustomerName' => $options['customer_name'] ?? '',
            'CustomerPwd' => $options['customer_pwd'] ?? '',
            'MonthCode' => $options['month_code'] ?? '',
            'IsReturnPrintTemplate' => $options['need_print_tpl'] ?? 0,
            'Remark' => $options['remark'] ?? '', //备注
            'Sender' => $from,
            'Receiver' => $to,
            'Commodity' => $commodity,
        ];
        
        $jsonReqData = json_encode($requestData, JSON_UNESCAPED_UNICODE);
        
        $orderData = [
            'EBusinessID' => $this->getAppId(),
            'RequestType' => '1007',
            'RequestData' => urlencode($jsonReqData),
            'DataType' => '2',
            'DataSign' => $this->makeSign($jsonReqData),
        ];
        
        $result = $this->getHttpClient()->post($this->getConfigByKey('create_url'), $orderData);
        
        if (isset($result['status']) && $result['status'] == '0') {
            return $result;
        }
        
        if (empty($result) || !isset($result['Success'])) {
            return ['status' => '0', 'message' => '接口返回数据有误'];
        }
        if (!$result['Success'] || !isset($result['Order'])) {
            return ['status' => '0', 'message' => $result['Reason'] ?? '接口返回结果为失败'];
        }
        
        //结果order
        $resultOrder = [
            'order_id' => $orderId, 
            'express_code' => $expressCode, 
            'express_no' => $result['Order']['LogisticCode'] ?? '',
            'dest_code' => $result['Order']['DestinatioCode'] ?? '', 
            'print_tpl' => $result['PrintTemplate'] ?? '',
        ];
        
        return ['status' => '1', 'message' => 'success', 'order' => $resultOrder];
    }
    
    /**
     * Cancel order
     * {@inheritDoc}
     * @see \SpringExpress\Express\Contracts\ExpressInterface::cancelOrder()
     */
    public function cancelOrder($expressCode, $expressNo, $orderId, $options = [])
    {
        if (empty($expressCode) || empty($expressNo) || empty($orderId)) {
            return ['status' => '0', 'message' => '快递编码、运单号、订单编号不能为空'];
        }
        
        $requestData = [
            'ShipperCode' => $expressCode,
            'OrderCode' => $orderId, 
            'ExpNo' => $expressNo, 
            'CustomerName' => $options['customer_name'] ?? '',
            'CustomerPwd' => $options['customer_pwd'] ?? '', 
        ];
        
        $jsonReqData = json_encode($requestData, JSON_UNESCAPED_UNICODE);
        
        $orderData = [
            'EBusinessID' => $this->getAppId(),
            'RequestType' => '1147',
            'RequestData' => urlencode($jsonReqData),
            'DataType' => '2',
            'DataSign' => $this->makeSign($jsonReqData),
        ];
        
        $result = $this->getHttpClient()->post($this->getConfigByKey('create_url'), $orderData);
        
        if (isset($result['status']) && $result['status'] == '0') {
            return $result;
        }
        
        if (empty($result) || !isset($result['Success'])) {
            return ['status' => '0', 'message' => '接口返回数据有误'];
        }
        if (!$result['Success']) {
            return ['status' => '0', 'message' => $result['Reason'] ?? '接口返回结果失败'];
        }
        
        return ['status' => '1', 'message' => '取消成功'];
    }
    
    /**
     * Get 
     * {@inheritDoc}
     * @see \SpringExpress\Express\Contracts\ExpressInterface::getRoute()
     */
    public function queryRoute($expressCode, $expressNo, $orderId)
    {
        if (empty($expressCode) || empty($expressNo) || empty($orderId)) {
            return ['status' => '0', 'message' => '快递编码、运单号、订单编号不能为空'];
        }
        
        $requestData = [
            'ShipperCode' => $expressCode,
            'OrderCode' => $orderId,
            'LogisticCode' => $expressNo,
        ];
        
        $jsonReqData = json_encode($requestData, JSON_UNESCAPED_UNICODE);
        
        $orderData = [
            'EBusinessID' => $this->getAppId(),
            'RequestType' => '1002',
            'RequestData' => urlencode($jsonReqData),
            'DataType' => '2',
            'DataSign' => $this->makeSign($jsonReqData),
        ];
        
        $result = $this->getHttpClient()->post($this->getConfigByKey('query_url'), $orderData);
        
        if (isset($result['status']) && $result['status'] == '0') {
            return $result;
        }
        
        if (empty($result) || !isset($result['Success'])) {
            return ['status' => '0', 'message' => '接口返回数据有误'];
        }
        if (!$result['Success'] || $result['Reason'] != null) {
            return ['status' => '0', 'message' => $result['Reason'] ?: '接口返回结果失败'];
        }
        
        $traces = [];
        if (!empty($result['Traces'])) {
            foreach ($result['Traces'] as $trace) {
                $traces[] = [
                    'remark' => $trace['Remark'] ?? '', 
                    'accept_time' => $trace['AcceptTime'] ?? '', 
                    'accept_address' => $trace['AcceptStation'] ?? '',
                ];
            }
        }
        
        $expressStatus = isset($result['State']) ? intval($result['State']) : 0; //0-无轨迹，1-揽件，2-在途，3-签收，4-问题件
        
        return ['status' => '1', 'message' => 'success', 'traces' => $traces, 'express_status' => $expressStatus];
    }

    public function queryRoutes($dataMap)
    {
        
    }
    
    /**
     * Make sign
     * @param string $requestData
     */
    protected function makeSign($requestData)
    {
        return urlencode(base64_encode(md5($requestData . $this->getAppKey())));
    }
}