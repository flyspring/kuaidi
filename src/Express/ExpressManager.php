<?php

namespace SpringExpress\Express;

/**
 * Express manager
 * @author abel
 *
 */
class ExpressManager
{
    protected $config;
    
    protected $drivers;
    
    /**
     * construct
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->config = $config;
    }
    
    /**
     * get express service by name
     * @param string $name
     * @return \SpringExpress\Express\Contracts\ExpressInterface
     */
    public function express($name = 'kdniao')
    {
        $name = $name ?: $this->getDefaultDriver();
        return $this->drivers[$name] ?? $this->drivers[$name] = $this->resolve($name);
    }
    
    /**
     * Get default driver name
     * @return string
     */
    public function getDefaultDriver()
    {
        $name = $this->getConfigByKey('default');
        return !empty($name) ? $name : 'kdniao';
    }
    
    /**
     * Get config by key
     * @param string $key
     */
    public function getConfigByKey($key)
    {
        return $this->config[$key] ?? '';
    }
    
    /**
     * Resolve express service
     * @param string $name
     * @return \bailuWorker\Services\Express\Contracts\ExpressInterface;
     */
    protected function resolve($name)
    {
        $config = $this->config[$name] ?? $this->config;
        
        switch ($name) {
            default:
                return new KuaiDiNiao($config);
        }
    }
}