<script src="http://api-maps.yandex.ru/1.1/index.xml?key={$cfg.yandex_key}&modules=pmap" type="text/javascript"></script>
{add_css file="components/usermaps/css/usermaps.css"}
<h3>Перетащите маркер на новое место. После окончания нажмите кнопку "Отправить"</h3>
{literal}
<script type="text/javascript">
  var map;
  var placeStyle;
  window.onload = function ()
  {
    map = new YMaps.Map(document.getElementById("YMapsID"));
    map.setCenter(new YMaps.GeoPoint({/literal}{$place.x}{literal}, {/literal}{$place.y}{literal}), {/literal}{$cfg.point_zoom}{literal});
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
    placeStyle.iconStyle.href = "/components/usermaps/img/{/literal}{$icon}{literal}.png";
    var placemark = new YMaps.Placemark(map.getCenter(), {draggable: true , style: placeStyle,  hasBalloon: false});
    map.addOverlay(placemark);
    YMaps.Events.observe(placemark, placemark.Events.DragEnd, function (obj)
    {
      obj.update();
      $('#coord').val(obj.getGeoPoint());
    });

  }

  function showAddress (value) {
    map.removeAllOverlays();
    var geocoder = new YMaps.Geocoder(value, {results: 1, boundedBy: map.getBounds()});
    YMaps.Events.observe(geocoder, geocoder.Events.Load, function ()
    {
      if (this.length())
      {
	geoResult = this.get(0);
	var placemark = new YMaps.Placemark(geoResult.getGeoPoint(), {draggable: true , style: placeStyle,  hasBalloon: false});
	map.addOverlay(placemark);
	$('#coord').val(geoResult.getGeoPoint());
        map.setBounds(geoResult.getBounds());
      }
    });
  }
</script>
{/literal}
<form action="" onsubmit="showAddress(this.address.value);return false;">
  <p>
    Адрес: <input type="text" id="address" style="width:500px;" value="" />
    <input type="submit" value="Искать" />
  </p><br />
</form>
<div id="YMapsID"></div>
<div class="pointinfo">
<form action="" method="POST" name='cfrm'>
  <input type='hidden' name='coord' value="{$place.x}, {$place.y}" id="coord">
  {if $place.type_id != 1}
    <span class="title">Категория:</span>
    <select name=cat_id>
      <option>Выберите категорию</option>
      {foreach key=id item=category from=$categores}
	<option value={$category.id} {if $category.id == $place.type_id}selected{/if}>{$category.title}</option>
      {/foreach}
    </select>
    <br />
    <span class="title">Имя:</span>  <input name="title" type="text" value='{$place.title}'/>
    <br/>
    <span class="title">Подробнее:</span> <br />
    <textarea cols="35" rows="10" name="body">{$place.body}</textarea>
  {/if}
  <input type="submit" value="Отправить">
</form>
</div>