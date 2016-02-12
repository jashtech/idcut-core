<script>
    function IDCutOauthWindowOpen(href){
        window.open(href, "_blank", "toolbar=no, scrollbars=yes, resizable=no, top=500, left=500, width=600, height=600");
        return false;
    }
</script>

<p>
    <?php if (!$connected && $authorizationUrl): ?>
        <button
            onclick="return IDCutOauthWindowOpen(this.getAttribute('data-href'));"
            data-href="<?php echo $authorizationUrl; ?>"
            class="btn btn-primary btn-lg"
        ><?php echo $connect_button_label; ?></button>
            
    <?php endif; ?>

    <?php if ($authorizationUrl): ?>
        <button
            onclick="return IDCutOauthWindowOpen(this.getAttribute('data-href'));"
            data-href="<?php echo $authorizationUrl; ?>"
            class="btn btn-info btn-lg"
        ><?php echo $reconnect_button_label; ?></button>
    <?php endif; ?>

    
   <?php /* if ($connected): ?>
        <a type="button" class="btn btn-danger btn-lg ">Disconnect</a>
    <?php endif;  */ ?>
</p>