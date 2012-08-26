{add_css file="components/usermaps/css/usermaps.css"}
<h3>Последние добавленые точки {if $category} в категории "{$category.title}"{/if}</h3>
<div class="poi">
{if $poi}
  <ul>
    {if !$category}
      <li><a href="/usermaps/poi_add.html">Добавить</a></li>
    {/if}
    {foreach key=id item=place from=$poi}
      <li><img src="/components/usermaps/img/{$place.name}_big.png">{$place.category_title} <a href="/usermaps/view{$place.id}.html">"{$place.title}"</a></li>
    {/foreach}
  </ul>
{/if}
</div>