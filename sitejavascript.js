// external JavaScript document (sitejavascript.js)

function redirect(target) {
	window.location.href = target;
}

function popup(message, target) {
	let text = message;
	alert(text);
	redirect(target);
}