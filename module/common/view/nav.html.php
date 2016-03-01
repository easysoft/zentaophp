<div class='navbar navbar-default' role="navigation">
  <div class='navbar-header'>
    <?php echo html::a($this->createLink('index'), $lang->zentaophp, "class='navbar-brand'")?>
  </div>
  <div class="collapse navbar-collapse">
    <ul class='nav navbar-nav'>
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
