{add_css file="components/usermaps/css/usermaps.css"}
<h3>Раздел "{$root.title}"</h3>
<div class="poi">
{if $subcat}
  <ul>
    {foreach key=id item=place from=$subcat}
      <li><img src="/components/usermaps/img/{$place.name}_big.png">{$place.category_title} <a href="/usermaps/category{$place.id}.html">"{$place.title}"</a></li>
    {/foreach}
  </ul>
{/if}
{if $is_admin}
<h1 class="con_heading">Добавить категорию</h1>
Перед добавлением новой категории загрузите иконки категории в папку $ROOT/compotetns/usermaps/img/ . Необходимы 2 иконки большая (name_big.png) размером 40*40 и маленькую (name.png) размером 20*20. Формат картинок PNG. Выравнивание по низу - по середине. Если у Вас возникли проблемы с поиском картинок к категориям рекомендуем брать <a href="http://mapicons.nicolasmollet.com/">Здесь</a>
<form action="" method="post">
<div id="basic">
  <table width="661" border="0" cellpadding="10" cellspacing="0" class="proptable">
    <tr>
      <td width="250">
	<strong>Имя иконки: </strong><br/>
	<span class="hinttext">
	    без ".png" и "_big"
        </span>
      </td>
      <td valign="top">
	<input name="name" type="text" style="width:240px"/>
      </td>
    </tr>
    <tr>
      <td width="250">
	<strong>Заголовок: </strong><br/>
	<span class="hinttext">
	    в единственном числе
        </span>
      </td>
      <td valign="top">
	<input name="title" type="text" style="width:240px"/>
      </td>
    </tr>
  </table>
<input type="submit" name="send" value="Добавить" />
  </div>
</form>
{/if}
</div>