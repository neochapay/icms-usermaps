<?php
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

class cms_model_usermaps{

    function __construct(){
        $this->inDB = cmsDatabase::getInstance();
    }

    public function getDefaultConfig() {
        $cfg = array();
        return $cfg;
    }

    public function addPlace($user_id,$x,$y, $cat_id)
    {
      $sql_check = "SELECT * FROM cms_places WHERE user_id = {$user_id} AND type_id = 1";
      $result = $this->inDB->query($sql_check);
      
      if (!$this->inDB->num_rows($result))
      {
	$point = $this->inDB->fetch_assoc($result);
	$id = $point['id'];
	$sql = "UPDATE cms_places SET x = '$x' , y = '$y', type_id = '$cat_id' WHERE user_id = '$user_id' AND id = $id LIMIT 1";
      }

      $sql = "INSERT INTO cms_places (type_id, user_id, x , y)
                VALUES ('{$cat_id}', '{$user_id}', '{$x}', '{$y}')";

      $this->inDB->query($sql);

      if ($this->inDB->error())
      {
	return false;
      }
      else
      {
	return $this->inDB->get_last_id('cms_places');
      }
    }

    public function deletePlace($place_id)
    {
      $sql = "DELETE FROM cms_places WHERE id = '{$place_id}' LIMIT 1";

      $result = $this->inDB->query($sql);

      if ($this->inDB->error())
      {
	return $this->inDB->get_last_id('cms_places');
      }
      else
      {
        return true;
      }
    }

    public function getUserPlace($user_id)
    {
      $sql = "SELECT * FROM cms_places WHERE user_id = '{$user_id}' and type_id = '1'";
      $result = $this->inDB->query($sql);
      
      if(!$this->inDB->num_rows($result))
      {
	return FALSE;
      }
      return $this->inDB->fetch_assoc($result);
    }

    public function getPlace($place_id)
    {
      if(!is_numeric($place_id))
      {
	return FALSE;
      }
      $sql = "SELECT * FROM cms_places WHERE id = '{$place_id}'";
      $result = $this->inDB->query($sql);
      if(!$this->inDB->num_rows($result))
      {
	return FALSE;
      }
      return $this->inDB->fetch_assoc($result);
    }

    public function updatePlace($id,$user_id,$x,$y,$cat_id,$title,$body)
    {
      $sql = "UPDATE cms_places SET x = '$x' , y = '$y', type_id = '$cat_id' , user_id = '$user_id' , title = '$title', body = '$body' WHERE id = '$id' LIMIT 1";
      $result = $this->inDB->query($sql);
      if($this->inDB->error())
      {
	return TRUE;
      }
      else
      {
	return FALSE;
      }
    }

    public function getAllPlaces()
    {
      $sql = "SELECT x,y,user_id FROM cms_places";
      $result = $this->inDB->query($sql);
      
      if ($this->inDB->error()) 
      { 
	return false; 
      }
      
      if (!$this->inDB->num_rows($result)) 
      { 
	return false; 
      }
      
      $output = array();
      while ($output = $this->inDB->fetch_assoc($result))
      {
	 $output[] = $output;
      }
      return $output;
    }

    public function getCommentTarget($target, $target_id)
    {
      $result = array();
      switch($target)
      {
	case 'point':
	$result['link']  = "/usermaps/view".$target_id.".html";
	$sql = "SELECT * FROM cms_places WHERE id = '$target_id'";
	$result = $this->inDB->query($sql);
	$point = $this->inDB->fetch_assoc($result);
	if($point['type_id'] == 1)
	{
	  $user_id = $point['user_id'];
	  $sql = "SELECT `nickname` FROM `cms_users` WHERE `id` = '{$user_id}'";
	  $result = $this->inDB->query($sql);
	  $nickname = $this->inDB->fetch_assoc($result);
	  $result['title'] = 'точки пользовател€ '.$nickname;
	}
	break;
      }
      return ($result ? $result : false);
    }

    public function getPoi($id)
    {
      if(!is_numeric($id))
      {
	return FALSE;
      }

      $sql = "SELECT * FROM cms_places_category WHERE id = '$id'";
      $result = $this->inDB->query($sql);
      
      if(!$this->inDB->num_rows($result))
      {
	return FALSE;
      }

      $poi = $this->inDB->fetch_assoc($result);
      if($poi['is_root'] == 1)
      {
	return FALSE;
      }

      return $poi;
    }

