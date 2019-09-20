<?php

namespace SpringExpress\Express;

use SpringExpress\Http\HttpClient;

/**
 * Express
 * @author abel
 *
 */
class Express 
{
    /**
     * driver name
     * @var string
     */
    protected $driverName;
    
    /**
     * http client
     * @var HttpClient
     */
    protected $httpClient;
    
    /**
     * config
     * @var array
     */
    protected $config;
    
    /**
     * Construct
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->setConfig($config);
    }
    
    /**
     * Get config
     */
    public function getConfig()
    {
        return $this->config;
    }
    
    /**
     * Set config
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }
    
    /**
     * Get config by key
     * @param string $key
     */
    public function getConfigByKey($key)
    {
        return $this->config[$key] ?? null;
    }
    
    /**
     * Get app id
     */
    public function getAppId()
    {
        return $this->getConfigByKey('app_id');
    }
    
    /**
     * Get app key or secrect
     */
    public function getAppKey()
    {
        return $this->getConfigByKey('app_key');
    }
    
    /**
     * Get http client
     * @return \SpringExpress\Http\HttpClient
     */
    public function getHttpClient()
    {
        return $this->httpClient ?: $this->httpClient = new HttpClient();
    }
    
    /**
     * Check sender or receiver
     * @param array $data
     * @param int $type 1-sender, 2-receiver
     * @return array
     */
    public function checkSenderOrReceiver($data, $type)
    {
        $columns = [
            'name' => '姓名不能为空',
            'province' => '省份不能为空',
            'city' => '城市不能为空',
            'district' => '区县不能为空', 
            'address' => '详细地址不能为空',
        ];
        
        $status = '1';
        $msg = 'ok';
        foreach ($columns as $key => $value) {
            if (empty($data[$key])) {
                $status = '0';
                $msg = $value;
                break;
            }
        }
        
        if ($status == '1') {
            if (empty($data['mobile']) && empty($data['tel'])) {
                $status = '0';
                $msg = '手机号或电话不能都为空';
            }
        }
        
        $msg = ($type == 1 ? '发件方' : '收件方') . $msg;
        
        return ['status' => $status, 'message' => $msg];
    }
}