var base_url = null

var onmessage = function(oEvent) {
	console.log('elicotteri')
	var msg_type = oEvent.data.type;
	if(msg_type == 'base_url') {
		base_url = oEvent.data.base_url
		postMessage("Imposto base url nel worker " + base_url)
	}

	console.log(document.getElementById('list-elicotteri-in-volo'))
	if(document.getElementById('list-elicotteri-in-volo')) {
		console.log('eli ok')
		var xhr = new XMLHttpRequest();
			xhr.open('GET', base_url + '/evento/elicotteri-in-volo-html', true);
			xhr.send();

		xhr.addEventListener("load", function(evt){
			console.log('evt load', evt)
		}, false);
	}
};


