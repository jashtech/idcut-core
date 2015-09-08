{capture name=path}
    {l s='IdealCutter Payment Error' mod='idealcutter'}
    {/capture}

<h2>{l s='IdealCutter payment something went wrong' mod='idcut'}</h2>
{if isset($payment_errors) && !empty($payment_errors)}
    {foreach from=$payment_errors item=error}
        <p class="alert alert-error">{$error|escape:'html'}</p>
    {/foreach}
{/if}
