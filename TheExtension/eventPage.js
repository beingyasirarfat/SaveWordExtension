chrome.runtime.onInstalled.addListener(function () {
	chrome.contextMenus.create({
		id: "Vocabulary",
		title: "Save Word",
		contexts: ["selection"]
	});
});

chrome.contextMenus.onClicked.addListener(function (clickData) {

	if (clickData.menuItemId == "Vocabulary" && clickData.selectionText) {
		chrome.storage.sync.get({ 'storage': "" }, function (data) {
			if (data.storage == 'database') {
				chrome.storage.sync.get('db', function (data) {
					var word = "Word=" + clickData.selectionText;
					var xhttp = new XMLHttpRequest();
					xhttp.open("POST", data.db, true);
					xhttp.onreadystatechange = function () {
						if (this.readyState == 4 && this.status == 200) {
							//if(this.responseText == 'success')
							chrome.storage.sync.get({ 'sound': 'on' }, function (data) {
								if (data.sound == 'on') {
									chrome.tts.speak(
										'<?xml version="1.0"?>' +
										"<speak>" +
										"  Saved! <emphasis> " +
										clickData.selectionText +
										" </emphasis> " +
										"</speak>"
									);
								}
							});
						}
					};
					xhttp.setRequestHeader(
						"Content-type",
						"application/x-www-form-urlencoded"
					);
					xhttp.send(word);
				});
			} else {
				chrome.storage.sync.get({ Words: [] }, function (data) {
					let obj = data.Words;
					let time = new Date().toISOString().slice(0, 10) + " " + new Date().toISOString().slice(11, 19);
					obj.push({ Serial: obj.length + 1, Word: clickData.selectionText, Definition: '', Translation: '', SaveTime: time });
					chrome.storage.sync.set({
						'Words': obj
					}, function () {
						chrome.storage.sync.get({ 'sound': 'on' }, function (data) {
							if (data.sound == 'on') {
								chrome.tts.speak(
									'<?xml version="1.0"?>' +
									"<speak>" +
									"  Saved! <emphasis> " +
									clickData.selectionText +
									" </emphasis> " +
									"</speak>"
								);
							}
						});
					});
				});
			}
		});
	}
});