    public function getUser($id)
    {
      $sql = "SELECT * FROM cms_users WHERE id = '".$id."'";
      $result = $this->inDB->query($sql);
      return $this->inDB->fetch_assoc($result);
    }

    public function ImagesOnMap()
    {
      $sql = "SELECT 
      cms_places_events.x, 
      cms_places_events.y,
      cms_photo_files.file,
      cms_photo_files.id
      FROM cms_places_events
      INNER JOIN cms_photo_files ON cms_photo_files.id = cms_places_events.object_id
      WHERE cms_places_events.object_type = 'photo' 
      AND cms_photo_files.published = '1'";
      
      $result = $this->inDB->query($sql);

      if ($this->inDB->error())
      {
	return false;
      }

      if (!$this->inDB->num_rows($result))
      {
	return false;
      }
      
      $photos = array();
      while ($photo = $this->inDB->fetch_assoc($result))
      {
	$photos[] = $photo;
      }
      return $photos;      
    }
    
    public function getCategory($id)
    {
      $inCore = cmsCore::getInstance();
      $sql = "SELECT * FROM cms_places_category WHERE id = '".$id."'";
      $result = $this->inDB->query($sql);

      if ($this->inDB->error())
      {
	return false;
      }

      if (!$this->inDB->num_rows($result))
      {
	return false;
      }

      return $this->inDB->fetch_assoc($result);
    }

    public function getCategores($root)
    {
      $inCore = cmsCore::getInstance();
      if($root == "")
      {
	$sql = "SELECT id,title,name FROM cms_places_category WHERE is_root = 0 AND id <> 1";
      }
      else
      {
	$sql = "SELECT id,title,name FROM cms_places_category WHERE root_id = '$root'";
      }
      $result = $this->inDB->query($sql);

      if ($this->inDB->error())
      {
	return false;
      }

      if (!$this->inDB->num_rows($result))
      {
	return false;
      }

      $messages = array();
      while ($message = $this->inDB->fetch_assoc($result))
      {
	$messages[] = $message;
      }
      return $messages;
    }

    public function getAllCategores()
    {
      $inCore = cmsCore::getInstance();
      $sql = "SELECT * FROM cms_places_category ORDER BY `id` DESC";

      $result = $this->inDB->query($sql);

      if ($this->inDB->error())
      {
	return false;
      }

      if (!$this->inDB->num_rows($result))
      {
	return false;
      }

      $messages = array();
      while ($message = $this->inDB->fetch_assoc($result))
      {
	$messages[] = $message;
      }
      return $messages;
    }

    public function getAllPoi($cat_id)
    {
      $inCore = cmsCore::getInstance();
      if($cat_id == "")
      {
	$sql = "SELECT cms_places.*,
	cms_places_category.name
	FROM cms_places
	INNER JOIN cms_places_category
	ON cms_places.type_id=cms_places_category.id
	WHERE cms_places.type_id <> 1 ORDER BY cms_places.id DESC LIMIT 20";
      }
      else
      {
	$sql = "SELECT cms_places.*,cms_places_category.name FROM cms_places INNER JOIN cms_places_category ON cms_places.type_id=cms_places_category.id  WHERE cms_places.type_id = $cat_id ORDER BY cms_places.id DESC";
      }
      $result = $this->inDB->query($sql);

      if ($this->inDB->error())
      {
	return false;
      }

      if (!$this->inDB->num_rows($result))
      {
	return false;
      }

      $messages = array();
      while ($message = $this->inDB->fetch_assoc($result))
      {
	$sql = "SELECT title FROM cms_places_category WHERE id = ".$message["type_id"];
	$result = $this->inDB->query($sql);
	$tmp = $this->inDB->fetch_assoc($result);
	$message["category_title"] = $tmp['title'];
	if($cat_id == 1)
	{
	  $sql_ = "SELECT nickname FROM cms_users WHERE id = ".$message["user_id"];
	  $result_ = $this->inDB->query($sql_);
	  $tmp_ = $this->inDB->fetch_assoc($result_);
	  $message["title"] = $tmp_['nickname'];
	}
	$messages[] = $message;
      }
      return $messages;
    }

