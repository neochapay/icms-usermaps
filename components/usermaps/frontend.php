<?php
function usermaps()
{
  $inCore = cmsCore::getInstance();
  $inPage = cmsPage::getInstance();
  $inUser = cmsUser::getInstance();

  $inCore->loadModel('usermaps');

  $model = new cms_model_usermaps();

  $do = $inCore->request('do', 'str', 'poi_list');

  $cfg = $inCore->loadComponentConfig('usermaps');

  if ($do == 'mainmap')
  {
    $inPage->setTitle("Карта пользователей");
    
    $cfg['maps_engine'] = strtolower($cfg['maps_engine']);
    if($cfg['maps_engine'] == "pmap")
    {
      $cfg['maps_engine'] = "publicMap";
    }
  
    if($cfg['maps_engine'] == "phybrid")
    {
      $cfg['maps_engine'] = "publicMapHybrid";
    }
////Центр карты
    $new_center = explode(",", $cfg['maps_center']);
    $cfg['maps_center'] = $new_center['1']." ,".$new_center['0'];
  
// Настройки конкретного пользователя
    if($inUser->id != 0)
    {
      if($place = $model->getUserPlace($inUser->id))
      {
	//print_r($place);
	$cfg['maps_center'] = $place['y']." ,".$place['x'];
	$cfg['main_zoom'] = $cfg['main_zoom']+1;
	$have_userplace = 1;
      }
    }
//Загружаем структуру точек
//    $structure = $model->StructureOfPoints();
  
    $smarty = $inCore->initSmarty('modules', 'mod_usermaps_mapview.tpl');
    $smarty->assign('cfg', $cfg);
    $smarty->assign('user_id', $inUser->id);
    $smarty->assign('structure', $structure);
    $smarty->assign('have_userplace', $have_userplace);
    $smarty->display('mod_usermaps_mapview.tpl');
  }

  if ($do == 'imagemap')
  {
    $inPage->setTitle("Фото на карте");
    
    $cfg['maps_engine'] = strtolower($cfg['maps_engine']);
    if($cfg['maps_engine'] == "pmap")
    {
      $cfg['maps_engine'] = "publicMap";
    }
  
    if($cfg['maps_engine'] == "phybrid")
    {
      $cfg['maps_engine'] = "publicMapHybrid";
    }
////Центр карты
    $new_center = explode(",", $cfg['maps_center']);
    $cfg['maps_center'] = $new_center['1']." ,".$new_center['0'];
    
    $photos = $model->ImagesOnMap();
    
    $smarty = $inCore->initSmarty('modules', 'mod_usermaps_imagesview.tpl');
    $smarty->assign('cfg', $cfg);
    $smarty->assign('photos', $photos);
    $smarty->display('mod_usermaps_imagesview.tpl');  
  }
  
  if ($do == 'add')
  {
    $inPage->setTitle("Добавить себя");
    $is_send = $inCore->inRequest('coord');
    $user_id = $inUser->id;
    if($user_id == 0)
    {
      $inCore->redirect('/');
      return;
    }
    $place = $model->getUserPlace($user_id);
    if($place)
    {
      $inCore->redirect('/usermaps/edit'.$place['id'].'.html');
      return;
    }
    if (!$is_send)
    {
      $smarty = $inCore->initSmarty('components', 'com_places_add.tpl');
      $smarty->assign('cfg', $cfg);
      $smarty->display('com_places_add.tpl');
      return;
    }

    if ($is_send)
    {
      $coord_raw = $inCore->request('coord', 'str');
      $cat_id = $inCore->request('type', 'int');
      $coord = explode(",",$coord_raw);
      $x = $coord[0];
      $y = $coord[1];
      if($cat_id == "")
      {
	$cat_id = 1;
      }
      $place_id = $model->addPlace($user_id, $x, $y, $cat_id);

      if ($place_id)
      {
	if($cat_id == "1")
	{
	  cmsActions::log('add_place', array(
                'object' => 'себя на карту',
                'object_url' => '/usermaps/view'.$place_id.'.html',
                'object_id'=> $place_id,
                'target' => '',
                'target_url' => '/usermaps/view'.$place_id.'.html',
                'target_id' => 0,
                'description' => ''));
	}
	cmsCore::addSessionMessage('Ваше местоположение добавлено!', 'success');
	$inCore->redirect('/usermaps/edit'.$place_id.'.html');
	return;
      }
      else
      {
	cmsCore::addSessionMessage('Ошибка добавления! '.$place_id. ' ', 'error');
      }
      $inCore->redirect('/usermaps/edit'.$place['id'].'.html');
      exit;
    }
  }

  if($do == 'edit')
  {
    $user_id = $inUser->id;
    $place_id = $inCore->request('id', 'int', 0);
    $is_send = $inCore->inRequest('coord');

    if (!$place_id)
    {
      cmsCore::addSessionMessage('Ошибка запроса! '.$place_id. ' ', 'error');
    }
    else
    {
      $place = $model->getPlace($place_id);
    }

    if ($inUser->id == 0)
    {
      cmsCore::addSessionMessage('Ошибка запроса! '.$place_id. ' ', 'error');
      $inCore->redirect('/');
    }

    if (!$place || $inUser->id != $place['user_id'])
    {
      if(!$inUser->is_admin)
      {
	cmsCore::addSessionMessage('Ошибка запроса! '.$place_id. ' ', 'error');
	$inCore->redirectBack();
	exit;
      }
    }
//Если редактирующий админ и это не его точка сохраняем автора точки
    if($inUser->is_admin and $user_id != $place['user_id'])
    {
      $user_id = $place['user_id'];
    }

    if ($is_send)
    {
      $coord_raw = $inCore->request('coord', 'str');
      $title = $inCore->request('title', 'str');
      $body = $inCore->request('body', 'str');
      $cat_id = $inCore->request('cat_id', 'str');
      if($cat_id == "")
      {
	$cat_id = 1;
      }
      $coord = explode(",",$coord_raw);
      $x = $coord[0];
      $y = $coord[1];
      $point = $model->updatePlace($place['id'],$user_id, $x,$y,$cat_id,$title, $body);
      if($point)
      {
	if($place['type_id'] == "1" AND mysql_result(mysql_query("SELECT target_url FROM cms_actions_log ORDER BY id DESC LIMIT 1"),0) != '/usermaps/view'.$place['id'].'.html')
	{
	  cmsActions::log('edit_place', array(
                'object' => 'своего местоположения',
                'object_url' => '/usermaps/view'.$place['id'].'.html',
                'object_id'=> $place['id'],
                'target' => '',
                'target_url' => '/usermaps/view'.$place['id'].'.html',
                'target_id' => 0,
                'description' => ''));
	}
	cmsCore::addSessionMessage('Местоположение вашей точки изменено!', 'success');
      }
      else
      {
	cmsCore::addSessionMessage('Ошибка добавления! '.$place['id']. ' ', 'error');
      }
      $inCore->redirect('/usermaps/edit'.$place['id'].'.html');
      exit;
    }

    if(!$is_send)
    {
          $poi = $model->getPoi($place['type_id']);
    if(!$poi)
    {
      $icon = "unknow";
    }
    else
    {
      $icon = $poi['name']."_big";
    }
      $inPage->setTitle("Редактирование");
      $categores = $model->getCategores(NULL);
      $smarty = $inCore->initSmarty('components', 'com_places_edit.tpl');
      $smarty->assign('cfg', $cfg);
      $smarty->assign('place', $place);
      $smarty->assign('categores', $categores);
      $smarty->assign('icon', $icon);
      $smarty->display('com_places_edit.tpl');
      return;
    }
  }

  if($do == "delete")
  {
    $id = $inCore->request('id', 'int', 0);
    $place = $model->getPlace($id);
    if(!$place)
    {
      $inCore->redirectBack();
      return;
    }
    if($place['user_id'] == $inUser->id or $inUser->is_admin)
    {
      $delete = $model->deletePlace($place['id']);
      if($delete)
      {
	cmsCore::addSessionMessage('Точка удалена', 'success');
	$inCore->redirect('/usermaps/poi.html');
	return;
      }
      else
      {
	cmsCore::addSessionMessage('Ошибка при удалении', 'success');
	$inCore->redirectBack();
	return;
      }
    }
    else
    {
      $inCore->redirectBack();
      return;
    }
  }

  if($do == "view")
  {
    $id = $inCore->request('id', 'int', 0);
    $user_id = $inUser->id;

    $place = $model->getPlace($id);
//Если точки нет отправляем назад
    if(!$place)
    {
      $inCore->redirectBack();
      return;
    }
//Получаем описание категории и стиль иконки
    $poi = $model->getPoi($place['type_id']);
    if(!$poi)
    {
      $icon = "unknow";
    }
    else
    {
      $icon = $poi['name']."_big";
    }
//Если нужно чертить треки ищем пользовательскую точку
    if($place['type_id'] != "1" and $user_id != 0)
    {
      $userplace = $model->getUserPlace($user_id);
    }
//Если пользовательская точка то заголовком делаем имя пользователя
    if($place['type_id'] == "1")
    {
      $user = $model->getUser($place['user_id']);
      $title = $user['nickname'];
    }
    else
    {
      $title = $place['title'];
    }
//Назначаем переменные
    $author = $model->getUser($place['user_id']);
    $category = $model->getCategory($place['type_id']);
    $arround = $model->getArround($place["id"]);
//Если валаделец или админ говорим что автор и может редактировать
    if($place['user_id'] == $user_id or $inUser->is_admin)
    {
      $is_author = TRUE;
    }
//Проверяем чекины
    if($cfg['maps_chekin'])
    {
      $checkin = $model->getChekin($place['id']);
      $usercheck = $model->getUserChekin($place['id'],$inUser->id);
    }
    
    /*FOTOLIB*/
    include('fotolib.class.php');
    $foto = new FotoLib();
    //Проверяем можем ли добавлять фото
    $allow_add_foto = $foto->addAcces("usermaps");
  
    if($_FILES)
    {
      $foto->uploadFoto($_FILES, "usermaps", $place['id']);
    }
//Для совместимости с YandexMap API v2
    $cfg['maps_engine'] = strtolower($cfg['maps_engine']);
    if($cfg['maps_engine'] == "pmap")
    {
      $cfg['maps_engine'] = "publicMap";
    }
  
    if($cfg['maps_engine'] == "phybrid")
    {
      $cfg['maps_engine'] = "publicMapHybrid";
    }
//END
    $images = $foto->loadImages("usermaps",$place['id']);
    
    $inPage->setTitle($title);
    $smarty = $inCore->initSmarty('components', 'com_places_view.tpl');
    $smarty->assign('cfg', $cfg);
    $smarty->assign('place', $place);
    $smarty->assign('userplace', $userplace);
    $smarty->assign('checkin', $checkin);
    $smarty->assign('usercheck', $usercheck);
    $smarty->assign('icon', $icon);
    $smarty->assign('title', $title);
    $smarty->assign('author', $author);
    $smarty->assign('category', $category);
    $smarty->assign('is_author', $is_author);
    $smarty->assign('images', $images); //fotolib
    $smarty->assign('allow_add_foto', $allow_add_foto); //fotolib    
    $smarty->assign('user', $model->getUser($inUser->id));
    $smarty->assign('arround', $arround);
    $smarty->display('com_places_view.tpl');
    $inCore->includeComments();
    comments('point', $id);

    return;
  }

  if($do == "userpoint")
  {
    $user_id = $inUser->id;
    if($user_id == 0)
    {
      $inCore->redirectBack();
      return;
    }
    $uid = $inCore->request('uid', 'int', 0);
    $userplace = $model->getUserPlace($uid);
    if(!$userplace)
    {
      $inCore->redirectBack();
      return;
    }
    $inCore->redirect("/usermaps/view".$userplace['id'].".html");
    return;
  }
// РАБОТА С POI
  if($do == "poi_list")
  {
    $inPage->setTitle("Последние добавленные точки интересов");
    $poi = $model->getAllPoi(NULL);
    $smarty = $inCore->initSmarty('components', 'com_places_add.tpl');
    $smarty->assign('cfg', $cfg);
    $smarty->assign('poi', $poi);
    $smarty->display('com_places_view_poi.tpl');
    return;
  }

  if($do == "poi_add")
  {
    $is_send = $inCore->inRequest('coord');
    $user_id = $inUser->id;

    if($user_id == 0)
    {
      $inCore->redirect("/");
      return;
    }

    if (!$is_send)
    {
      $categores = $model->getCategores(NULL);
      $inPage->setTitle("Добавить POI");
      $smarty = $inCore->initSmarty('components', 'com_places_add_poi.tpl');
      $smarty->assign('cfg', $cfg);
      $smarty->assign('categores', $categores);
      $smarty->display('com_places_add_poi.tpl');
      return;
    }

    if ($is_send)
    {
      $coord_raw = $inCore->request('coord', 'str');
      $cat_id = $inCore->request('cat_id', 'int');
      $coord = explode(",",$coord_raw);
      $x = $coord[0];
      $y = $coord[1];
      $title = $inCore->request('title', 'str');
      $body = $inCore->request('body', 'str');
      if($cat_id == "" or $x == "" or $y == "" or $title == "")
      {
	$inCore->redirectBack();
      }

      $place_id = $model->addPoi($user_id, $x, $y, $cat_id, $title, $body);

      if ($place_id)
      {
	if($place['type_id'] == "1" AND mysql_result(mysql_query("SELECT target_url FROM cms_actions_log ORDER BY id DESC LIMIT 1"),0) != '/usermaps/view'.$place['id'].'.html')
	{
	  cmsActions::log('add_place', array(
                'object' => 'себя на карту',
                'object_url' => '/usermaps/view'.$place_id.'.html',
                'object_id'=> $place_id,
                'target' => '',
                'target_url' => '/usermaps/view'.$place_id.'.html',
                'target_id' => 0,
                'description' => ''));
	}
	cmsCore::addSessionMessage('Ваша точка добавлена!', 'success');
      }
      else
      {
	cmsCore::addSessionMessage('Ошибка добавления! '.$place_id. ' ', 'error');
      }
      $inCore->redirect('/usermaps/poi.html');
      exit;
    }
  }

  if($do == "ajax_checkin")
  {
//INSERT INTO `cms_actions` (`component`, `name`, `title`, `message`, `is_tracked`, `is_visible`) VALUES
//('usermaps', 'chekin', 'Добавление отметки', 'отметился в %s', 1, 1);
    $user_id = $inUser->id;
    $place_id = $inCore->request('place_id', 'int', 0);
    $place = $model->getPlace($place_id);
    if($user_id != 0 and $place and $place['type_id'] != 1)
    {
      if($model->addChekin($place_id, $user_id, time()))
      {
//INSERT INTO `cms_actions` (`component` ,`name` ,`title` ,`message` ,`is_tracked` ,`is_visible`)
//VALUES ('usermaps',  'add_checkin',  'Новая отметка',  'отметился в точке %s|',  '1',  '1');
	$category = $model->getCategory($place['type_id']);
	cmsActions::log('add_checkin', array(
                'object' => str_replace('""','"',$category['title'].' "'.$place['title'].'"'),
                'object_url' => '/usermaps/view'.$place_id.'.html',
                'object_id'=> $place_id,
                'target' => '',
                'target_url' => '/usermaps/view'.$place_id.'.html',
                'target_id' => 0,
                'description' => ''));
	echo 'ok';
      }
      else
      {
	echo 'Ошибка базы данных';
      }
    }
    exit;
  }
//Настройки пользователей
  if($do == "usersettings")
  {
    if($inUser->id == 0)
    {
      $inCore->redirectBack();
      return;
    }
    
    $maps_user_del = $inCore->request('maps_user_del', 'str');
    $maps_chekin_del = $inCore->request('maps_chekin_del', 'str');

    if($maps_user_del == "on")
    {
      $place = $model->getUserPlace($inUser->id);
      $model->deletePlace($place['id']);
      cmsCore::addSessionMessage('Ваша точка удалена с карты!', 'success');
    }
    
    if($maps_chekin_del == "on")
    {
      $model->deleteUserChekin($inUser->id);
      cmsCore::addSessionMessage('Ваши отметки о посещении удалены с карты!', 'success');
    }
    
    $inPage->setTitle("Настройки");
    $smarty = $inCore->initSmarty('components', 'com_places_usersettings.tpl');
    $smarty->assign('cfg', $cfg);
    $smarty->display('com_places_usersettings.tpl');
    
  }
  
//РАБОТА С КАТЕГОРИЯМИ
  if($do == "category_view")
  {
 //   $cfg['maps_engine'] = strtolower($cfg['maps_engine']);
 //   $cfg['maps_center'] = '['.$cfg['maps_center'].']';
    $id = $inCore->request('id', 'int', 0);
    $category = $model->getCategory($id);
    if(!$category and $category != 0)
    {
	$inCore->redirectBack();
    }
//Обработка POST
    if($inUser->is_admin)
    {
      $is_send = $inCore->inRequest('title');
      if($is_send)
      {
	$title = $inCore->request('title', 'str');
	$name = $inCore->request('name', 'str');
	if(!$title || !$name)
	{
	  cmsCore::addSessionMessage('Что то было не заполнено!', 'error');
	}
	else
	{
	  if($id == "0")
	  {
	    $is_root = 1;
	  }
	  $root_id = $id;
	  $add = $model->addCategory($name,$title,$is_root,$root_id);
	  if($add)
	  {
	    cmsCore::addSessionMessage('Категория добавлена!', 'success');
	  }
	  else
	  {
	    cmsCore::addSessionMessage('Что то пошло не так!', 'error');
	  }
	}
      }
    }

    if($category['is_root'] == 0 and $category['root_id'] != 0 or $category['id'] == 1)
    {
      $inPage->setTitle("Точки в категории ".$category['title']);

      $categores = $model->getAllCategores();
      $pois = $model->getPois($id);
      $smarty = $inCore->initSmarty('components', 'com_places_mainmap.tpl');
      $smarty->assign('cfg', $cfg);
      $smarty->assign('user_id', "-1");
//      $smarty->assign('userplace', $userplace);
      $smarty->assign('categores', $categores);
      $smarty->assign('pois', $pois);
      $smarty->display('com_places_mainmap.tpl');
    }
    elseif($category['id'] != 0)
    {
      $inPage->setTitle("Раздел ".$category['title']);
      $subcat = $model->getCategores($category['id']);

      $smarty = $inCore->initSmarty('components', 'com_places_view_category.tpl');
      $smarty->assign('root', $category);
      $smarty->assign('subcat', $subcat);
      $smarty->assign('is_admin', $inUser->is_admin);
      $smarty->display('com_places_view_category.tpl');
    }
    else
    {
      $inPage->setTitle("Категории");
      $subcat = $model->getCategores("0");
      $smarty = $inCore->initSmarty('components', 'com_places_view_root.tpl');
      $smarty->assign('subcat', $subcat);
      $smarty->assign('user_id',$inUser->id);
      $smarty->assign('is_admin', $inUser->is_admin);
      $smarty->display('com_places_view_root.tpl');
    }
    return;
  }

  if($do == "ajax_eventpoint")
  {
    $id = $inCore->request('event_id', 'int', 0);
    $type = $inCore->request('event_type', 'str');
    $coord_raw = explode(",",$inCore->request('new_coord', 'str'));
    $x = $coord_raw[0];
    $y = $coord_raw[1];

    if($type == "photo")
    {
      $photo = mysql_fetch_assoc(mysql_query("SELECT * FROM cms_photo_files WHERE id = $id"));

      if($photo['user_id'] == $inUser->id or $inUser->is_admin)
      {
	if(mysql_num_rows(mysql_query("SELECT * FROM cms_places_events WHERE object_id = $id AND `object_type` = '$type'")) != 0)
	{
	  $sql = mysql_query("UPDATE cms_places_events SET x = $x , y = $y WHERE object_id = $id AND `object_type` = '$type' LIMIT 1");
	}
	else
	{
	  $sql = mysql_query("INSERT INTO cms_places_events (`object_id`, `object_type`, `x`, `y`) VALUES ('$id', '$type', '$x', '$y')");
	}

	if($sql)
	{
	  echo "ok";
	}
	else
	{
	  print mysql_error();
	}
      }
      else
      {
	echo 'Ошибка доступа';
      }
    }
    exit;
  }

  if($do == "geolocation")
  {
      $inPage->setTitle("Геолокация");
      $smarty = $inCore->initSmarty('components', 'com_places_geolocation.tpl');
      $smarty->assign('cfg', $cfg);
      $smarty->display('com_places_geolocation.tpl');
  }

  if($do == "ajax_arround")
  {
    $coord_raw = explode(",",$inCore->request('coord', 'str'));
    $group = $inCore->request('group', 'int');
    $objects = $inCore->request('objects', 'int');
    $distance = $inCore->request('distance', 'int');
    $x = $coord_raw[0];
    $y = $coord_raw[1];
    $points = $model->getGeoArround($x,$y,$group,$objects,$distance);
    if($points)
    {
      ob_start();
      $smarty = $inCore->initSmarty('components', 'com_places_geo_arround.tpl');
      $smarty->assign('points', $points);
      $smarty->display('com_places_geo_arround.tpl');
      $html = ob_get_clean();
    }
    else
    {
      $html = "ничего не найдено";
    }
    print $html;
    exit;
  }
  
  if($do == "ajax_structure")
  {
    $bound = $coord_raw = explode(",",$inCore->request('bound', 'str'));
    $y_max = $bound[0];
    $x_max = $bound[1];
    $y_min = $bound[2];
    $x_min = $bound[3];
    print json_encode($model->StructureOfPoints($y_min,$x_min,$y_max,$x_max));
    //print_r($bound);
    
    exit;
  }
//   FOTOLIB
  if($do == "imagerotate")
  {
    $side = $md5 = $inCore->request('side', 'str');
    $image_id = $inCore->request('image_id', 'int');
    
    include('fotolib.class.php');
    $foto = new FotoLib();
    $foto->Rotate($side,$image_id);
    $inCore->redirectBack();
    exit;
  }
}
?>