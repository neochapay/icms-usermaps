<script src="http://api-maps.yandex.ru/1.1/index.xml?key={$cfg.yandex_key}&modules=pmap" type="text/javascript"></script>
{add_css file="components/usermaps/css/usermaps.css"}
</script>
{literal}
<script type="text/javascript">
  window.onload = function ()
  {
    var map = new YMaps.Map(document.getElementById("YMapsID"));
{/literal}
  {if !$userplace}
{literal}
    map.setCenter(new YMaps.GeoPoint({/literal}{$cfg.maps_center}{literal}), {/literal}{$cfg.main_zoom}{literal});
{/literal}
  {else}
{literal}
    map.setCenter(new YMaps.GeoPoint({/literal}{$userplace.x}{literal},{/literal}{$userplace.y}{literal}), {/literal}{$cfg.main_zoom+1}{literal});
{/literal}
  {/if}
{literal}
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
    map.setType(YMaps.MapType.{/literal}{$cfg.maps_engine}{literal});
// Кнопки добавления
{/literal}
  {if $user_id != "-1"}
{literal}
    var toolbar = new YMaps.ToolBar([]);
    var addButton = new YMaps.ToolBarButton({
{/literal}
  {if $userplace}
{literal}
    caption: "Изменить свое местоположение",
{/literal}
  {else}
{literal}
    caption: "Добавить себя на карту",
{/literal}
  {/if}
{literal}
    });
    var poiButton = new YMaps.ToolBarButton({caption: "Добавить организацию"});
    YMaps.Events.observe(addButton, addButton.Events.Click, function () {window.location.href='/usermaps/add.html';}, map);
    YMaps.Events.observe(poiButton, addButton.Events.Click, function () {window.location.href='/usermaps/poi_add.html';}, map);

    toolbar.add(addButton);
    toolbar.add(poiButton);
    map.addControl(toolbar);
{/literal}
  {/if}
{literal}
//Стили
    var userStyle = new YMaps.Style();
    userStyle.iconStyle = new YMaps.IconStyle();
    userStyle.iconStyle.offset = new YMaps.Point(0, -40);
    userStyle.iconStyle.size = new YMaps.Point(40, 40);
    userStyle.iconStyle = new YMaps.IconStyle();
    userStyle.iconStyle.href = "/components/usermaps/img/marker_me.png";

    var baseStyle = new YMaps.Style();
    baseStyle.iconStyle = new YMaps.IconStyle();
    baseStyle.iconStyle.offset = new YMaps.Point(0, -20);
    baseStyle.iconStyle.size = new YMaps.Point(20, 20);
{/literal}
{if $categores}
  {foreach key=id item=category from=$categores}
    {literal}
      var style_{/literal}{$category.id}{literal} = new YMaps.Style(baseStyle);
      style_{/literal}{$category.id}{literal}.iconStyle = new YMaps.IconStyle();
      style_{/literal}{$category.id}{literal}.iconStyle.href = "/components/usermaps/img/{/literal}{$category.name}{literal}.png";
    {/literal}
  {/foreach}
{/if}

{if $pois}
  {foreach key=id item=poi from=$pois}
    {if $poi.type_id == "1"}
      {if $poi.user_id == $user_id}
	{literal}
	var placemark_{/literal}{$poi.id}{literal} = new YMaps.Placemark(new YMaps.GeoPoint({/literal}{$poi.x}{literal},{/literal}{$poi.y}{literal}), {style: 	userStyle});
	{/literal}
      {else}
	{literal}
	var placemark_{/literal}{$poi.id}{literal} = new YMaps.Placemark(new YMaps.GeoPoint({/literal}{$poi.x}{literal},{/literal}{$poi.y}{literal}), {style: style_{/literal}{$poi.type_id}{literal}});
	{/literal}
      {/if}
      {literal}placemark_{/literal}{$poi.id}{literal}.setBalloonContent("<a href=\"/users/{/literal}{$poi.username}{literal}\"><img src=\"{/literal}{$poi.userpicture}{literal}\"></a>");{/literal}
    {else}
      {literal}
      var placemark_{/literal}{$poi.id}{literal} = new YMaps.Placemark(new YMaps.GeoPoint({/literal}{$poi.x}{literal},{/literal}{$poi.y}{literal}), {style: style_{/literal}{$poi.type_id}{literal}});
      placemark_{/literal}{$poi.id}{literal}.setBalloonContent("<a href=\"/usermaps/view{/literal}{$poi.id}{literal}.html\">{/literal}{$poi.title}{literal}</a>");
      {/literal}
    {/if}
    {literal}
    map.addOverlay(placemark_{/literal}{$poi.id}{literal});
    {/literal}
  {/foreach}
{/if}
  }
</script>
<div id="YMapsID" style="width:100%;height:300px;margin-bottom: 10px;"></div>