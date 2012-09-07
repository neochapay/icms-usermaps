<script src="http://api-maps.yandex.ru/2.0/?load=package.full&mode=release&lang=ru-RU" type="text/javascript"></script>
<style>
{literal}
  .balloonContentBody{
    display: inline-block;
  }
  .balloonContentBody IMG {
    max-width: 128px;
    max-height: 128px;
    float: left;
    padding-right: 5px;
  }
  
  .balloonContentBody .rating{
    width: 100%;
    font-size: 24px;
    text-align: center;
    padding-top: 10px;
  }
  
  .green{
    color: green;
  }
  
  .red{
    color: red;
  }
{/literal}
</style>
<script type="text/javascript">
{literal}
  var myMap;
  var clusterer;
  var openbaloon;
  var user_id;
  ymaps.ready(function () {
    balloonopen = 0;
    user_id = {/literal}{$user_id}{literal}
    myMap = new ymaps.Map('myMap', {
      center: [{/literal}{$cfg.maps_center}{literal}],
      zoom: {/literal}{$cfg.main_zoom}{literal},
      type: "yandex#{/literal}{$cfg.maps_engine}{literal}"
    });
    {/literal}
    {if $user_id != 0}
      {literal}

      var addOrg = new ymaps.control.Button({
      data: {
	content: "Добавить организацию"
      }
      });
      
      addOrg.events.add('click', function(e) {
	window.location.href='/usermaps/poi_add.html';
      });
 
    {/literal}
      {if $have_userplace == 1}
      {literal}
	var addHimself = new ymaps.control.Button({
	data: {
	  content: "Изменить местоположение"
	},
	});

	addHimself.events.add('click', function(e) {
	  window.location.href='/usermaps/add.html';
	});
	
      {/literal}
      {else}
      {literal}
	var addHimself = new ymaps.control.Button({
	data: {
	  content: "Добавить себя на карту"
	  }
	});
      
	addHimself.events.add('click', function(e) {
	  window.location.href='/usermaps/add.html';
	});
	
      {/literal}
      {/if}
    {/if}
    {literal}
    clusterer = new ymaps.Clusterer({clusterDisableClickZoom: false, gridSize : 64, minClusterSize: 3});
    myMap.controls
      .add('zoomControl', {top: '5', left: '5'})
      {/literal}{if $cfg.maps_traffic}{literal}
      .add('trafficControl', {
                    right: '5',
                    top: '40'
                })
      {/literal}{/if}{literal}
      .add('typeSelector')
      {/literal}
      {if $user_id}
      {literal}
      .add(addOrg, {bottom: '20', left: '10'})
      .add(addHimself, {bottom: '20', left: '180'});
      {/literal}
      {/if}
    {literal}
    var bound = myMap.getBounds();
    loadPoint(bound);
//загружаем точки 
    myMap.events.add('balloonopen', function(e) {
	  balloonopen = 1;
	});
	
    myMap.events.add('balloonclose', function(e) {
	  balloonopen = 0;
	});
	
    myMap.events.add('boundschange', function(e) {
	if(balloonopen != 1)
	{
	  loadPoint(myMap.getBounds());
	}
	balloonopen = 0;
	});
  })

  function loadPoint(bound)
  {
    $.ajax({
      url:    	'/usermaps/ajax_structure',
      data: 	"bound="+bound,
      type:   	'post',
      success: function(string)
      {
	var points = JSON.parse(string);
	myMap.geoObjects.remove(clusterer);
	clusterer = new ymaps.Clusterer({clusterDisableClickZoom: false, gridSize : 64, minClusterSize : 3});
	$(points).each(function() {
	  if(this.type_id == 1)
	  {
// 	Если нет авки ставим пустую
	    if(!this.imageurl)
	    {
	      this.imageurl = "nopic.jpg";
	    }
//	Ставим цвета рейтинга
	    if(this.rating > 0)
	    {
	      var color = "green";
	    }
	    else
	    {
	      var color = "red";
	    }
	    
// 	Рисуем содержимое балуна    
	    var BalloonContentFooter ='<a href="/users/'+this.user_id+'/sendmessage.html"><img src="/templates/_default_/images/icons/message.png"></a><a href="/users/'+this.login+'"><img src="/templates/_default_/images/icons/user.png"></a>'
	    var BalloonContentBody = '<div class="balloonContentBody"><img src="/images/users/avatars/small/'+this.imageurl+'">'+this.nickname+'<div class="rating"><div class="'+color+'">'+this.rating+'</div></div></div>';	    
//	Ресуем подвал балуна    
	    if(user_id != 0)
	    {
	      var BalloonContentFooter ='<a href="/users/'+this.user_id+'/sendmessage.html"><img src="/templates/_default_/images/icons/message.png"></a><a href="/users/'+this.login+'"><img src="/templates/_default_/images/icons/user.png"></a>'
	    }
	    else
	    {
	      var BalloonContentFooter ='<a href="/users/'+this.login+'"><img src="/templates/_default_/images/icons/user.png"></a>'
	    }
	  }
	  else
	  {
	    var BalloonContentBody = '<div class="balloonContentBody"><a href="/usermaps/view'+this.id+'.html"><img src="/components/usermaps/img/'+this.catname+'_big.png">'+this.title+'</a></div>';
	    var BalloonContentFooter = '';
	  }
//	Пользовательская точка и размеры  
	  if(this.type_id == 1 && this.user_id == user_id)
	  {
	    var IconImageHref = "/components/usermaps/img/marker_me.png";
	    var IconImageSize =  [40, 40];
	    var IconImageOffset = [-20, -40];
	    var userpoint = 1;
	  }
	  else
	  {
	    var IconImageHref = "/components/usermaps/img/"+this.catname+".png";
	    var IconImageSize = [20, 20];
	    var IconImageOffset = [-10, -20];
	    var userpoint = 0;
	  }
	  
	  var placemark = new ymaps.Placemark([this.y,this.x],
	      {
		balloonContentHeader: this.cattitle,
		balloonContentBody: BalloonContentBody,
		balloonContentFooter: BalloonContentFooter
	      }
	      ,
	      {
		iconImageHref: IconImageHref,
		iconImageSize: IconImageSize,
		iconImageOffset: IconImageOffset
	      });
	  if(userpoint == 0)
	  {
	    clusterer.add(placemark);
	  }
	  else
	  {
	    myMap.geoObjects.add(placemark);
	  }
	});
	myMap.geoObjects.add(clusterer);
      }
    });
  }
{/literal}
</script>
<div id="myMap" style="width: 100%; height: 300px"></div>