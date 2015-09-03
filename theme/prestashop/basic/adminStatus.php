<h2>IdealCutter client status</h2>
<dl>

    <dt>Core</dt>
    <dd><?php echo $coreClass; ?></dd>

    <dt>Config</dt>
    <dd><?php echo $configClass; ?></dd>

    <dt>API Client</dt>
    <dd><?php echo $apiClientClass; ?></dd>

    <dt>Cipher</dt>
    <dd><?php echo $cipherClass; ?></dd>

    <dt>Authenticated</dt>
    <dd><?php echo $authenticated; ?></dd>

    <dt>API Access Token</dt>
    <dd><?php echo $accessToken; ?></dd>

    <dt>API Access Token Info</dt>
    <dd><pre><?php echo $accessTokenInfo; ?></pre></dd>

    <dt>Service URL</dt>
    <dd><?php echo $serviceUrl; ?></dd>

    <dt>Cipher Test</dt>
    <dd><?php echo $cipherTest; ?></dd>

    <dt>API Client version</dt>

    <dd><?php echo $apiClientVersion; ?></dd>

    <dt>Response Test</dt>
    <dd><pre><?php echo $testResponse; ?></pre></dd>


    <dt>DUMP</dt>
    <dd><pre><?php var_dump($dump); ?></pre></dd>

</dl>
