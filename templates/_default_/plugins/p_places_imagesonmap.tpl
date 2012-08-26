{add_css file="components/usermaps/css/usermaps.css"}
<script src="http://api-maps.yandex.ru/1.1/index.xml?key={$cfg.yandex_key}&modules=pmap" type="text/javascript"></script>
{literal}
<script type="text/javascript">
  window.onload = function ()
  {
    var map = new YMaps.Map(document.getElementById("pluginmaps"));
    map.setCenter(new YMaps.GeoPoint({/literal}{$center}{literal}), {/literal}{$cfg.main_zoom}{literal});
    map.setType(YMaps.MapType.{/literal}{$cfg.maps_engine}{literal});

    var zoomControl = new YMaps.Zoom({noTips: true});
    map.addControl(zoomControl);
    map.disableDblClickZoom();

    var placeStyle = new YMaps.Style();
    placeStyle.iconStyle = new YMaps.IconStyle();
    placeStyle.iconStyle.offset = new YMaps.Point(0, -40);
    placeStyle.iconStyle.size = new YMaps.Point(40, 40);
    placeStyle.iconStyle = new YMaps.IconStyle();
    placeStyle.iconStyle.href = "/components/usermaps/img/photo_big.png";
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
    {if $have_point}
{literal}
    var placemark = new YMaps.Placemark(map.getCenter(), {style: placeStyle, hasBalloon: false}, {draggable: true});
    map.addOverlay(placemark);
{/literal}
    {/if}
    {if $is_author}
{literal}
    YMaps.Events.observe(map, map.Events.DblClick, function (map, mEvent)
    {
      var photo_id = "{/literal}{$photo_id}{literal}";
      var photo_type = "{/literal}{$photo_type}{literal}";
      var new_coord = mEvent.getGeoPoint();
      $.ajax({
      url:    	'/usermaps/ajax_eventpoint',
      data: 	"event_id="+photo_id+"&event_type="+photo_type+"&new_coord="+new_coord,
      type:   	'post',
      success: function(answer)
      {
	if(answer == 'ok')
	{
	  map.removeAllOverlays()
	  var placemark = new YMaps.Placemark(mEvent.getGeoPoint(), {style: placeStyle, hasBalloon: false}, {draggable: true});
	  map.addOverlay(placemark);
	}
	else
	{
	  alert(answer);
	}
      }
    });
  });
{/literal}
    {/if}
{literal}
  }
</script>
{/literal}
<div class="photo_sub_details">Фото на карте:</div>
<div class="photo_details">
<div id="pluginmaps"></div>
{if $is_author}
<div class="photo_details" style="margin-top:5px;font-size: 12px">
Для того чтобы добавить метку кликните на карте
</div>
{/if}
</div>