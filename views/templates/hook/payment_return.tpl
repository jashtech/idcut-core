{if $status == 'ok'}
    <p>{l s='Your order on %s is complete.' sprintf=$shop_name mod='kickass'}
        <br /><br />
        {l s='Your payment must include:' mod='kickass'}
        <br /><br />- {l s='Payment amount.' mod='kickass'} <span class="price"><strong>{$total_to_pay}</strong></span>
                {if !isset($reference)}
            <br /><br />- {l s='Do not forget to insert your order number #%d.' sprintf=$id_order mod='kickass'}
        {else}
            <br /><br />- {l s='Do not forget to insert your order reference %s.' sprintf=$reference mod='kickass'}
        {/if}
        <br /><br />{l s='An email has been sent to you with this information.' mod='kickass'}
        <br /><br /><strong>{l s='Your order will be sent as soon as we receive your payment.' mod='kickass'}</strong>
        <br /><br />{l s='For any questions or for further information, please contact our' mod='kickass'} <a href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='customer service department.' mod='kickass'}</a>.
    </p>
{else}
    <p class="warning">
        {l s='We have noticed that there is a problem with your order. If you think this is an error, you can contact our' mod='kickass'} 
        <a href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='customer service department.' mod='kickass'}</a>.
    </p>
{/if}
