var hiddenText = (function (public, Velocity) {
	function _returnOnclickFunction(toggleArrow, hiddenContent) {
		return function () {
			if (this.classList.contains("show")) {
				this.classList.add("hide");
				this.classList.remove("show");
				Velocity(hiddenContent, {height: 0}, {duration: 500, easing: "easeOutCubic"});
				Velocity(toggleArrow, {rotateZ: "0deg"}, {duration: 250, easing: "easeOutCubic"});
			} else {
				this.classList.add("show");
				this.classList.remove("hide");
				Velocity(hiddenContent, {height: hiddenContent.scrollHeight}, {duration: 500, easing: "easeOutCubic"});
				Velocity(toggleArrow, {rotateZ: "90deg"}, {duration: 250, easing: "easeOutCubic"});
			}
		};

	}
	public.initialize = function () {
		var hiddenTextWraps = document.getElementsByClassName("hiddenTextWrap");
		for (var i = 0; i < hiddenTextWraps.length; i++) {
			var wrapHeader = hiddenTextWraps[i].getElementsByClassName("header")[0];
			var hiddenContent = hiddenTextWraps[i].getElementsByClassName("hidden")[0];

			var toggleArrowWrap = document.createElement("DIV");
			toggleArrowWrap.classList.add("toggleArrowWrap");

			var toggleArrow = document.createElement("IMG");
			toggleArrow.setAttribute("src", "includes/toggleArrow.png");
			toggleArrow.classList.add("toggleArrow");
			toggleArrowWrap.appendChild(toggleArrow);

			wrapHeader.onclick = _returnOnclickFunction(toggleArrow, hiddenContent);
			hiddenTextWraps[i].insertBefore(toggleArrowWrap, hiddenTextWraps[i].childNodes[0]);
		}
	};
	return public;
})(hiddenText || {}, Velocity);