{if $status == 'ok'}
    <p>{l s='Your order on %s is complete.' sprintf=$shop_name mod='idcut'}
        <br /><br />
        {l s='Your payment must include:' mod='idcut'}
        <br /><br />- {l s='Payment amount.' mod='idcut'} <span class="price"><strong>{$total_to_pay}</strong></span>
                {if !isset($reference)}
            <br /><br />- {l s='Do not forget to insert your order number #%d.' sprintf=$id_order mod='idcut'}
        {else}
            <br /><br />- {l s='Do not forget to insert your order reference %s.' sprintf=$reference mod='idcut'}
        {/if}
        <br /><br />{l s='An email has been sent to you with this information.' mod='idcut'}
        <br /><br /><strong>{l s='Your order will be sent as soon as we receive your payment.' mod='idcut'}</strong>
        <br /><br />{l s='For any questions or for further information, please contact our' mod='idcut'} <a href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='customer service department.' mod='idcut'}</a>.
    </p>
{else}
    {if isset($transaction->error_code) && !empty($transaction->error_code)}
        <div class="alert alert-danger">
            <p>
                 {$transaction->error_code}
            </p>
        </div>
    {elseif isset($transaction->message) && !empty($transaction->message)}
        <div class="alert alert-danger">
            <p>
                 {$transaction->message|escape:'html'}
            </p>
        </div>
    {/if}
    <p class="warning">
        {l s='We have noticed that there is a problem with your order. If you think this is an error, you can contact our' mod='idcut'} 
        <a href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='customer service department.' mod='idcut'}</a>.
    </p>
{/if}
