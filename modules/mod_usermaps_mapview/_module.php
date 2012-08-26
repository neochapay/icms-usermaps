<?php
function mod_places_mapview($module_id, $user_id){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    
  echo ' <script src="http://api-maps.yandex.ru/1.1/index.xml?key=ABn-V04BAAAAjTMjIQMAs2crvaK0SSRbOdHaAEI4s6-PRmQAAAAAAAAAAAAke3PoCvdDzvPon9YDtn6eSUIxnw==" type="text/javascript"></script>';

  if($_SESSION["user"]["id"] != 0){
    $sql = "SELECT * FROM cms_places WHERE `user_id` = '".$_SESSION["user"]["id"]."' AND `type` = 'user'"; 
    $result = $inDB->query($sql);  
    if ($inDB->num_rows($result)){
      $center = "47.25, 56.13";
      $zoom = "12";    
    }else{
      $coord = $inDB->fetch_assoc($result);
      $x = $coord['x'];
      $y = $coord['y'];
      $center = $x.', '.$y;
      $zoom = "12";
    }
  }else{
    $center = "47.25, 56.13";
    $zoom = "12";
  }
  echo '<script type="text/javascript">
        window.onload = function () 
        {
        var map = new YMaps.Map(document.getElementById("YMapsID"));
        map.setCenter(new YMaps.GeoPoint('.$center.'), '.$zoom.');
        var zoomControl = new YMaps.Zoom({noTips: true});
        map.addControl(zoomControl);';

  
  $sql = "SELECT * FROM cms_places";
  $result = $inDB->query($sql);
  while($coord = $inDB->fetch_assoc($result)){
    $user_id = $coord['user_id'];
    $x = $coord['x'];
    $y = $coord['y'];
    if($_SESSION["user"]["id"] == $user_id){
      echo 'var placemark_'.$user_id.' = new YMaps.Placemark(new YMaps.GeoPoint('.$x.','.$y.'), {style: "default#redPoint"});';
    }else{
      echo 'var placemark_'.$user_id.' = new YMaps.Placemark(new YMaps.GeoPoint('.$x.','.$y.'), {style: "default#whiteSmallPoint"});';
    }
    if($_SESSION["user"]["id"] != 0){
      $user_data = $inDB->get_fields('cms_users','id='.$user_id,'login,nickname,is_deleted');    
      $user_name = $user_data['nickname'];
      $user_login = $user_data['login'];
      if (!function_exists('usrLink') && !function_exists('usrImageNOdb')){ //if not included earlier
        $inCore->includeFile('components/users/includes/usercore.php');
      }
      $imageurl = $inDB->get_field('cms_user_profiles','user_id='.$user_id,'imageurl');
      $image = usrLink(usrImageNOdb($user_id, 'small', $imageurl, $user_data['is_deleted']), $user_data['login']);
      echo '
	       placemark_'.$user_id.'.name = "'.$user_name.'";
	       placemark_'.$user_id.'.setBalloonContent("<a href=\"/users/'.$user_login.'\"><img src=\"/images/users/avatars/small/'.$image.'\"></a>");
	   ';
    }
    echo 'map.addOverlay(placemark_'.$user_id.');';
  }
  echo '}'."\n";
  echo '</script>
  <div id="YMapsID" style="width:100%;height:300px"></div>';
  if (isset($_SESSION["user"]["id"])){
    $sql = "SELECT * FROM cms_places WHERE user_id = ".$user_id; 
    if($inDB->num_rows($result)){
      echo '<a href="/places/add">Добавь себя</a>';
    }else{
      echo '<a href="/places/add">Изменить местоположение</a>';    
    }
  }else{
    echo '<a href="registration">Добавить себя</a>';
  }
  return true;
}
?> 
