{capture name=path}
    <a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}" title="{l s='Go back to the Checkout' mod='idcut'}">{l s='Checkout' mod='idcut'}</a><span class="navigation-pipe">{$navigationPipe}</span>{l s='IdealCutter payment' mod='idealcutter'}
    {/capture}

{include file="$tpl_dir./breadcrumb.tpl"}

<h2>{l s='Order summary' mod='idcut'}</h2>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

{if isset($nbProducts) && $nbProducts <= 0}
    <p class="alert alert-warning">{l s='Your shopping cart is empty.' mod='idcut'}</p>
{else}
    {if isset($error_messages) && !empty($error_messages)}
        {foreach from=$error_messages item=error}
            <p class="alert alert-warning">{$error|escape:'html'}</p>
        {/foreach}
    {/if}
    
    <h3>{l s='IdealCutter payment' mod='idcut'}</h3>
    <form action="{$link->getModuleLink('idcut', 'payment', [], true)|escape:'html'}" method="post">
        <p>
            {l s='You have chosen to pay by idealcutter.' mod='idcut'}
            <br/><br />
            {l s='Here is a short summary of your order:' mod='idcut'}
        </p>
        <p style="margin-top:20px;">
            - {l s='The total amount of your order comes to:' mod='idcut'}
            <span id="amount" class="price">{displayPrice price=$total}</span>
            {if $use_taxes == 1}
                {l s='(tax incl.)' mod='idcut'}
            {/if}
        </p>
        <p>
            -
            {if isset($currencies) && $currencies|@count > 1}
                {l s='We accept several currencies to receive payments by idealcutter.' mod='idcut'}
                <br /><br />
                {l s='Choose one of the following:' mod='idcut'}
                <select id="currency_payement" name="currency_payement" onchange="setCurrency($('#currency_payement').val());">
                    {foreach from=$currencies item=currency}
                        <option value="{$currency.id_currency}" {if isset($currencies) && $currency.id_currency == $cust_currency}selected="selected"{/if}>{$currency.name}</option>
                    {/foreach}
                </select>
            {else}
                {l s='We allow the following currencies to be sent by idealcutter:' mod='idcut'}&nbsp;<b>{$currencies.0.name}</b>
                <input type="hidden" name="currency_payement" value="{$currencies.0.id_currency}" />
            {/if}
        </p>
        <p>
            - {l s='Please choose wisely' mod='idcut'}
        </p>
{*            {if $can_create_deal}*}
        <p class="radio-inline">
                <label>
                        <input type="radio" name="deal_join" id="deal_new" value="0"{if isset($smarty.post.deal_join) && $smarty.post.deal_join == 0} checked="checked"{/if} />
                        {l s='Create new deal' mod='idcut'}
                </label>
        </p>
{*            {/if}*}
        <p class="radio-inline">
            <label>
                    <input type="radio" name="deal_join" id="deal_join" value="1"{if isset($smarty.post.deal_join) && $smarty.post.deal_join == 1} checked="checked"{/if} />
                    <input type="text" name="deal_token" placeholder="{l s='Join to existing deal' mod='idcut'}" value="{if isset($deal_token)}{$deal_token}{/if}">
            </label>
        </p>
        <p>
            <b>{l s='Please confirm your order by clicking \'I confirm my order\'.' mod='idcut'}</b>
        </p>
        {*<p class="cart_navigation" id="cart_navigation">
            <input type="submit" name="confirm_order" value="{l s='I confirm my order' mod='idcut'}" class="exclusive_large"/>
            <a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html'}" class="button_large">{l s='Other payment methods' mod='idcut'}</a>
            
            
        </p>*}
        <p class="cart_navigation clearfix" id="cart_navigation">
        	<a 
            class="button-exclusive btn btn-default" 
            href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}">
                <i class="icon-chevron-left"></i>{l s='Other payment methods' mod='idcut'}
            </a>
            <button 
            class="button btn btn-default button-medium" 
            type="submit"
            name="confirm_order" 
            value="confirm_order_val"
            >
                <span>{l s='I confirm my order' mod='idcut'}<i class="icon-chevron-right right"></i></span>
            </button>
        </p>
    </form>
{/if}
