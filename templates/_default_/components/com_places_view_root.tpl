{add_css file="components/usermaps/css/usermaps.css"}
<h3>Разделы точек</h3>
<div class="poi">
{if $subcat}
  <ul>
    {foreach key=id item=place from=$subcat}
      <li><a href="/usermaps/category{$place.id}.html"> {$place.title} </a></li>
    {/foreach}
      <li><a href="/usermaps/poi.html">Последние</a></li>
      <li><a href="/usermaps/poi_add.html">Добавить точку</a></li>
  </ul>
{/if}
{if $is_admin}
<h1 class="con_heading">Добавить раздел</h1>
<form action="" method="post">
<div id="basic">
  <table width="661" border="0" cellpadding="10" cellspacing="0" class="proptable">
    <tr>
      <td width="250">
	<strong>Имя на латинице: </strong>
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
{if $user_id != 0}
  <input type="submit" name="send" value="Добавить" />
{/if}
  </div>
</form>
{/if}
</div>