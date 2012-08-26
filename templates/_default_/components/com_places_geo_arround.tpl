{foreach key=id item=point from=$points}
  <div class="arroundpoint">
  <a href="/usermaps/view{$point.id}.html"><img src="/components/usermaps/img/{$point.category_icon}_big.png" title="{$point.category_title}">{$point.title}</a> : {$point.distance} ì.
   </div>
{/foreach}