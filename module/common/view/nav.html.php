<div id='header' class='bg-primary with-shadow'>
<div class='container navbar navbar-default' role="navigation">
  <div class='navbar-header'>
    <?php echo html::a($this->createLink('index'), $lang->zentaophp, "class='navbar-brand' style='font-size:17px'")?>
  </div>
  <div class="collapse navbar-collapse">
    <ul class='nav navbar-nav nav-reverce'>
      <?php
      foreach($lang->menu as $menuModule => $menuLabel)
      {
          $menuClass = $moduleName == $menuModule ? 'active' : '';
          echo "<li class='$menuClass'>";
          echo html::a($this->createLink($menuModule), $menuLabel);
          echo '</li>';
      }
      ?>
    </ul>
     <ul class="nav navbar-nav navbar-right">
      <li>
        <div class='btn-group' style='margin-right:5px;margin-top:8px;'>
          <?php 
          foreach($config->langs as $langCode => $langLabel)
          {
              $btnClass = $app->getClientLang() == $langCode ? 'btn-primary' : '';
              echo "<button class='btn btn-sm $btnClass lang-switcher' data-lang='$langCode' onclick='switchLang(this)'>$langLabel</button>";
          }
          ?>
        </div>
      </li>
    </ul>
  </div>
</div>
</div>
<style>
#header{ background:#333;}
#header .navbar{background:transparent; border:none;}
#header .navbar-header a{color:#fff;}
#header .nav > li > a{color:#fff;}
#header .nav > li.active > a{background:#444;}
</style>
