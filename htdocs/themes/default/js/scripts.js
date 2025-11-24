

'use strict';

// Cookie Consent Banner
function setCookie(name, value, days) {
	var expires = '';
	if (days) {
		var date = new Date();
		date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
		expires = '; expires=' + date.toUTCString();
	}
	document.cookie = name + '=' + (value || '') + expires + '; path=/';
}

function getCookie(name) {
	var nameEQ = name + '=';
	var ca = document.cookie.split(';');
	for (var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) === ' ') c = c.substring(1, c.length);
		if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
	}
	return null;
}

function showCookieBanner() {
	// Remove any existing banner before creating a new one
	var oldBanner = document.getElementById('cookie-consent-banner');
	if (oldBanner) oldBanner.remove();
	if (getCookie('cookie_consent') !== '1') {
		var banner = document.createElement('div');
		banner.id = 'cookie-consent-banner';
		banner.style.position = 'fixed';
		banner.style.bottom = '0';
		banner.style.left = '0';
		banner.style.width = '100%';
		banner.style.background = '#222';
		banner.style.color = '#fff';
		banner.style.padding = '16px';
		banner.style.textAlign = 'center';
		banner.style.zIndex = '9999';
		banner.innerHTML = 'This website uses cookies to ensure you get the best experience. <button id="cookie-consent-accept" style="margin-left:16px;" class="btn btn-primary btn-sm">Accept</button>';
		document.body.appendChild(banner);
		banner.querySelector('#cookie-consent-accept').addEventListener('click', function () {
			setCookie('cookie_consent', '1', 365);
			banner.style.transition = 'transform 0.5s ease, opacity 0.5s ease';
			banner.style.transform = 'translateY(100%)';
			banner.style.opacity = '0';
			setTimeout(function () {
				banner.remove();
			}, 500);
		});
	}
}

window.addEventListener('DOMContentLoaded', showCookieBanner);