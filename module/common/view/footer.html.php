<?php if(isset($pageJS)) js::execute($pageJS);?>
<?php if($this->server->HTTP_X_PJAX == false):?>
  </div>
</body>
</html> 
<?php endif;?>
