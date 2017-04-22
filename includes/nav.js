var nav = (function (public) {

	var infoBoxes = [];
	var currentSection;
	var navLinks = [];

	function _styleActive() {
		/* <navList>
		 * 	<navWrap>
		 * 		<li><a></a></li>
		 * 		<li><a></a></li>
		 * 		<li><a></a></li>
		 *		...
		 * 	</navWrap>
		 * </navList>
		 */
		for (var i = 0; i < navLinks.length; i++) {
			if (navLinks[i].getAttribute("data-section") == currentSection) {
				navLinks[i].classList.add("active");
			} else {
				navLinks[i].classList.remove("active");
			}
		}
	}

	function _getInfoBoxes() {
		var mainWrapChildren = document.getElementById("mainWrap").children;
		for (var i = 0; i < mainWrapChildren.length; i++) {
			if (mainWrapChildren[i].classList.contains("infoBox")) {
				infoBoxes.push(mainWrapChildren[i]);
			}
		}
	}

	function _showIfNotEmpty(infoBox) {
		if (infoBox.innerHTML !== "") {
			infoBox.style.display = "block";
			infoBox.classList.add("fade-in");
		} else {
			infoBox.style.display = "none";
		}
	}

	// Show all the infoBoxes in the IDArray and hide all the other infoBoxes
	function _show(IDArray) {
		for (var i = 0; i < infoBoxes.length; i++) {
			for (var j = 0; j < IDArray.length; j++) {
				if (infoBoxes[i].id == IDArray[j]) {
					_showIfNotEmpty(infoBoxes[i]);
					break;
				} else {
					infoBoxes[i].style.display = "none";
				}
			}
		}
	}

	function _navAction() {
		if (currentSection == 'home') {
			// Show these and hide all others
			_show(['loadingWrap', 'masterServerStatus', 'serverList']);
		} else if (currentSection == 'settings') {
			_show(['stalkeeList', 'settings1', 'settings2', 'settings3', 'settings4', 'settings5', 'settings6']);
		} else if (currentSection == 'about') {
			_show(['stalkerAbout']);
		}
	}

	function _returnOnclickFunction() {
		return function () {
			var linkSection = this.getAttribute("data-section");
			nav.display(linkSection);
		};
	}

	function _getNavLinks() {
		var navListLinks = document.getElementById("navWrap").children;
		for (var i = 0; i < navListLinks.length; i++) {
			navLinks.push(navListLinks[i].children[0]);
			navLinks[i].classList.remove("active");
			navLinks[i].onclick = _returnOnclickFunction();
		}
	}

	public.unhideNewInSection = function () {
		_navAction();
	};

	// Called from onclick
	public.display = function (view) {
		currentSection = view;
		_styleActive();
		_navAction();
	};

	public.initialize = function() {
		_getInfoBoxes();
		_getNavLinks();
		this.display('home');
	};

	return public;

}(nav || {}));