<script>
    function IDCutOauthWindowOpen(href){
        window.open(href, "_blank", "toolbar=no, scrollbars=yes, resizable=no, top=500, left=500, width=600, height=600");
        return false;
    }
</script>

<p>
    <?php if (!$connected && $authorizationUrl): ?>
        <a onclick="return IDCutOauthWindowOpen(this.getAttribute('href'));" href="<?php echo $authorizationUrl; ?>" type="button" class="btn btn-primary btn-lg">Connect</a>
    <?php endif; ?>

    <?php if ($authorizationUrl): ?>
        <a onclick="return IDCutOauthWindowOpen(this.getAttribute('href'));" href="<?php echo $authorizationUrl; ?>" type="button" class="btn btn-info btn-lg">Reconnect</a>
    <?php endif; ?>

    
   <?php /* if ($connected): ?>
        <a type="button" class="btn btn-danger btn-lg ">Disconnect</a>
    <?php endif;  */ ?>
</p>