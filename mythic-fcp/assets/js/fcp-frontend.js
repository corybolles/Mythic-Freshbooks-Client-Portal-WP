(function($) {
    "use strict";

    $(document).ready(function() {
        var fcp_body = $('.fcp_body');

        if(fcp_body.width() < 750) {
            fcp_body.addClass('collapsed');
        }

        $(window).resize(function() {
            if(fcp_body.width() < 750) {
                fcp_body.addClass('collapsed');
            }
			
			if(fcp_body.width() > 750) {
                fcp_body.removeClass('collapsed');
            }
        });
    });
})(jQuery);