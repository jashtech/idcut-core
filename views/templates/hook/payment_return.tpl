<div class="idealcutter_order_confirmation">
<h3><img class="idcut_payment_logo" src="{$this_path_idcut}img/idealcutter.png" alt="{l s='IdealCutter payment' mod='idcut'}" /></h3>
{if $status == 'ok'}
<h5>{l s='Thank You!' mod='idcut'}</h5>
    <p>
        {l s='Your order on %s is complete.' sprintf=$shop_name mod='idcut'}
        <br /><br />
        {l s='Value of Your order is:' mod='idcut'} <span class="price"><strong>{$total_to_pay}</strong></span>
        {if !isset($reference)}
            {l s='If You want to contact about just placed order, please give us this order number:' mod='idcut'} <span class="order_num">{$id_order}</span>
        {else}
            {l s='If You want to contact about just placed order, please give us this order reference:' mod='idcut'} <span class="order_num">{$reference}</span>
        {/if}<br /><br />
        {l s='An email has been sent to you with this order confirmation.' mod='idcut'}<br /><br />
        {l s='Your order will be sent as soon as we receive your payment.' mod='idcut'}<br /><br />
        {l s='For any questions or for further information, please contact our' mod='idcut'} <a href="{$link->getPageLink('contact', true, null, "id_order={$id_order}")|escape:'html'}">{l s='customer service department.' mod='idcut'}</a>.
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
</div>