( function ($) {
    $(document).on('click', '#wcbli_bo_btn', function(e) {
        var userConfirmed = confirm('Warning: Are you sure you want to block this order? The customer’s details will be added to the blacklist.');
        if (userConfirmed) {            
            var post_ID = $('#post_ID').val();
            $.ajax({
                url: adminajax.ajaxurl,
                type : 'POST',  
                data: {
                    'action':'wcblu_block_order_details_update_blacklist',
                    'order_id':post_ID,
                    'nonce':  adminajax.nonce,
                },
                success:function(result) {
                    console.log(result);
                    if( 'Blocked' === result ){
                        location.reload();
                    }
                    if( 'not_blocked' === result ){
                        alert('Order details not available to block');
                    }
                },
                error: function(errorThrown){
                    console.log(errorThrown);
                }
            });
        } else {
            e.preventDefault();
        }
    });
} )(jQuery);