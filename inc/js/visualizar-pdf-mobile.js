function checkMobileUpdateURL(){

	var userAgent = navigator.userAgent.toLowerCase();
	var devices = new Array('nokia','blackberry','sony','lg',
		'htc_tattoo','samsung','symbian','SymbianOS','elaine','palm',
		'series60','windows ce','android','obigo','netfront',
		'openwave','mobilexplorer','operamini');
	var url_prefix_direct_link = 'http://docs.google.com/gview?embedded=true&url=';


	if (mobiDetect(userAgent, devices)) {
		visualizar_pdf = document.getElementById("visualizar-pdf");
		visualizar_pdf.href = url_prefix_direct_link + visualizar_pdf.href; 
	}

}

function mobiDetect(userAgent, devices) {
	for(var i = 0; i < devices.length; i++) {
		if (userAgent.search(devices[i]) > 0) {
			return true;
		}
	}
	return false;
}


