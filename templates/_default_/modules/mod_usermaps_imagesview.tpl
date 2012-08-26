<script src="http://api-maps.yandex.ru/2.0/?load=package.full&mode=release&lang=ru-RU" type="text/javascript"></script>
<script type="text/javascript">
{literal}
  ymaps.ready(function () {
    var myMap = new ymaps.Map('myMap', {
      center: [{/literal}{$cfg.maps_center}{literal}],
      zoom: {/literal}{$cfg.main_zoom}{literal},
      type: "yandex#{/literal}{$cfg.maps_engine}{literal}"
    });
    myMap.controls
      .add('zoomControl', {top: '5', left: '5'})
      .add('typeSelector');
    {/literal}
      {if $photos}
	{foreach key=id item=photo from=$photos}
	  var placemark{$photo.id}{literal} = new ymaps.Placemark([{/literal}{$photo.y}{literal},{/literal}{$photo.x}{literal}],
	  {},
	  {
	    iconImageHref: "/images/photos/small/{/literal}{$photo.file}{literal}",
	    iconImageSize: [48, 48],
	    iconImageOffset: [-24, -24]
	  });
	  myMap.geoObjects.add(placemark{/literal}{$photo.id});
	  
	  placemark{$photo.id}.events.add('click', function(e) {literal}{
	    window.location.href='/photos/photo{/literal}{$photo.id}{literal}.html';
	  }{/literal});
	{/foreach}
      {/if}
    {literal}
  });
{/literal}
</script>
<div id="myMap" style="width: 100%; height: 300px"></div>