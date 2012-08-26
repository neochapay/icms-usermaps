<?php
function mod_usermaps_mapview($user_id)
{
  $inCore = cmsCore::getInstance();
  $inUser = cmsUser::getInstance();
  $cfg = $inCore->loadComponentConfig('usermaps');

  $inCore->loadModel('usermaps');

  $model = new cms_model_usermaps();
  
//Настраиваем совместимость старого конфига с новым движком  
////Типы карт
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
  
  $smarty = $inCore->initSmarty('modules', 'mod_usermaps_mapview.tpl');
  $smarty->assign('cfg', $cfg);
  $smarty->assign('user_id', $inUser->id);
  $smarty->assign('structure', $structure);
  $smarty->assign('have_userplace', $have_userplace);
  $smarty->display('mod_usermaps_mapview.tpl');

  return true;
}
?>
