{add_css file="components/usermaps/css/usermaps.css"}
{add_js file="components/usermaps/js/gears_init.js"}
{add_js file="components/usermaps/js/geo.js"}
<script src="http://api-maps.yandex.ru/1.1/index.xml?key={$cfg.yandex_key}&modules=pmap" type="text/javascript"></script>

  <p id="YMapsID">
    <span id="live-geolocation"></span>
  </p>
  <div class="pointinfo">Идёт поиск...</div>
  <div class="arround">
    <ul>
      <li><a href="#">Добавить точку здесь</a></li>
    </ul>
  </div>
{literal}
<script>
  var objects = 10;
  var map;
  var group = 0;
  var distance = 1000;
  var placeStyle;
  window.onload = function ()
  {
    map = new YMaps.Map(document.getElementById("YMapsID"));
    map.setCenter(new YMaps.GeoPoint({/literal}{$cfg.maps_center}{literal}), {/literal}{$cfg.point_zoom}{literal});
    map.setType(YMaps.MapType.{/literal}{$cfg.maps_engine}{literal});
    var miniMapPositive = new YMaps.MiniMap(3);
    map.addControl(miniMapPositive);
    var zoomControl = new YMaps.Zoom({noTips: true});
    map.addControl(zoomControl);
{/literal}
    {if $cfg.maps_engine == "PMAP" or $cfg.maps_engine == "PHYBRID"}
{literal}
    map.addControl(new YMaps.TypeControl([YMaps.MapType.PMAP, YMaps.MapType.PHYBRID, YMaps.MapType.SATELLITE]));
{/literal}
    {else}
{literal}
    map.addControl(new YMaps.TypeControl());
{/literal}
    {/if}
{literal}
    placeStyle = new YMaps.Style();
    placeStyle.iconStyle = new YMaps.IconStyle();
    placeStyle.iconStyle.offset = new YMaps.Point(0, -40);
    placeStyle.iconStyle.size = new YMaps.Point(40, 40);
    placeStyle.iconStyle = new YMaps.IconStyle();
    placeStyle.iconStyle.href = "/components/usermaps/img/marker_me.png";
  }

  function lookup_location()
  {
    geo_position_js.getCurrentPosition(show_map, show_map_error);
  }

  function show_map(loc)
  {
    map.setCenter(new YMaps.GeoPoint(loc.coords.longitude,loc.coords.latitude), {/literal}{$cfg.point_zoom}{literal});

    var placemark = new YMaps.Placemark(map.getCenter(), {draggable: true , style: placeStyle,  hasBalloon: false});
    map.addOverlay(placemark);
    YMaps.Events.observe(placemark, placemark.Events.DragEnd, function (obj)
    {
      obj.update();
      var coord = obj.getGeoPoint();
      getArround(coord);
    });
    getArround(loc.coords.longitude+","+loc.coords.latitude);
  }


  function show_map_error()
  {
    if (YMaps.location)
    {
      center = new YMaps.GeoPoint(YMaps.location.longitude, YMaps.location.latitude);
      if (YMaps.location.zoom)
      {
        zoom = YMaps.location.zoom;
      }
      map.setCenter(center, zoom);
    }
    var placemark = new YMaps.Placemark(map.getCenter(), {draggable: true , style: placeStyle,  hasBalloon: false});
    map.addOverlay(placemark);
    YMaps.Events.observe(placemark, placemark.Events.DragEnd, function (obj)
    {
      obj.update();
      var coord = obj.getGeoPoint();
      getArround(coord);
    });
    getArround(map.getCenter());
  }

  if (geo_position_js.init())
  {
    lookup_location();
  }

  function getArround(coord)
  {
    $.ajax({
    url:    	'/usermaps/ajax_arround',
    data: 	"coord="+coord+"&objects="+objects+"&group="+group+"&distance="+distance,
    type:   	'post',
    success: function(answer)
      {
	$('.pointinfo').html(answer)
      }
    });
  }
</script>
{/literal}