    public function getPois($category=0)
    {
      $inCore = cmsCore::getInstance();

      if(!$category)
      {
	$sql = "SELECT * FROM cms_places ORDER BY `id` DESC";
      }
      else
      {
	$sql = "SELECT * FROM cms_places WHERE type_id = $category ORDER BY `id` DESC";
      }

      $result = $this->inDB->query($sql);
      if ($this->inDB->error())
      {
	return false;
      }
      if (!$this->inDB->num_rows($result))
      {
	return false;
      }

      $messages = array();
      while ($message = $this->inDB->fetch_assoc($result))
      {
	$message["title"] = mysql_escape_string($message["title"]);
	$user_sql = "SELECT login FROM cms_users WHERE id = ".$message["user_id"];
	$user_result = $this->inDB->query($user_sql);
	$user = $this->inDB->fetch_assoc($user_result);
	$message["username"] = $user['login'];
	$image_sql = "SELECT imageurl FROM cms_user_profiles WHERE `user_id` = '".$message["user_id"]."'";
	$image_result = $this->inDB->query($image_sql); 
	$image = $this->inDB->fetch_assoc($image_result);
	$message["userpicture"] = '/images/users/avatars/small/'.$image['imageurl'];
	$messages[] = $message;
      }
      return $messages;
    }

    public function addPoi($user_id,$x,$y, $cat_id, $title, $body)
    {
      $sql = "INSERT INTO cms_places (type_id, user_id, x , y, title, body)
                VALUES ('{$cat_id}', '{$user_id}', '{$x}', '{$y}', '{$title}', '{$body}')";

      $this->inDB->query($sql);

      if ($this->inDB->error())
      {
	return false;
      }
      else
      {
	return $this->inDB->get_last_id('cms_places');
      }
    }

    public function getArround($id)
    {
      $point = $this->getPlace($id);
      $sql = "SELECT *, ( 6371000 * acos( cos( radians(".$point['y'].") ) * cos( radians( y ) ) * cos( radians( x ) - radians(".$point['x'].") ) + sin( radians(".$point['y'].") ) * sin( radians( y ) ) ) ) AS distance FROM cms_places HAVING distance < 250 AND id <> {$id} ORDER BY distance LIMIT 0 , 10";
      $result = $this->inDB->query($sql);
      if ($this->inDB->error())
      {
	return false;
      }

      while ($message = $this->inDB->fetch_assoc($result))
      {
	if($message['title'] == "" or $message['title'] == "NULL")
	{
	  $user = $this->getUser($message['user_id']);
	  $message['title'] = $user['nickname'];
	}
	$subsql = "SELECT * FROM cms_places_category WHERE id = ".$message["type_id"];
	$subresult = $this->inDB->query($subsql);
	$querty = $this->inDB->fetch_assoc($subresult);
	$message["category_title"] = $querty["title"];
	$message["category_icon"] = $querty["name"];
	$message["distance"] = round($message["distance"]);
	$messages[] = $message;
      }
      return $messages;
    }

    public function addCategory($name,$title,$is_root,$root_id)
    {
      $sql = "INSERT INTO cms_places_category (`name`, `title`, `is_root`, `root_id`) VALUES ('$name','$title','$is_root','$root_id')";
      $result = $this->inDB->query($sql);
      if ($this->inDB->error())
      {
	return false;
      }
      else
      {
	return TRUE;
      }
    }

    public function addChekin($place_id,$user_id,$time)
    {
      $sql = "INSERT INTO cms_places_checkin (`place_id`, `user_id`, `time`) VALUES ('$place_id','$user_id','$time')";
      $result = $this->inDB->query($sql);
      if ($this->inDB->error())
      {
	return false;
      }
      else
      {
	return TRUE;
      }
    }

