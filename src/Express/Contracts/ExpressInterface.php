<?php

namespace SpringExpress\Express\Contracts;

/**
 * Express interface
 * @author abel
 *
 */
interface ExpressInterface
{
    /**
     * Query Route
     * @param string $expressCode
     * @param string $expressNo
     * @param string $orderId
     * @return array $result
     */
    public function queryRoute($expressCode, $expressNo, $orderId);
    
    /**
     * Query Routes
     * @param array $dataMap = ['order_id' => $expressNo]
     * @return array $result
     */
    public function queryRoutes($dataMap);
    
    /**
     * Create order
     * @param string $expressCode
     * @param string $orderId
     * @param array $sender
     * @param array $receiver 
     * @param array $options
     * @return string $expressNo
     */
    public function createOrder($expressCode, $orderId, $sender, $receiver, $options = []);
    
    /**
     * Cancel order
     * @param string $expressCode
     * @param string $expressNo
     * @param string $orderId
     * @return array $result
     */
    public function cancelOrder($expressCode, $expressNo, $orderId, $options = []);
}