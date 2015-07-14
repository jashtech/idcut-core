$(document).ready( function() {
    $('.friendly_seconds').each(function(){
        var container = $(this);
        container.children('input.friendly_onchange').change(function($e){
            var to_update = 0;
            container.children('input.friendly_onchange').each(function(){
                if(isUint32($(this).val())){
                    if($(this).hasClass('days')){
                        to_update += parseInt($(this).val() * 24*3600); 
                    }else{
                        to_update += parseInt($(this).val() * 3600); 
                    }
                }
            });
            container.find('input.friendly_real_value').val(to_update);
        });
    });
    $('.cents_value').each(function(){
        var container = $(this);
        container.find('input.currency_value').change(function($e){
            container.find('input.cents_value_to_save').val(parseInt(parseFloat($(this).val().replace(",", "."))*100));
        });
    });
});

function isUint32(n) {
    var Pn = parseInt(n,10);
    return +n === Pn && !(Pn % 1) && Pn < 0x100000000 && Pn >= 1;
}