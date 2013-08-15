<?php
/*
 * admin.php - admin plugin controller
 * Copyright (c) 2010  Andr? Kla?en
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

require_once 'app/controllers/authenticated_controller.php';
require_once $this->trails_root.'/models/OCModel.php';
require_once $this->trails_root.'/classes/OCRestClient/SearchClient.php';
require_once $this->trails_root.'/classes/OCRestClient/SeriesClient.php';
require_once $this->trails_root.'/classes/OCRestClient/CaptureAgentAdminClient.php';
require_once $this->trails_root.'/classes/OCRestClient/InfoClient.php';


class AdminController extends AuthenticatedController
{
    /**
     * Common code for all actions: set default layout and page title.
     */
    function before_filter(&$action, &$args)
    {
        $this->flash = Trails_Flash::instance();


        /*
        // let's init the services...
        $this->search_conf = OCRestClient::getConfig('search');
        if(isset ($this->search_conf)) {
            if(!fsockopen($this->search_conf['service_url'])) {
                //throw new Exception(_(""))
            } else {
                $this->search_client = new OCRestClient($this->search_conf['service_url'], $this->search_conf['service_user'], $this->search_conf['service_password']);
                $update = 0;
                if($allseries = $this->search_client->getAllSeries()) {
                    $tmp_series = $allseries->seriesList->series;
                    if(is_array($tmp_series)){
                        foreach ($tmp_series as $key => $serie) {
                            if(OCRestClient::storeAllSeries($serie->id)) {
                                $update+=1;
                            }
                        }
                    }
                     $this->flash['success'] = $update > 0 ? $success . sprintf(_(" Es wurden %s neue Series gefunden und hinzugef?gt."), $update) : $success;
                }
                else {
                    $this->flash['error'] = _("Es besteht momentan keine Verbindung zum Search Service");
                }
            }
        }

        $this->series_conf = OCRestClient::getConfig('series');

        if(isset ($this->series_conf)) {
            if(!fsockopen($this->series_conf['service_url'].'/series')) {
                $this->flash['error'] = _("Es besteht momentan keine Verbindung zum Series Service");
            } else {
                 $this->series_client = new OCRestClient($this->series_conf['service_url'], $this->series_conf['service_user'], $this->series_conf['service_password']);
            }
        }

        $this->captureadmin_conf = OCRestClient::getConfig('captureadmin');
        if(isset ($this->series_conf)) {
            if(!fsockopen($this->series_conf['service_url'].'/capture-admin')) {
                $this->flash['error'] = _("Es besteht momentan keine Verbindung zum Capture-Admin Service");
            } else {
                $this->captureadmin_client = new OCRestClient($this->captureadmin_conf['service_url'], $this->captureadmin_conf['service_user'], $this->captureadmin_conf['service_password']);
            }
        }

        */


        // set default layout
        $layout = $GLOBALS['template_factory']->open('layouts/base');
        $this->set_layout($layout);


    }

    /**
     * This is the default action of this controller.
     */
    function index_action()
    {

        if (isset($this->flash['message'])) {
            $this->message = $this->flash['message'];
        }


       /* $this->series = $this->occlient->getAllSeries();
        // We got all series so preserve their ids
        foreach ($this->series as $key => $serie) {
            OCRestClient::storeAllSeries($serie->seriesId[0]);
        } */

    }

    function config_action()
    {

        $GLOBALS['CURRENT_PAGE'] =  'OpenCast Administration';
        Navigation::activateItem('/admin/config/oc-config');


       // $this->info_conf = OCRestClient::setConfig('info', 'vm283.rz.uos.de:8080', 'matterhorn_system_account', 'CHANGE_ME');
 

        if (isset($this->flash['message'])) {
            $this->message = $this->flash['message'];
        }


        /**
         * TODO: add generic mechanism for the assignment of config params
         *
         */
        if( ($this->info_conf = OCRestClient::getConfig('info'))) {


            $this->info_url = $this->info_conf['service_url'];
            $this->info_user = $this->info_conf['service_user'];
            $this->info_password = $this->info_conf['service_password'];


        } else {
            /* 
            $this->search_url = 'SEARCH_ENDPOINT_URL';
            $this->search_user = 'SEARCH_ENDPOINT_USER';
            $this->search_password = '';


            $this->series_url = 'SERIES_ENDPOINT_URL';
            $this->series_user = 'SERIES_ENDPOINT_USER';
            $this->series_password = '';


            $this->scheduling_url = 'SCHEDULE_ENDPOINT_URL';
            $this->scheduling_user = 'SCHEDULE_ENDPOINT_USER';
            $this->scheduling_password = '';

            $this->captureadmin_url = 'CAPTUREADMIN_ENDPOINT_URL';
            $this->captureadmin_user = 'CAPTUREADMIN_ENDPOINT_USER';
            $this->scheduling_password = '';




            $this->series_url = 'URL_TO_MATTERHORN';
            $this->search_url = 'URL_TO_MATTERHORN';
            $this->user = 'matterhorn_system_account';
            $this->password = 'CHANGE_ME'; */
        }

        // After we've dsiplayed the server settings, we need to display the available resources and the corresponding capture agents
        //$this->resources = OCModel::getOCRessources();

    }


    function update_action()
    {
        $service_url =  parse_url(Request::get('info_url'));
        $this->info_url = $service_url['host'] . (isset($service_url['port']) ? ':' . $service_url['port'] : '') .  $service_url['path']; 

        $this->info_user = Request::get('info_user');
        $this->info_password = Request::get('info_password');
        
        OCRestClient::setConfig('info', $this->info_url, $this->info_user, $this->info_password);
       
        $info_client    = InfoClient::getInstance();
        $comp = $info_client->getRESTComponents();
        
        $services = OCModel::retrieveRESTservices($comp);
        OCRestClient::clearConfig();
        
        foreach($services as $service_type => $service_url) {
            OCRestClient::setConfig($service_type, $service_url, $this->info_user, $this->info_password);
        
        }

        $success = _("Änderungen wurden erfolgreich übernommen.");

        $this->redirect(PluginEngine::getLink('opencast/admin/config'));
    }
    /**
     * brings REST URL in one format before writing in db
     */
    function cleanClientURLs()
    {
        $urls = array('series', 'search', 'scheduling', 'ingest', 'captureadmin'
            , 'upload', 'mediapackage');
            
        foreach($urls as $pre) {
            $var = $pre.'_url';
            $this->$var = rtrim($this->$var,"/");
        }
        
    }

    function resources_action()
    {
        $GLOBALS['CURRENT_PAGE'] =  'OpenCast Administration';
        Navigation::activateItem('/admin/config/oc-resources');

        $caa_client = CaptureAgentAdminClient::getInstance();
        $this->resources = OCModel::getOCRessources();
        $this->agents = $caa_client->getCaptureAgents();
        

        
        if(empty($this->resources)) {
            $this->flash['info'] = _('Es wurden keine passenden Ressourcen gefunden.');

        }
        //$this->agents = $this->agents['agent-state-update'];
        $this->assigned_cas = OCModel::getAssignedCAS();

    }


    function update_resource_action()
    {

        $this->resources = OCModel::getOCRessources();

        foreach($this->resources as $resource) {
            if(($candidate_ca = Request::get($resource['resource_id'])) &&  Request::get('action') == 'add'){
                OCModel::setCAforResource($resource['resource_id'], $candidate_ca);
            }
        }


        $this->redirect(PluginEngine::getLink('opencast/admin/resources'));

    }

    function remove_ca_action($resource_id, $capture_agent)
    {
        OCModel::removeCAforResource($resource_id, $capture_agent);
        $this->redirect(PluginEngine::getLink('opencast/admin/resources'));
    }

    // client status
    function client_action()
    {


        //$search_client = new SearchClient();
        //$series_client = new SeriesClient();
        $caa_client    = CaptureAgentAdminClient::getInstance();
        $this->agents  = $caa_client->getCaptureAgents();
    }
}
?>
