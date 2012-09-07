<script src="http://api-maps.yandex.ru/1.1/index.xml?key={$cfg.yandex_key}&modules=pmap" type="text/javascript"></script>
{add_css file="components/usermaps/css/usermaps.css"}
<h3>Выберите категорию, напишите название, выберите точку <b>правой кнопкой мыши</b></h3>
{literal}
<script type="text/javascript">
  var map;
  $(document).ready(function ()
  {
    map = new YMaps.Map(document.getElementById("YMapsID"));
    map.setCenter(new YMaps.GeoPoint({/literal}{$cfg.maps_center}{literal}), {/literal}{$cfg.main_zoom}{literal});
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
    YMaps.Events.observe(map, map.Events.ContextMenu, function (map, mEvent)
    {
      map.openBalloon(mEvent.getGeoPoint(), "Новая метка", {hasCloseButton:false});
      $('#coord').val(mEvent.getGeoPoint());
    });
  });

  function showAddress (value) {
    map.removeAllOverlays();
    var geocoder = new YMaps.Geocoder(value, {results: 1, boundedBy: map.getBounds()});
    YMaps.Events.observe(geocoder, geocoder.Events.Load, function ()
    {
      if (this.length())
      {
	geoResult = this.get(0);
	map.openBalloon(geoResult.getGeoPoint(), "Новая метка", {hasCloseButton:false});
	$('#coord').val(geoResult.getGeoPoint());
        map.setBounds(geoResult.getBounds());
      }
    });
  }

  function sendForm()
  {
    if($('#title').val() == "")
    {
      alert("Имя точки не заполнено!");
    }
    else if($('#cat_id').val() == "")
    {
      alert("Не указана категория точки!");
    }
    else if($('#coord').val() == "")
    {
      alert("Не задано расположение точки!");
    }
    else
    {
      $('#mainform').submit();
    }
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
<form action="" method="POST" name='cfrm' id="mainform" onsubmit="sendForm(); return false;">
  <input type='hidden' name='coord' id="coord">
  <span class="title">Категория:</span>
  <select name="cat_id" id="cat_id">
    <option>Выберите категорию</option>
{foreach key=id item=category from=$categores}
    <option value={$category.id}>{$category.title}</option>
{/foreach}
  </select>
  <br />
  <span class="title">Имя:</span>  <input name="title" type="text" id="title"/>
  <br/>
  <span class="title">Подробнее:</span> <br />
  <textarea cols="35" rows="10" name="body" id="body"></textarea>
  <input type="submit" value="Отправить">
</form>
</div>