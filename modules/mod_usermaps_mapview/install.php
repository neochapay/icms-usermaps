<?php

    function info_module_mod_usermaps_mapview(){
        $_module['title']        = 'Карта';
        $_module['name']         = 'Карта пользователей';
        $_module['description']  = 'Показывает карту с пользователями.';
        $_module['link']         = 'mod_usermaps_mapview';
        $_module['position']     = 'maintop';
        $_module['author']       = 'Сергей Игоревич (NeoChapay)';
        $_module['version']      = '0.5';

        $_module['config'] = array();

        return $_module;

    }

    function install_module_mod_usermaps_mapview(){
        return true;
    }

    function upgrade_module_mod_usermaps_mapview(){
        return true;
    }

?>