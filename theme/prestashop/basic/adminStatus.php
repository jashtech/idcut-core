<h2><?php echo $heading; ?></h2>
<?php if(!isset($store_active)): ?>
<p class="warning-inline error"><?php echo $error; ?></p>
<?php elseif((bool)$store_active): ?>
<p><?php echo $message; ?></p>
<?php else: ?>
<p><?php echo $message; ?></p>
<?php endif; ?>
