<div>
{include file="$tpl_dir./errors.tpl"}
<h1>{l s="OAuth2 verification" mod='idcut'}</h1>

{if $resultInfo}
    <p>{l s="Token saved" mod='idcut'}</p>
{else}
    <p>{l s="Try again" mod='idcut'}</p>
{/if}
<a onclick="window.opener.location.href = window.opener.location.href; self.close();" href="">{l s="Close this window" mod='idcut'}</a>
</div>