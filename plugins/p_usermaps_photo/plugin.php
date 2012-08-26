<?php
class p_usermaps_photo extends cmsPlugin
{
  public function __construct()
  {

    parent::__construct();

    $this->info['plugin']           = 'p_usermaps_photo';
    $this->info['title']            = 'Фото на карте';
    $this->info['description']      = 'Позволяет привязать фотографию к точке на карте';
    $this->info['author']           = 'NeoChapay';
    $this->info['version']          = '0.6';
    $this->events[]                 = 'GET_PHOTO';
  }

  public function install()
  {
    return parent::install();
  }

  public function upgrade()
  {
    return parent::upgrade();
  }

  public function execute($event, $item)
  {
    parent::execute();
    switch ($event)
    {
      case 'GET_PHOTO':
	$item['map'] = $this->photomap($item);
	break;
    }
    return $item;
  }

  public function photomap($photo)
  {
    $inCore     = cmsCore::getInstance();
    $inUser     = cmsUser::getInstance();
    $type 	= "photo";

    if($photo['user_id'] == $inUser->id)
    {
      $is_author = TRUE;
    }

    $sql = mysql_query("SELECT * FROM cms_places_events WHERE `object_id` = '".$photo['id']."' AND `object_type` = '$type'");
    $cfg = $inCore->loadComponentConfig('usermaps');

    ob_start();
    if(mysql_num_rows($sql) == 1 or $is_author)
    {
      $point = mysql_fetch_assoc($sql);

      if($point['x'] == "" or $point['y'] == "")
      {
	$center = $cfg['maps_center'];
	$have_point = FALSE;
      }
      else
      {
	$center = '"'.$point['x'].'","'.$point['y'].'"';
	$have_point = TRUE;
      }

      $smarty= $this->inCore->initSmarty('plugins', 'p_places_imagesonmap.tpl');
      $smarty->assign('is_author', $is_author);
      $smarty->assign('photo_id', $photo['id']);
      $smarty->assign('photo_type', $type);
      $smarty->assign('center', $center);
      $smarty->assign('cfg', $cfg);
      $smarty->assign('have_point', $have_point);
      $smarty->display('p_places_imagesonmap.tpl');
    }
    $html = ob_get_clean();

    return $html;
  }
}