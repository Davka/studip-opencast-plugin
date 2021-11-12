<?php

namespace Opencast\Models\REST;

use Opencast\Models\Config;

class ApiWorkflowsClient extends RestClient
{
    public static $me;
    public        $serviceName = "ApiWorkflows";

    function __construct($config_id = 1)
    {
        parent::__construct($config_id, 'apiworkflows');
    }

    public function retract($episode_id)
    {
        $service_url = '/';
        $data = [
            'event_identifier' => $episode_id,
            'workflow_definition_identifier' => 'retract',
        ];
        list(, $code) = $this->postJSON($service_url, $data, true);

        if ($code == 201) {
            return true;
        }

        return false;
    }

    /**
     * Republish the passed episode / event
     *
     * @param  string $episode_id
     *
     * @return int   true, if the workflow could be started, false if an error occured
     *               or a workflow was already in process
     */
    public function republish($episode_id)
    {
        $service_url = "/";

        $data = [
            'event_identifier'               => $episode_id,
            'workflow_definition_identifier' => 'republish-metadata',
            'configuration'                  => '',
            'withoperations'                 => false,
            'withconfiguration'              => false
        ];

        $result = $this->postJSON($service_url, $data, true);

        if ($result[1] == 201) {
            return true;
        }

        return false;
    }
}