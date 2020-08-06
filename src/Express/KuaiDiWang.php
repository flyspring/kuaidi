<?php

namespace SpringExpress\Express;

use SpringExpress\Express\Contracts\ExpressInterface;

/**
 * Kuai di wang 
 * @author abel
 *
 */
class KuaiDiWang extends Express implements ExpressInterface
{
    /**
     * Create order
     * {@inheritDoc}
     * @see \SpringExpress\Express\Contracts\ExpressInterface::createOrder()
     */
    public function createOrder($expressCode, $orderId, $sender, $receiver, $options = [])
    {
        return ['status' => '0', 'message' => '暂无开通', 'order' => null];
    }
    
    /**
     * Cancel order
     * {@inheritDoc}
     * @see \SpringExpress\Express\Contracts\ExpressInterface::cancelOrder()
     */
    public function cancelOrder($expressCode, $expressNo, $orderId, $options = [])
    {
        return ['status' => '0', 'message' => '暂无开通'];
    }
    
    /**
     * Get 
     * {@inheritDoc}
     * @see \SpringExpress\Express\Contracts\ExpressInterface::getRoute()
     */
    public function queryRoute($expressCode, $expressNo, $orderId)
    {
        if (empty($expressCode) || empty($expressNo)) {
            return ['status' => '0', 'message' => '快递编码、运单号不能为空'];
        }
        
        $requestData = [
            'id' => $this->getAppId(),
            'com' => $expressCode,
            'nu' => $expressNo,
            'show' => 0,
            'muti' => 0,
            'order' => 'desc',
        ];
        
        $result = $this->getHttpClient()->get($this->getConfigByKey('query_url'), $requestData);
        
        if (empty($result) || !isset($result['success'])) {
            return ['status' => '0', 'message' => '接口返回数据有误，' . $result['message'] ?? ''];
        }
        if (!$result['success']) {
            return ['status' => '0', 'message' => '查询失败，' . $result['reason'] ?? ''];
        }
        
        $traces = [];
        if (!empty($result['data'])) {
            foreach ($result['data'] as $trace) {
                $traces[] = [
                    'remark' => $trace['Remark'] ?? '', 
                    'accept_time' => $trace['time'] ?? '', 
                    'accept_address' => $trace['context'] ?? '',
                ];
            }
        }
        
        $expressStatus = isset($result['status']) ? $this->getExpressStatus($result['status']) : 1; //0-无轨迹，1-揽件，2-在途，3-签收，4-问题件
        
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
    
    /**
     * Get express status
     * 归为：1-无轨迹，2-揽件，3-在途，4-签收，5-问题件
     * @param int $state
     */
    protected function getExpressStatus($state)
    {
        $status = 0;
        switch ($state) {
            case 0:
               $status = 0;
               break;
            case 3: //在途
            case 8: //派送
                $status = 2;
                break;
            case 4:
                $status = 1;
                break;
            case 6:
                $status = 3;
                break;
            case 5:
            case 7:
            case 9:
                $status = 4;
                break;
        }
        
        return $status;
    }
}