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
            <h3><i class="icon-university"></i> {l s='Transaction' mod='idcut'}</h3>
            <div class="form-horizontal">
                <div class="form-group">
                        <label class="col-lg-3 control-label">{l s='Order id:'  mod='idcut'}</label>
                        <div class="col-lg-9"><p class="form-control-static">{$transaction->id_order}</p></div>
                </div>
                <div class="form-group">
                        <label class="col-lg-3 control-label">{l s='Transaction id:'  mod='idcut'}</label>
                        <div class="col-lg-9"><p class="form-control-static">{$transaction->transaction_id}</p></div>
                </div>
                <div class="form-group">
                        <label class="col-lg-3 control-label">{l s='Status:'  mod='idcut'}</label>
                        <div class="col-lg-9"><p class="form-control-static">{$transaction->getStatus()}</p></div>
                </div>
                <div class="form-group">
                        <label class="col-lg-3 control-label">{l s='Error Code:'  mod='idcut'}</label>
                        <div class="col-lg-9"><p class="form-control-static">{$transaction->error_code}</p></div>
                </div>
                <div class="form-group">
                        <label class="col-lg-3 control-label">{l s='Message:'  mod='idcut'}</label>
                        <div class="col-lg-9"><p class="form-control-static">{$transaction->message}</p></div>
                </div>
                <div class="form-group">
                        <label class="col-lg-3 control-label">{l s='Last updated:'  mod='idcut'}</label>
                        <div class="col-lg-9"><p class="form-control-static">{$transaction->date_edit}</p></div>
                </div>
            </div>
        </div>
    </div>
</div>
{/block}