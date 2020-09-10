var js_fcp_body = document.getElementsByClassName('fcp_body')[0];

if(js_fcp_body) {
	if(js_fcp_body.offsetWidth < 750) {
		js_fcp_body.classList.add("collapsed");
	}
}

window.onresize = function(e) {
	if(js_fcp_body) {
		if(js_fcp_body.offsetWidth < 750) {
			js_fcp_body.classList.add("collapsed");
		} else {
			js_fcp_body.classList.remove("collapsed");
		}
	}
};