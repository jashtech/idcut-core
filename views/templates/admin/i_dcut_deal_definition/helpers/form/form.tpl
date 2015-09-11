{extends file="helpers/form/form.tpl"}

{block name="input"}
    {if $input.type == 'range_type'}
        <span class="switch prestashop-switch fixed-width-lg">
                {foreach $input.values as $value}
                <input type="radio" name="{$input.name}"{if $value.value == 1} id="{$input.name}_on"{else} id="{$input.name}_off"{/if} value="{$value.value}"{if $fields_value[$input.name] == $value.value} checked="checked"{/if}{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}/>
                {strip}
                <label {if $value.value == 1} for="{$input.name}_on"{else} for="{$input.name}_off"{/if}>
                        {if $value.value == 1}
                                {l s='Amount'}
                        {else}
                                {l s='Percent'}
                        {/if}
                </label>
                {/strip}
                {/foreach}
                <a class="slide-button btn"></a>
        </span>
    {elseif $input.type == 'ranges'}
        <input type="hidden" name="{$input.name}" id="{$input.name}" value='{$input.value}' />
        <script>
            {literal}
            $(document).ready( function() {
                $('input#new_min_participants_number').change(function($e){
                    var $mpn = $(this).val();
                    if(isUint32($mpn)){
                        if($(this).hasClass('invalid')){
                            $(this).removeClass('invalid');
                        }
                    }else if(!($(this).hasClass('invalid'))){
                        $(this).addClass('invalid');
                    }
                });
                $('input#new_discount_size').change(function($e){
                    var $mpn = $(this).val();
                    if(isUint32($mpn)){
                        if($(this).hasClass('invalid')){
                            $(this).removeClass('invalid');
                        }
                    }else if(!($(this).hasClass('invalid'))){
                        $(this).addClass('invalid');
                    }
                });
                $("#current_ranges").on('click', 'a',function() {
                    $(this).parent().parent().remove();
                    rebuildJsonValue('{/literal}{$input.name}{literal}');
                });
                $('#ranges_add').click(function() {
                        if(isUint32($('input#new_min_participants_number').val()) && isUint32($('input#new_discount_size').val())){
                            var mpn = parseInt($('input#new_min_participants_number').val());
                            var nds = parseInt($('input#new_discount_size').val());
                            var $elid = '#ranges_'+mpn;
                            var $matched = $('#current_ranges').children($elid);
                            if(typeof $matched !== 'undefined' && $matched.length>0){
                                $matched.children("div.discount_size").html(nds);
                                $matched.attr("data-discount_size", nds);
                            }else{
                                var smaller = 0;
                                $('#current_ranges').children().each(function(){
                                    if($(this).attr('data-min_participants_number') < mpn){
                                        smaller = $(this);
                                    }else{
                                        return;
                                    }
                                });
                                var $html_el = "<div class='row' id='ranges_"+mpn+"' data-min_participants_number='"+mpn+"'  data-discount_size='"+nds+"'><div class='col-lg-5 min_participants_number'>"+mpn+"</div><div class='col-lg-5 discount_size'>"+nds+"</div><div class='col-lg-2'><a class='list-toolbar-btn' href='#current_ranges'><i class='process-icon-cancel'></i></a></div></div>";
                                if(smaller===0){
                                    $("#current_ranges").append($html_el);
                                }else{
                                    smaller.after($html_el);
                                }
                            }
                            rebuildJsonValue('{/literal}{$input.name}{literal}');
                        }else{
                            $mpn = $('input#new_min_participants_number');
                            if(isUint32($mpn.val())){
                                if($mpn.hasClass('invalid')){
                                    $mpn.removeClass('invalid');
                                }
                            }else if(!($mpn.hasClass('invalid'))){
                                $mpn.addClass('invalid');
                            }
                            
                            $nds = $('input#new_discount_size');
                            if(isUint32($nds.val())){
                                if($nds.hasClass('invalid')){
                                    $nds.removeClass('invalid');
                                }
                            }else if(!($nds.hasClass('invalid'))){
                                $nds.addClass('invalid');
                            }
                        }
                        return false;
                });
            });

            function rebuildJsonValue(name){
                var arr_to_parse = [];
                $('#current_ranges').children().each(function(){
                   var mpn = parseInt($(this).attr("data-min_participants_number"),10);
                   var nds = parseInt($(this).attr("data-discount_size"),10);
                   arr_to_parse.push(''+mpn+'-'+nds);
                });
                console.log("arr_to_parse");
                console.log(arr_to_parse);
                console.log("input#"+name);
                console.log($("input#"+name));
                console.log(JSON.stringify(arr_to_parse));

                $("input#"+name).val(JSON.stringify(arr_to_parse));
            }
            {/literal}
        </script>
        <div id="{$input.name}_ranges">
            <div class="row" id="ranges_new">
                <div class="col-lg-5">{l s='Minimum users:'}<br /><input id="new_min_participants_number" name="new_min_participants_number" value="" /></div>
                <div class="col-lg-5">{l s='Return value (percent):'}<br /><input id="new_discount_size" name="new_discount_size" value="" /></div>
                <div class="col-lg-2"><br /><a id="ranges_add" class="list-toolbar-btn" href="#ranges_add" title="{l s='Add'}"><i class="process-icon-new"></i></a></div>
            </div>
            <div id="current_ranges">
                {foreach $input.current_ranges as $r}
                <div class="row" id="ranges_{$r->min_participants_number}" data-min_participants_number="{$r->min_participants_number}"  data-discount_size="{$r->discount_size}">
                    <div class="col-lg-5 min_participants_number">{$r->min_participants_number}</div>
                    <div class="col-lg-5 discount_size">{$r->discount_size}</div>
                    <div class="col-lg-2"><a class="list-toolbar-btn" href="#current_ranges"><i class="process-icon-cancel"></i></a></div>
                </div>
                {/foreach}
            </div>
        </div>
    {elseif $input.type == 'friendly_seconds'}
        <div id="{$input.name}_friendly_seconds" class="friendly_seconds">
            <input type="hidden" name="{$input.name}" id="{$input.name}" class="friendly_real_value" value='{(int)$fields_value[$input.name]}' {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if} />
            {assign var='days_value' value=floor(((int)$fields_value[$input.name])/(3600*24))}
            {assign var='hours_value' value=floor((((int)$fields_value[$input.name])-$days_value*3600*24)/3600)}
            <input id="{$input.name}_days" name="{$input.name}_days" class="friendly_onchange days" value="{$days_value}" {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if} /> <span>{l s='days'}</span> <input id="{$input.name}_hours" name="{$input.name}_hours" class="friendly_onchange hours" value="{$hours_value}" {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if} /> <span>{l s='hours'}</span>
        </div>
    {elseif $input.type == 'cents_value'}
        <div class="cents_value">
            <input type="hidden" name="{$input.name}" id="{$input.name}" class="cents_value_to_save" value='{(int)$fields_value[$input.name]}' />
            {assign var='currency_value' value=round($fields_value[$input.name]/100, 2)}
            {if isset($input.prefix) || isset($input.suffix)}
            <div class="input-group{if isset($input.class)} {$input.class}{/if}">
            {/if}
                {if isset($input.prefix)}
                <span class="input-group-addon">
                  {$input.prefix}
                </span>
                {/if}
                
                <input type="text" id="{$input.name}_currency" name="{$input.name}_currency" class="currency_value" value="{$currency_value}" /> 
                
                {if isset($input.suffix)}
                <span class="input-group-addon">
                  {$input.suffix}
                </span>
                {/if}
            {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
            </div>
            {/if}
        </div>
    {else}
            {$smarty.block.parent}
    {/if}

{/block}
