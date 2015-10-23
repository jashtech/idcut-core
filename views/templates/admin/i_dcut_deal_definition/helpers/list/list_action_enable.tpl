{*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if $enabled}
<span class="list-action-enable action-enabled" title="{l s='Yes' mod='idcut'}">
    <i class="icon-check"><span class="hidden">{l s='Yes' mod='idcut'}</span></i>
</span>
{else}
<span class="list-action-enable action-disabled" title="{l s='No' mod='idcut'}">
    <i class="icon-remove"><span class="hidden">{l s='No' mod='idcut'}</span></i>
</span>
{/if}
{*{if $enabled}
<a class="list-action-enable{if isset($ajax) && $ajax} ajax_table_link{/if} action-enabled" href="{$url_enable|escape:'html':'UTF-8'}"{if isset($confirm)} onclick="return confirm('{$confirm}');"{/if} title="{l s='Enabled' mod='idcut'}">
	<i class="icon-check"></i>
	<i class="icon-remove hidden"></i>
</a>
{else}
<span class="list-action-enable action-disabled" title="{l s='Disabled' mod='idcut'}">
	<i class="icon-remove"></i>
</span>
{/if}*}