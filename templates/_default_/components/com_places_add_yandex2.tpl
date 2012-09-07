<script src="http://api-maps.yandex.ru/1.1/index.xml?key={$cfg.yandex_key}&modules=pmap" type="text/javascript"></script>
<h3>Нажмите правой кнопкой мыши на месте вашего расположения</h3>
{literal}
<script type="text/javascript">
  function coordSend(coord)
  {
    var obj=document.cfrm
    obj.coord.value=coord
    obj.submit()
  }

  window.onload = function ()
  {
    var map = new YMaps.Map(document.getElementById("YMapsID"));
    map.setCenter(new YMaps.GeoPoint({/literal}{$cfg.maps_center}{literal}), {/literal}{$cfg.main_zoom}{literal});
    map.setType(YMaps.MapType.{/literal}{$cfg.maps_engine}{literal});
    var miniMapPositive = new YMaps.MiniMap(3);
    map.addControl(miniMapPositive);
    var zoomControl = new YMaps.Zoom({noTips: true});
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
    map.addControl(zoomControl);
    YMaps.Events.observe(map, map.Events.ContextMenu, function (map, mEvent)
    {
      coordSend(mEvent.getGeoPoint());
    });

  }
</script>
{/literal}
<div id="YMapsID" style="width:100%;height:400px"></div>
<form action="" method="POST" name='cfrm'>
  <input type='hidden' name='coord'>
</form>