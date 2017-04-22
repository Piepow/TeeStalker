var userData = (function (public, browserStorage, ajaxStalkees, nav) {
	public.initializeAll = function() {
		this.initializeStalkees();
		this.initializeCheckbox("loadServerListState", "loadServerList", ajaxStalkees.setShowServerList, _serverListCheckboxOnclick);
		this.initializeCheckbox("muteNotificationsState", "muteNotifications", notify.setMute, _muteNotificationsOnclick);
		this.initializeCheckbox("showNotificationsOnOfflineState", "showNotificationsOnOffline", notify.setMuteOnOffline, _showNotificationsOnOfflineOnclick);
		this.initializeRadioButton("refreshDelay", 60000, "refreshDelayButtonWrap", ajaxStalkees.setRefreshDelay, _refreshDelayOnclick);
		this.initializeRadioButton("requestsFailedTolerance", 0.1, "requestsFailedToleranceButtonWrap", ajaxStalkees.setRequestsFailedTolerance, _setRequestsFailedToleranceOnclick);
		this.initializeRadioButton("totalServerThreshold", 800, "totalServerThresholdButtonWrap", ajaxStalkees.setTotalServerThreshold, _setTotalServerThresholdOnclick);
	};

	public.initializeCheckbox = function (browserStorageName, checkboxID, initializeFunction, onclickCFunction) {
		var checkboxStorageState = browserStorage.load(browserStorageName, true, false);
		// Maybe not needed
		checkboxStorageState = (checkboxStorageState === null) ? false : checkboxStorageState;
		var checkboxElement = document.getElementById(checkboxID);
		checkboxElement.checked = checkboxStorageState;
		initializeFunction(checkboxStorageState);
		checkboxElement.onclick = onclickCFunction();
	};
	public.initializeRadioButton = function (browserStorageName, browserStorageDefaultValue, radioButtonWrapID, initializeFunction, onclickCFunction) {
		var radioButtonStorageState = browserStorage.load(browserStorageName, true, browserStorageDefaultValue);
		var radioButtonWrapElements = document.getElementById(radioButtonWrapID).children;
	    var radioButtonElements = [];
	    /* radioButtonWrapElements contains ALL the elements inside, we only want the input elements
		 * So we filter it out into radioButtonElements */
		for (var i = 0; i < radioButtonWrapElements.length; i++) {
			if (radioButtonWrapElements[i].tagName == "INPUT") {
				radioButtonElements.push(radioButtonWrapElements[i]);
			}
		}
		for (var j = 0; j < radioButtonElements.length; j++) {
			if (radioButtonElements[j].value == radioButtonStorageState) {
				radioButtonElements[j].checked = true;
			}
			radioButtonElements[j].onclick = onclickCFunction();
		}
		initializeFunction(radioButtonStorageState);
	};

	function _serverListCheckboxOnclick() {
		return function () {
			if (!this.checked) {
				var serverList = document.getElementById("serverList");
				serverList.innerHTML = "";
				serverList.style.display = "none";
				ajaxStalkees.setShowServerList(false);
			} else {
				ajaxStalkees.setShowServerList(true);
				ajaxStalkees.skipRequest();
			}
			browserStorage.save("serverListCheckboxState", this.checked);
		};
	}

	function _muteNotificationsOnclick() {
		return function () {
			if (this.checked) {
				notify.setMute(true);
			} else {
				notify.setMute(false);
			}
			browserStorage.save("muteNotificationsState", this.checked);
		};
	}

	function _showNotificationsOnOfflineOnclick() {
		return function () {
			if (this.checked) {
				notify.setMuteOnOffline(true);
			} else {
				notify.setMute(false);
			}
			browserStorage.save("showNotificationsOnOfflineState", this.checked);
		};
	}

	function _refreshDelayOnclick() {
		return function () {
			// setRequestDelay() wants milliseconds
			ajaxStalkees.setRefreshDelay(this.value);
			browserStorage.save("refreshDelay", this.value);
		};
	}
	function _setRequestsFailedToleranceOnclick() {
		return function () {
			ajaxStalkees.setRequestsFailedTolerance(this.value);
			browserStorage.save("requestsFailedTolerance", this.value);
		};
	}
	function _setTotalServerThresholdOnclick() {
		return function () {
			ajaxStalkees.setTotalServerThreshold(this.value);
			browserStorage.save("totalServerThreshold", this.value);
		};
	}

	public.initializeStalkees = function () {
		// [stalkeeID, "stalkeeName", "stalkeeClan", ignoreClan]
		var stalkees = browserStorage.load("stalkees", true, []);
		var stalkeeListWrapper = document.getElementById("stalkeeList");
		var stalkeeStatusWrapper = document.getElementById("stalkeeStatus");
		if (stalkees.length === 0) {
			var teeExclaim = document.createElement("IMG");
			teeExclaim.setAttribute("src", "includes/teeEx.png");
			teeExclaim.setAttribute("alt", "Tee");
			teeExclaim.classList.add("teeArt");
			stalkeeStatusWrapper.appendChild(teeExclaim);

			var noStalkees = document.createElement("P");
			var noStalkeesText = document.createTextNode("Nobody to stalk");
			noStalkees.appendChild(noStalkeesText);
			noStalkees.classList.add("title");
			stalkeeStatusWrapper.appendChild(noStalkees);

			var addThem = document.createElement("P");
			var addThemText = document.createTextNode("Add some in the 'Settings' tab");
			addThem.appendChild(addThemText);
			addThem.classList.add("sub");
			stalkeeStatusWrapper.appendChild(addThem);

			stalkeeStatusWrapper.style.display = "block";
			document.getElementById("dynamicLoading").style.display = "none";

			var noStalkees2 = document.createElement("P");
			var noStalkees2Text = document.createTextNode("Nobody to stalk");
			noStalkees2.appendChild(noStalkees2Text);
			noStalkees2.classList.add("title");
			stalkeeListWrapper.appendChild(noStalkees2);

			nav.unhideNewInSection();
			ajaxStalkees.dontStart();
		} else if (stalkees.length > 0) {
			var stalkeeListTable = document.createElement("TABLE");
			stalkeeListTable.classList.add("stalkeeListTable");

			var stalkeeListHeadRow = document.createElement("TR");
			var headRow1 = document.createElement("TH");
			var headRow2 = document.createElement("TH");
			var headRow3 = document.createElement("TH");
			var headRow1Text = document.createTextNode("Player");
			var headRow2Text = document.createTextNode("Clan");
			var headRow3Text = document.createTextNode("Actions");
			headRow1.appendChild(headRow1Text);
			headRow2.appendChild(headRow2Text);
			headRow3.appendChild(headRow3Text);
			stalkeeListHeadRow.appendChild(headRow1);
			stalkeeListHeadRow.appendChild(headRow2);
			stalkeeListHeadRow.appendChild(headRow3);
			stalkeeListTable.appendChild(stalkeeListHeadRow);

			for (var i = 0; i < stalkees.length; i++) {
				var stalkeeListRow = document.createElement("TR");
				var tableCell1 = document.createElement("TD");	// Player
				var tableCell2 = document.createElement("TD");	// Clan
				var tableCell3 = document.createElement("TD");	// Actions

				// Player
				var tableCell1Text = document.createTextNode(stalkees[i][1]);
				tableCell1.appendChild(tableCell1Text);
				stalkeeListRow.appendChild(tableCell1);

				// Clan
				if (stalkees[i][3] === false) {
					if (stalkees[i][2] === "") {
						var tableCell2EmptySpan = document.createElement("SPAN");
						tableCell2EmptySpan.classList.add("nullClan");
						var tableCell2SpanEmptyText = document.createTextNode("Empty");
						tableCell2EmptySpan.appendChild(tableCell2SpanEmptyText);
						tableCell2.appendChild(tableCell2EmptySpan);
					} else {
						var tableCell2Text = document.createTextNode(stalkees[i][2]);
						tableCell2.appendChild(tableCell2Text);
					}
				} else {
					var tableCell2IgnoredSpan = document.createElement("SPAN");
					tableCell2IgnoredSpan.classList.add("nullClan");
					var tableCell2IgnoredSpanText = document.createTextNode("Ignored");
					tableCell2IgnoredSpan.appendChild(tableCell2IgnoredSpanText);
					tableCell2.appendChild(tableCell2IgnoredSpan);
				}

				// Actions
				tableCell3.classList.add("oneLine");
				// Edit button
				var editStalkeeElement = document.createElement("DIV");
				editStalkeeElement.classList.add("button");
				editStalkeeElement.classList.add("modalButton");
				editStalkeeElement.onclick = _returnEditStalkeeFunction(stalkees[i][0], stalkees[i][1], stalkees[i][2], stalkees[i][3]);
				var editStalkeeText = document.createTextNode("Edit");
				editStalkeeElement.appendChild(editStalkeeText);
				tableCell3.appendChild(editStalkeeElement);
				// Delete button
				deleteStalkeeElement = document.createElement("DIV");
				deleteStalkeeElement.classList.add("button");
				deleteStalkeeElement.classList.add("last");
				deleteStalkeeElement.onclick = _returnDeleteStalkeeFunction(stalkees[i][0]);
				var deleteStalkeeImg = document.createElement("IMG");
				deleteStalkeeImg.classList.add("delete");
				deleteStalkeeImg.src = "includes/x24w.png";
				deleteStalkeeImg.alt = "&times;";
				deleteStalkeeElement.appendChild(deleteStalkeeImg);
				tableCell3.appendChild(deleteStalkeeElement);

				// Append tableCells
				stalkeeListRow.appendChild(tableCell1);
				stalkeeListRow.appendChild(tableCell2);
				stalkeeListRow.appendChild(tableCell3);
				stalkeeListTable.appendChild(stalkeeListRow);
			}
			stalkeeListWrapper.appendChild(stalkeeListTable);
		}
		var addStalkeeElement = document.createElement("DIV");
		var addStalkeeTextNode = document.createTextNode("Add Stalkee");
		addStalkeeElement.appendChild(addStalkeeTextNode);
		addStalkeeElement.classList.add("button");
		addStalkeeElement.onclick = function() {modalFunctions.displayAddStalkee();};
		stalkeeListWrapper.appendChild(addStalkeeElement);
		ajaxStalkees.setStalkeeList(stalkees);
	};

	// Don't create functions inside loops
	function _returnEditStalkeeFunction(stalkeeID, stalkeeName, stalkeeClan, ignoreClan) {
		return function() {
			modalFunctions.displayEditStalkee(stalkeeID, stalkeeName, stalkeeClan, ignoreClan);
		};
	}

	// Don't create functions inside loops
	function _returnDeleteStalkeeFunction(stalkeeID) {
		return function() {
			modalFunctions.displayDeleteStalkee(stalkeeID);
		};
	}
	public.loadDefaults = function () {
		browserStorage.remove("loadServerListState");
		browserStorage.remove("muteNotificationsState");
		browserStorage.remove("showNotificationsOnOfflineState");
		browserStorage.remove("refreshDelay");
		browserStorage.remove("requestsFailedTolerance");
		browserStorage.remove("totalServerThreshold");
		window.location.reload(false);
	};
	public.clearAll = function () {
		this.loadDefaults();
		browserStorage.remove("stalkees");
		window.location.reload(false);
	};

	return public;
})(userData || {}, browserStorage, ajaxStalkees, nav);