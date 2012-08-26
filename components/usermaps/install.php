<?php
    function info_component_usermaps(){
        $_component['title']        = 'Карта пользователей';
        $_component['description']  = 'Карта пользователей';
        $_component['link']         = 'usermaps';
        $_component['author']       = 'Сергей Игоревич (NeoChapay)';
        $_component['internal']     = '0';
        $_component['version']      = '0.6';

        return $_component;
    }

    function install_component_usermaps(){

        $inCore = cmsCore::getInstance();
        $inDB   = cmsDatabase::getInstance();
        $inConf = cmsConfig::getInstance();

        include($_SERVER['DOCUMENT_ROOT'].'/includes/dbimport.inc.php');
        dbRunSQL($_SERVER['DOCUMENT_ROOT'].'/components/usermaps/install.sql', $inConf->db_prefix);

        if ($inCore->isComponentInstalled('billing')){
        dbRunSQL($_SERVER['DOCUMENT_ROOT'].'/components/usermaps/billing.sql', $inConf->db_prefix);
        }

        return true;

    }

    function upgrade_component_usermaps(){

        $inCore = cmsCore::getInstance();
        $inDB   = cmsDatabase::getInstance();
        $inConf = cmsConfig::getInstance();

        include($_SERVER['DOCUMENT_ROOT'].'/includes/dbimport.inc.php');
        dbRunSQL($_SERVER['DOCUMENT_ROOT'].'/components/usermaps/update.sql', $inConf->db_prefix);

        return true;
    }

?>