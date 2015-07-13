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
    

});

function isUint32(n) {
    var Pn = parseInt(n,10);
    return +n === Pn && !(Pn % 1) && Pn < 0x100000000 && Pn >= 1;
}