(function($) {
    "use strict";

    $(document).ready(function() {
        $fcp_body = $('.fcp_body');

        if($fcp_body.width() < 750) {
            $fcp_body.addClass('mobile');
        }

        $(window).resize(function() {
            if($fcp_body.width() < 750) {
                $fcp_body.addClass('mobile');
            } else {
                $fcp_body.removeClass('mobile');
            }
        });
    });
})(jQuery);