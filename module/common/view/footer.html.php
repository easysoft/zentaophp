<?php if(isset($pageJS)) js::internal($pageJS);?>
<?php if($this->server->HTTP_X_PJAX == false):?>
  </div>
</body>
</html> 
<?php endif;?>
