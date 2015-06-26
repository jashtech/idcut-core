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
                        if($(this).hasClass('unvalid')){
                            $(this).removeClass('unvalid');
                        }
                    }else if(!($(this).hasClass('unvalid'))){
                        $(this).addClass('unvalid');
                    }
                });
                $('input#new_discount_size').change(function($e){
                    var $mpn = $(this).val();
                    if(isUint32($mpn)){
                        if($(this).hasClass('unvalid')){
                            $(this).removeClass('unvalid');
                        }
                    }else if(!($(this).hasClass('unvalid'))){
                        $(this).addClass('unvalid');
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
                        }
                        return false;
                });
            });
            function isUint32(n) {
                var Pn = parseInt(n,10);
                return +n === Pn && !(Pn % 1) && Pn < 0x100000000 && Pn >= 1;
            }
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
                <div class="col-lg-3">{l s='Minimum users:'} </div>
                <div class="col-lg-2"><input id="new_min_participants_number" name="new_min_participants_number" value="" /></div>
                <div class="col-lg-3">{l s='Return value:'} </div>
                <div class="col-lg-2"><input id="new_discount_size" name="new_discount_size" value="" /></div>
                <div class="col-lg-2"><a id="ranges_add" class="list-toolbar-btn" href="#ranges_add" title="{l s='Add'}"><i class="process-icon-new"></i></a></div>
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
    {else}
            {$smarty.block.parent}
    {/if}

{/block}