    public function getChekin($place_id)
    {
      $inCore = cmsCore::getInstance();

      $sql = "SELECT cms_places_checkin.*,
      cms_users.login,
      cms_users.nickname
      FROM cms_places_checkin
      INNER JOIN cms_users ON cms_users.id = cms_places_checkin.user_id
      WHERE cms_places_checkin.place_id = $place_id
      GROUP BY cms_places_checkin.user_id
      ORDER BY cms_places_checkin.time DESC LIMIT 10";

      $result = $this->inDB->query($sql);
      if ($this->inDB->error())
      {
	return false;
      }
      if (!$this->inDB->num_rows($result))
      {
	return false;
      }

      $messages = array();
      while ($message = $this->inDB->fetch_assoc($result))
      {
	$messages[] = $message;
      }
      return $messages;
    }

    public function getUserChekin($place_id,$user_id)
    {
      $time = time() - 60*60; //ѕользователь отмечаетс€ раз в час максимум в одной точке
      $sql = "SELECT place_id FROM cms_places_checkin WHERE place_id = $place_id AND user_id = $user_id AND time > $time";
      $result = $this->inDB->query($sql);
      if ($this->inDB->error())
      {
	return false;
      }
      if (!$this->inDB->num_rows($result))
      {
	return false;
      }
      else
      {
	return true;
      }
    }

    public function getGeoArround($x,$y,$group=0,$limit=10,$distance=250)
    {
      if($limit == "")
      {
	$limit = 10;
      }

      if($distance == "")
      {
	$distance = 250;
      }

      $sql = "SELECT *, ( 6371000 * acos( cos( radians(".$y.") ) * cos( radians( y ) ) * cos( radians( x ) - radians(".$x.") ) + sin( radians(".$y.") ) * sin( radians( y ) ) ) ) AS distance FROM cms_places HAVING distance < $distance ORDER BY distance LIMIT 0 , $limit";
      $result = $this->inDB->query($sql);
      if ($this->inDB->error())
      {
	return false;
      }

      while ($message = $this->inDB->fetch_assoc($result))
      {
	if($message['title'] == "" or $message['title'] == "NULL")
	{
	  $user = $this->getUser($message['user_id']);
	  $message['title'] = $user['nickname'];
	}
	$subsql = "SELECT * FROM cms_places_category WHERE id = ".$message["type_id"];
	$subresult = $this->inDB->query($subsql);
	$querty = $this->inDB->fetch_assoc($subresult);

	$message["category_title"] = $querty["title"];
	$message["category_icon"] = $querty["name"];
	$message["distance"] = round($message["distance"]);
	$messages[] = $message;
      }
      return $messages;
    }
    
    public function deleteUserChekin($user_id)
    {
      $sql = "DELETE FROM cms_places_checkin WHERE user_id = $user_id";
      $result = $this->inDB->query($sql);
      if ($this->inDB->error())
      {
	return false;
      }
      else
      {
	return true;
      }
    }
    
    public function StructureOfPoints($y_min,$x_min,$y_max,$x_max)
    {
      $sql = "SELECT cms_places.* ,
      cms_places_category.name as catname,
      cms_places_category.title as cattitle,
      cms_user_profiles.imageurl,
      cms_users.login,
      cms_users.nickname,
      cms_users.rating
      FROM cms_places
      INNER JOIN cms_places_category ON cms_places.type_id = cms_places_category.id 
      INNER JOIN cms_user_profiles ON cms_user_profiles.user_id = cms_places.user_id 
      INNER JOIN cms_users ON cms_users.id = cms_places.user_id
      WHERE cms_places.x < '$x_min' 
      AND cms_places.x > '$x_max' 
      AND cms_places.y < '$y_min' 
      AND cms_places.y > '$y_max' 
      ORDER BY cms_places.id ASC";
      $result = $this->inDB->query($sql);

      if ($this->inDB->error())
      {
	return FALSE;
      }
      
      if(!$this->inDB->num_rows($result))
      {
	return FALSE;
      }
 
      $points = array();
      while($point = $this->inDB->fetch_assoc($result))
      {
	$point['title'] = iconv("cp1251","utf-8",$point['title']);
	$point['body'] = iconv("cp1251","utf-8",$point['body']);
	$point['nickname'] = iconv("cp1251","utf-8",$point['nickname']);
	$point['cattitle'] = iconv("cp1251","utf-8",$point['cattitle']);
	$points[] = $point;
      }
      return $points;
    }

}
?>