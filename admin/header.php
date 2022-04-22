<header>
    <a id="logo" href="/admin.php">Панель управления</a>
    <div id="user">
        <div id="setting"><a href="/admin/settings.php">&#9881;</a></div>
        <div id="userName"><? echo $user ?></div>
    </div>
</header>
<ul class="menu">
    <a href="/admin.php"><li <? if($menu === 'home') echo 'class="selected"' ?>>Главная</li></a>
    <a href="/admin.php?menu=config"><li <? if($menu === 'config') echo 'class="selected"' ?>>Конфиг</li></a>
    <a href="/admin.php?menu=quests"><li <? if($menu === 'quests') echo 'class="selected"' ?>>Квесты</li></a>
    <a href="/admin.php?menu=roles"><li <? if($menu === 'roles') echo 'class="selected"' ?>>Роли</li></a>
    <a href="/admin.php?menu=mappacks"><li <? if($menu === 'mappacks') echo 'class="selected"' ?>>Мап паки</li></a>
    <a href="/admin.php?menu=gauntlets"><li <? if($menu === 'gauntlets') echo 'class="selected"' ?>>Гаунтлеты</li></a>
</ul>