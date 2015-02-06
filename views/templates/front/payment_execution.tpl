{capture name=path}
    <a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}" title="{l s='Go back to the Checkout' mod='kickass'}">{l s='Checkout' mod='kickass'}</a><span class="navigation-pipe">{$navigationPipe}</span>{l s='Kickass payment' mod='kickass'}
    {/capture}

{include file="$tpl_dir./breadcrumb.tpl"}

<h2>{l s='Order summary' mod='kickass'}</h2>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

{if isset($nbProducts) && $nbProducts <= 0}
    <p class="warning">{l s='Your shopping cart is empty.' mod='kickass'}</p>
{else}

    <h3>{l s='Kickass payment' mod='kickass'}</h3>
    <form action="{$link->getModuleLink('kickass', 'validation', [], true)|escape:'html'}" method="post">
        <p>
            {l s='You have chosen to pay by kickass.' mod='kickass'}
            <br/><br />
            {l s='Here is a short summary of your order:' mod='kickass'}
        </p>
        <p style="margin-top:20px;">
            - {l s='The total amount of your order comes to:' mod='kickass'}
            <span id="amount" class="price">{displayPrice price=$total}</span>
            {if $use_taxes == 1}
                {l s='(tax incl.)' mod='kickass'}
            {/if}
        </p>
        <p>
            -
            {if isset($currencies) && $currencies|@count > 1}
                {l s='We accept several currencies to receive payments by kickass.' mod='kickass'}
                <br /><br />
                {l s='Choose one of the following:' mod='kickass'}
                <select id="currency_payement" name="currency_payement" onchange="setCurrency($('#currency_payement').val());">
                    {foreach from=$currencies item=currency}
                        <option value="{$currency.id_currency}" {if isset($currencies) && $currency.id_currency == $cust_currency}selected="selected"{/if}>{$currency.name}</option>
                    {/foreach}
                </select>
            {else}
                {l s='We allow the following currencies to be sent by kickass:' mod='kickass'}&nbsp;<b>{$currencies.0.name}</b>
                <input type="hidden" name="currency_payement" value="{$currencies.0.id_currency}" />
            {/if}
        </p>
        <p>
            <b>{l s='Please confirm your order by clicking \'I confirm my order\'.' mod='kickass'}</b>
        </p>
        <p class="cart_navigation" id="cart_navigation">
            <input type="submit" value="{l s='I confirm my order' mod='kickass'}" class="exclusive_large"/>
            <a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html'}" class="button_large">{l s='Other payment methods' mod='kickass'}</a>
        </p>
    </form>
{/if}
