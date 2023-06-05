var base_url = null

var onmessage = function(oEvent) {
	var msg_type = oEvent.data.type;
	if(msg_type == 'base_url') {
		base_url = oEvent.data.base_url
		postMessage("Imposto base url nel worker " + base_url)
	}
};



var i = setInterval( function() {
	if(base_url) {
		var xhr = new XMLHttpRequest();
		xhr.open('GET', base_url + '/cap/parse', true);
		xhr.send()

		postMessage('called ' +base_url + '/cap/parse' )
	}
}, 20000);