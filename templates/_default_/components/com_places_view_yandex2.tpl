<script src="http://api-maps.yandex.ru/2.0/?load=package.full&mode=release&lang=ru-RU" type="text/javascript"></script>
{add_css file="components/usermaps/css/usermaps.css"}
{add_css file="components/usermaps/css/fotolib.css"}
{add_css file="components/usermaps/js/fancybox/jquery.fancybox-1.3.4.css"}

{add_js file="components/usermaps/js/fancybox/jquery.fancybox-1.3.4.js"}
{add_js file="components/usermaps/js/fancybox/jquery.easing-1.3.pack.js"}

{literal}
<script type="text/javascript">
  var fid = 0;
  
  ymaps.ready(function () {
    var myMap = new ymaps.Map('YMapsID', {
      center: [{/literal}{$place.y}{literal}, {/literal}{$place.x}{literal}],
      zoom: {/literal}{$cfg.point_zoom}{literal},
      type: "yandex#{/literal}{$cfg.maps_engine}{literal}"
    });
    
    myMap.controls
      .add('zoomControl', {top: '5', left: '5'})
      .add('typeSelector');
      
    var placemark{/literal}{$place.id}{literal} = new ymaps.Placemark([{/literal}{$place.y}{literal},{/literal}{$place.x}{literal}],{},{
       iconImageHref: "/components/usermaps/img/{/literal}{$icon}{literal}.png",
       iconImageSize: [40, 40],
       iconImageOffset: [-20, -40]
    });
//     Добавляем точку
    myMap.geoObjects.add(placemark{/literal}{$place.id}{literal});
//     Определяем адресс
    ymaps.geocode([{/literal}{$place.y}{literal},{/literal}{$place.x}{literal}]).then(function (res) {
      var names = [];
      res.geoObjects.each(function (obj) {
	names.push(obj.properties.get('name'));
      });
      $('#adr').html('<h1>Адрес:</h1>'+names[0]);
    });
//     Если есть точка пользователя рисуем кнопку и маршрут 
{/literal}
  {if $userplace}
{literal}
    var addRoute = new ymaps.control.Button({
	data: {
	  content: "Проложить маршрут"
	},
	});
    myMap.controls
      .add(addRoute,{top: '5', right: '100'});
    
    addRoute.events.add('click', function(e) {
      var placemark_userplace = new ymaps.Placemark([{/literal}{$userplace.y}{literal},{/literal}{$userplace.x}{literal}],
      {},
      {
       iconImageHref: "/components/usermaps/img/marker_me.png",
       iconImageSize: [40, 40],
       iconImageOffset: [-20, -40]
      });
      myMap.geoObjects.add(placemark_userplace);
      
      var myRouter = ymaps.route([
        [{/literal}{$place.y}{literal},{/literal}{$place.x}{literal}],
        [{/literal}{$userplace.y}{literal},{/literal}{$userplace.x}{literal}]
      ],
      {
        mapStateAutoApply: true 
      });
      myRouter.then(function(route) {
      
	var points = route.getWayPoints(); 
	points.removeAll(points);
	
	myMap.geoObjects.add(route);
      });
    });
{/literal}
  {/if}
{literal}
  });
  window.onload = function ()
  {
    
    $("a.inline").fancybox({
        'autoScale'     	: 'true' ,
        'transitionIn'		: 'none',
	'transitionOut'		: 'none',
	'type'			: 'inline',
	'opacity'		: 'true',
	'centerOnScroll'	: 'true',
	'padding'		: 0,
	'scrolling'		: 'no'
    });
    
/*
    var placeStyle = new YMaps.Style();
    placeStyle.iconStyle = new YMaps.IconStyle();
    placeStyle.iconStyle.offset = new YMaps.Point(0, -40);
    placeStyle.iconStyle.size = new YMaps.Point(40, 40);
    placeStyle.iconStyle = new YMaps.IconStyle();
    placeStyle.iconStyle.href = "/components/usermaps/img/{/literal}{$icon}{literal}.png";
{/literal}
{if !$userplace}
{literal}
    var placemark = new YMaps.Placemark(map.getCenter(), {style: placeStyle, hasBalloon: false}, {draggable: false});
    map.addOverlay(placemark);
{/literal}
{else}
{literal}
	var style = new YMaps.Style(); // Стиль для меток и линий маршрутизатора
	style.lineStyle = new YMaps.LineStyle(); // Задаем стиль линии
	style.lineStyle.strokeWidth = 5; // Ширина линии
	style.lineStyle.strokeColor = '72B5F9FF'; // Цвет линии в формате RGBA
	 // Применяем стиль к маршруту

	var userStyle = new YMaps.Style();
	userStyle.iconStyle = new YMaps.IconStyle();
	userStyle.iconStyle.offset = new YMaps.Point(0, -40);
	userStyle.iconStyle.size = new YMaps.Point(40, 40);
	userStyle.iconStyle = new YMaps.IconStyle();
	userStyle.iconStyle.href = "/components/usermaps/img/marker_me.png";
	var router = new YMaps.Router(
	[
	  new YMaps.GeoPoint({/literal}{$userplace.x}{literal}, {/literal}{$userplace.y}{literal}),
          new YMaps.GeoPoint({/literal}{$place.x}{literal}, {/literal}{$place.y}{literal})
	],
	[],
	{ viewAutoApply: true }
	);

	router.setStyle(style);

	YMaps.Events.observe(router, router.Events.Success, function()
	{
                router.getWayPoint(0).setStyle(userStyle);
                router.getWayPoint(1).setStyle(placeStyle);
                map.addOverlay(router);
        });
{/literal}
{/if}
{literal}
    var geocoder = new YMaps.Geocoder("{/literal}{$place.x}{literal}, {/literal}{$place.y}{literal}");
    YMaps.Events.observe(geocoder, geocoder.Events.Load, function () {
      if (this.length()) {
	geoResult = this.get(0);
	$('#adr').html('<h1>Адрес:</h1>'+geoResult.text);
      }
    });
*/  }

  function checkIn()
  {
    if($('#checkin').text() != "Вы отметились")
    {
      $.ajax({
      url:    	'/usermaps/ajax_checkin',
      data: 	"place_id="+{/literal}{$place.id}{literal},
      type:   	'post',
      success: function(answer)
      {
	if(answer == 'ok')
	{
	  $('#checkin').text('Вы отметились');
	  $('#checkinlist').show();
	  $('#checkinlist UL').prepend('<li><a href="/users/{/literal}{$user.login}{literal}">{/literal}{$user.nickname}{literal}</a></li>');
	}
	else
	{
	  alert(answer);
	}
      }
      });
    }
  }
  
  function addFile()
  {
    fid = fid+1;
    $('#inputs').append('<input type="file" name="file_'+fid+'"><br />');
  }
  
  function rotate(to,id)
  {
    if(to == "left")
    {
    }
    
    if(to == "right")
    {
    }
  }
