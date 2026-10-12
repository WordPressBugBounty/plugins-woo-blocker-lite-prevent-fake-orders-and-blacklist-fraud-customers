( function ($) {
    var pendingAction = '';

    $(function () {
        var $modal = $('#wcblu_bo_modal');
        if ( $modal.length ) {
            $modal.appendTo('body');
        }
    });

    function wcbluGetSelectedFields() {
        var fields = [];
        $('#wcblu-bo-meta-box-id input[name="wcblu_bo_fields[]"]:checked').not(':disabled').each(function () {
            fields.push($(this).val());
        });
        return fields;
    }

    function wcbluCloseBlockModal() {
        $('#wcblu_bo_modal').hide();
        pendingAction = '';
    }

    function wcbluOpenBlockModal(action) {
        var $modal = $('#wcblu_bo_modal');
        var isUnblock = ( 'unblock' === action );
        pendingAction = action;
        $modal.toggleClass('is-unblock', isUnblock);
        $modal.find('.wcblu-bo-modal-title-block').toggle(!isUnblock);
        $modal.find('.wcblu-bo-modal-title-unblock').toggle(isUnblock);
        $modal.find('.wcblu-bo-modal-body-block').toggle(!isUnblock);
        $modal.find('.wcblu-bo-modal-body-unblock').toggle(isUnblock);
        $modal.find('.wcblu-bo-modal-confirm').text(isUnblock ? wblp_order_ajax.i18n.confirm_unblock : wblp_order_ajax.i18n.confirm_block);
        $modal.show();
    }

    function wcbluSubmitBlockAction() {
        var orderId = $('.wcblu-bo-widget').data('order-id') || $('#post_ID').val() || $('#order_id').val();
        $.ajax({
            url: wblp_order_ajax.ajaxurl,
            type: 'POST',
            data: {
                action: 'wcblu_block_order_details_update_blacklist',
                order_id: orderId,
                wcblu_action: pendingAction,
                fields: wcbluGetSelectedFields(),
                nonce: wblp_order_ajax.nonce
            },
            success: function (result) {
                if ( 'Blocked' === result || 'Unblocked' === result ) {
                    location.reload();
                    return;
                }
                wcbluCloseBlockModal();
                alert(wblp_order_ajax.i18n.not_blocked);
            },
            error: function () {
                wcbluCloseBlockModal();
                alert(wblp_order_ajax.i18n.not_blocked);
            }
        });
    }

    $(document).on('click', '#wcbli_bo_btn, #wcbli_ubo_btn', function (e) {
        e.preventDefault();
        if ( $(this).is(':disabled') || $('.wcblu-bo-widget').hasClass('wcblu-upgrade-pro-to-unlock') ) {
            return;
        }
        if ( ! wcbluGetSelectedFields().length ) {
            alert(wblp_order_ajax.i18n.select_field);
            return;
        }
        wcbluOpenBlockModal( $(this).is('#wcbli_ubo_btn') ? 'unblock' : 'block' );
    });

    $(document).on('click', '.wcblu-bo-modal-close, .wcblu-bo-modal-cancel', function (e) {
        e.preventDefault();
        wcbluCloseBlockModal();
    });

    $(document).on('click', '#wcblu_bo_modal', function (e) {
        if ( e.target === this ) {
            wcbluCloseBlockModal();
        }
    });

    $(document).on('click', '.wcblu-bo-modal-confirm', function (e) {
        e.preventDefault();
        wcbluSubmitBlockAction();
    });
} )(jQuery);
