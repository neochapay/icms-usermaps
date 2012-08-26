<h3>Настройки карты</h3>
<form action="" method="POST">
<fieldset>
  <legend>Приватность</legend>
    {if $cfg.maps_user_del != 0}
    <input type="checkbox" name="maps_user_del"> <b>Удалить точку пользователя</b> <br/>
    {/if}

    {if $cfg.maps_chekin_del != 0}
    <input type="checkbox" name="maps_chekin_del"> <b>Удалить отметки о посещении</b> <br/>
    {/if}
  </fieldset>
  <input type="submit" value="Отправить">
</form>