<?php
function routes_usermaps()
{
     $routes[] = array(
       '_uri'  => '/^usermaps\/add.html$/i',
       'do'    => 'add'
     );

     $routes[] = array(
       '_uri'  => '/^usermaps\/delete([0-9]+).html$/i',
       'do'    => 'delete',
       1       => 'id'
     );

     $routes[] = array(
       '_uri'  => '/^usermaps\/edit([0-9]+).html$/i',
       'do'    => 'edit',
       1       => 'id'
     );

     $routes[] = array(
      '_uri'  => '/^usermaps\/view([0-9]+).html$/i',
      'do'    => 'view',
      1       => 'id'
     );

     $routes[] = array(
       '_uri'  => '/^usermaps\/poiadd.html$/i',
       'do'    => 'poi_add'
     );

      $routes[] = array(
       '_uri'  => '/^usermaps\/user([0-9]+).html$/i',
       'do'    => 'userpoint',
	1      => 'uid'
      );

      $routes[] = array(
       '_uri'  => '/^usermaps\/poi.html$/i',
       'do'    => 'poi_list'
      );

      $routes[] = array(
       '_uri'  => '/^usermaps\/poi_add.html$/i',
       'do'    => 'poi_add'
      );

     $routes[] = array(
      '_uri'  => '/^usermaps\/category([0-9]+).html$/i',
      'do'    => 'category_view',
      1       => 'id'
     );

     $routes[] = array(
      '_uri'  => '/^usermaps\/mainmap.html$/i',
      'do'    => 'mainmap',
     );

     $routes[] = array(
      '_uri'  => '/^usermaps\/imagemap.html$/i',
      'do'    => 'imagemap',
     );
          
     $routes[] = array(
      '_uri'  => '/^usermaps\/geolocation.html$/i',
      'do'    => 'geolocation',
     );

     $routes[] = array(
      '_uri'  => '/^usermaps\/settings.html$/i',
      'do'    => 'usersettings',
     );     
     
     $routes[] = array(
      '_uri'  => '/^usermaps\/ajax_eventpoint$/i',
      'do'    => 'ajax_eventpoint',
     );

     $routes[] = array(
      '_uri'  => '/^usermaps\/ajax_checkin$/i',
      'do'    => 'ajax_checkin',
     );
     
     $routes[] = array(
      '_uri'  => '/^usermaps\/ajax_arround$/i',
      'do'    => 'ajax_arround',
     );
     
     $routes[] = array(
      '_uri'  => '/^usermaps\/ajax_structure$/i',
      'do'    => 'ajax_structure',
     );
     
     $routes[] = array(
      '_uri'  => '/^usermaps\/rotate\/([a-zA-Z0-9\-]+)\/([a-zA-Z0-9\-]+).html$/i',
      'do'    => 'imagerotate',
      1	      => 'side',
      2	      => 'image_id'
     );     
    return $routes;
  }
?>