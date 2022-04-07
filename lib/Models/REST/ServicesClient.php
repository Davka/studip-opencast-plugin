<?php

namespace Opencast\Models\REST;

use Opencast\Models\Config;

class ServicesClient extends RestClient
{
    public static $me;

    public function __construct($config_id = 1)
    {
        $this->serviceName = 'ServicesClient';

        if ($config = Config::getConfigForService('services', $config_id)) {
            parent::__construct($config);
        } else {
            throw new \Exception ($this->serviceName . ': '
                . _('Die Opencast-Konfiguration wurde nicht korrekt angegeben'));
        }
    }

    /**
     * getComponents() - retrieves episode system components from conntected Opencast-Matterhorn Core
     *
     *  @return array response of components
     */
    function getRESTComponents()
    {
        $service_url = "/services.json";

        if ($result = $this->getJSON($service_url)) {
            return $result['services']['service'];
        } else {
            return false;
        }
    }
}