</script>
{/literal}
<div id="YMapsID"></div>
<div class="pointinfo">
<h1>{$title}</h1>
<img src="/components/usermaps/img/{$icon}.png">
<ul>
  <li>Автор: <a href="/users/{$author.login}">{$author.nickname}</a></li>
  <li>Категория: <a href="/usermaps/category{$category.id}.html">{$category.title}</a></li>
  {if $is_author}
  <li><a href="/usermaps/edit{$place.id}.html">Редактировать</a> | <a href="/usermaps/delete{$place.id}.html">Удалить</a></li>
  {/if}
</ul>
{if $cfg.maps_chekin and $category.id != 1 and $user.id != 0}
  <div id="checkin" onClick="checkIn()">
    {if $usercheck}
    Вы отметились
    {else}
    Отметиться
    {/if}
  </div>
  <div id="checkinlist" {if !$checkin}style='display:none'{/if} />
    <h3>Тут были:</h3>
    <ul>
      {foreach key=id item=check from=$checkin}
      <li><a href="/users/{$check.login}">{$check.nickname}</a></li>
      {/foreach}
    </ul>
  </div>
{/if}
</div>
<div class="arround" id="adr">
</div>

<!-- FOTOLIB -->
{if $images or $allow_add_foto}
<div class="arround" id="fotolib_img">
  <h1>Галерея:</h1>
{/if}
<!-- Сами фото -->
{if $images}
  <ul>
  {foreach key=id item=image from=$images}
    <li>
      <a href="#{$image.name}" rel="group1" class="inline">
	<img src="/images/fotolib/L_{$image.name}.jpg">
      </a>
      <div style="display:none" class="fbinline">
	<div id="{$image.name}">
	  <img src="/images/fotolib/S_{$image.name}.jpg" class="mainimage">
	  <div id="fancybox-title" class="fancybox-title-over" style="width: 100%; display: block; ">
	    {if $image.user_id == $user.id}
	    <span id="fancybox-title-over">
	      <div align="center">
		<a href="/usermaps/rotate/left/{$image.id}.html"><img src="/components/usermaps/images/object-rotate-left.png"></a>
		<a href="/usermaps/rotate/right/{$image.id}.html"><img src="/components/usermaps/images/object-rotate-right.png"></a>
	      </div>
	    </span>
	    {/if}
	  </div>
	</div>
      </div>
    </li>
  {/foreach}  
  </ul>
{/if}
<!-- Форма добавления -->
{if $allow_add_foto}
    <form action="" method="POST" enctype="multipart/form-data">
      <fieldset>
	<legend>Загрузить изображение</legend>
	<div id="inputs">
	  <input type="file" name="file_0"><br />
	</div>
	<a onClick="addFile()">[+]</a><br/>
	<input type="submit" value="Отправить">
    </form>
{/if}
{if $images or $allow_add_foto}
</div>
{/if}


{if $place.body}
<div class="arround">
  <h1>Описание:</h1>
  {$place.body}
</div>
{/if}
{if $arround}
<div class="arround">
  <h1>Рядом:</h1>
  <ul>
    {foreach key=id item=arr from=$arround}
      <li><a href="/usermaps/view{$arr.id}.html">{$arr.category_title} "{$arr.title}" ({$arr.distance} м)</a></li>
    {/foreach}
  </ul>
</div>
{/if}