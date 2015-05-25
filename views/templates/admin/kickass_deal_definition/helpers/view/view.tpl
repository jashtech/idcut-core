{*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{block name="override_tpl"}
<div class="row">
    <div class="col-lg-12">
        <div class="panel">
            <h3><i class="icon-university"></i> {l s='Deal definition' mod='kickass'}</h3>
            <div class="form-horizontal">
                <div class="form-group">
                        <label class="col-lg-3 control-label">{l s='Access id:'  mod='kickass'}</label>
                        <div class="col-lg-9"><p class="form-control-static">{$deal_definition->uuid}</p></div>
                </div>
                <div class="form-group">
                        <label class="col-lg-3 control-label">{l s='Starts:'  mod='kickass'}</label>
                        <div class="col-lg-9"><p class="form-control-static">{$deal_definition->start_date}</p></div>
                </div>
                <div class="form-group">
                        <label class="col-lg-3 control-label">{l s='Ends:'  mod='kickass'}</label>
                        <div class="col-lg-9"><p class="form-control-static">{$deal_definition->end_date}</p></div>
                </div>
                <div class="form-group">
                        <label class="col-lg-3 control-label">{l s='Time to join:'  mod='kickass'}</label>
                        <div class="col-lg-9"><p class="form-control-static">{$deal_definition->formatTtl()}</p></div>
                </div>
                <div class="form-group">
                        <label class="col-lg-3 control-label">{l s='Time to return money:'  mod='kickass'}</label>
                        <div class="col-lg-9"><p class="form-control-static">{$deal_definition->formatLocktime()}</p></div>
                </div>
                <div class="form-group">
                        <label class="col-lg-3 control-label">{l s='Maximum users:'  mod='kickass'}</label>
                        <div class="col-lg-9"><p class="form-control-static">{$deal_definition->user_max}</p></div>
                </div>
                <div class="form-group">
                        <label class="col-lg-3 control-label">{l s='Minimum order value:'  mod='kickass'}</label>
                        <div class="col-lg-9"><p class="form-control-static">{$deal_definition->min_order_value}</p></div>
                </div>
                <div class="form-group">
                        <label class="col-lg-3 control-label">{l s='Return Type:'  mod='kickass'}</label>
                        <div class="col-lg-9"><p class="form-control-static">{if $deal_definition->range_type == 1}{l s='Amount'  mod='kickass'}{else}{l s='Percent'  mod='kickass'}{/if}</p></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel">
            <h3><i class="icon-money"></i> {l s='Ranges'  mod='kickass'}</h3>
            <div class="form-horizontal">
                <div class="form-group">
                        <label class="col-lg-4 control-label">{l s='Minimum number of Participants:'  mod='kickass'}</label>
                        <div class="col-lg-8"><p class="form-control-static">{l s='Return value' mod='kickass'}</p></div>
                </div>
                {foreach from=$deal_definition->ranges item=range name=ranges}
                <div class="form-group">
                        <label class="col-lg-4 control-label">{$range->min_participants_number}</label>
                        <div class="col-lg-8"><p class="form-control-static">{if $deal_definition->range_type == 1}{$range->discount_size}{else}{$range->discount_size}%{/if}</p></div>
                </div>
                {/foreach}
            </div>
        </div>
    </div>
</div>
{/block}