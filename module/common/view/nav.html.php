<div class='navbar navbar-inverse navbar-fixed-top' data-spy='affix' data-offset-top='200'>
  <div class='navbar-inner'>
    <div class='container-fluid'>
      <button type='button' class='btn btn-navbar' data-toggle='collapse' data-target='.nav-collapse'>
        <span class='icon-bar'></span>
        <span class='icon-bar'></span>
        <span class='icon-bar'></span>
      </button>
      <?php echo html::a($this->createLink('index'), $lang->zentaophp, '', "class='brand'")?>
      <div class='nav-collapse collapse'>
        <ul class='nav'>
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
        <div class="btn-group pull-right">
          <?php 
          foreach($config->langs as $langCode => $langLabel)
          {
              $btnClass = $app->getClientLang() == $langCode ? 'btn-primary' : '';
              echo html::a($app->getURI(), $langLabel, '', "class='btn btn-small $btnClass lang-switcher' data-lang='$langCode'");
          }
          ?>
        </div>
      </div>
    </div>
  </div>
</div>
