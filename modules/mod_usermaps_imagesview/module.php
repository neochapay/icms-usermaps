<?php
function mod_usermaps_imagesview()
{
  $inCore = cmsCore::getInstance();
  $inUser = cmsUser::getInstance();
  $cfg = $inCore->loadComponentConfig('usermaps');

  $inCore->loadModel('usermaps');

  $model = new cms_model_usermaps();
  
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
  
  return true;
}
?>