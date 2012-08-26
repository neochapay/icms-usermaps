<?php

class p_usermaps_sosedi extends cmsPlugin {

    public function __construct(){
        parent::__construct();
        // Информация о плагине
        $this->info['plugin']           = 'p_usermaps_sosedi';
        $this->info['title']            = 'Рядом на карте';
        $this->info['description']      = 'Добавляет вкладку "Рядом" в профили всех пользователей';
        $this->info['author']           = 'Сергей Игоревич (NeoChapay)';
        $this->info['version']          = '0.1';
        $this->info['tab']              = 'Рядом'; //-- Заголовок закладки в профиле

        // Настройки по-умолчанию
        $this->config['Количество объектов'] = 10;
	$this->config['Квадрат поиска в метрах'] = 500;

        // События, которые будут отлавливаться плагином
        $this->events[]  = 'USER_PROFILE';

    }

    public function install(){
        return parent::install();
    }

    public function upgrade(){
        return parent::upgrade();
    }

    public function execute($event, $user){
        parent::execute();
        $inCore     = cmsCore::getInstance();
	$inUser     = cmsUser::getInstance();
	$inCore->loadModel('usermaps');

	$model = new cms_model_usermaps();

        $catalogs   = array();

        $user_id    = $user['id'];

        $limit      = $this->config['Количество объектов'];
	$steep	    = $this->config['Квадрат поиска в метрах'];

	$have_point = $model->getUserPlace($user_id);
	
	if($have_point)
	{
	  $points = $model->getArround($have_point['id']);
	}

	ob_start();
	if($have_point and $inUser->id == $user_id)
	{
	  $smarty= $this->inCore->initSmarty('plugins', 'p_places_sosedi.tpl');
	  $smarty->assign('total', count($points));
	  $smarty->assign('points', $points);
	  $smarty->display('p_places_sosedi.tpl');
	}
        $html = ob_get_clean();

       return $html;
    }
}

?>
