var ajaxStalkees = (function (public, modalFunctions, browserStorage, nav) {
	var showServerList = false;
	var requestLoopDelay = 20000;
	var stalkeeList;
	var xhttp;
	var totalServerThreshold = 800;
	var requestsFailedRatioThreshold = 0.03;
	var dontStartFlag = false;

	function _ajax(url, cFunction, usePOST, POSTData) {
		// Default parameters in for pre-2015 JavaScript
		usePOST = typeof usePOST !== 'undefined' ? usePOST : false;
		xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				cFunction(this);
			}
		};
		if (usePOST) {
			xhttp.open("POST", url, true);
			xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhttp.send("stalkeelist=" + JSON.stringify(POSTData));
		} else {
			xhttp.open("GET", url, true);
			xhttp.send();
		}
	}

	function _displayStalkeeStatus(stalkeeArray) {
		var stalkeeStatusWrapper = document.getElementById("stalkeeStatus");
		stalkeeStatusWrapper.innerHTML = "";
		if (stalkeeArray.length === 0) {
			var teeZ = document.createElement("IMG");
			teeZ.setAttribute("src", "includes/teeZ.png");
			teeZ.setAttribute("alt", "Tee");
			teeZ.classList.add("teeArt");
			stalkeeStatusWrapper.appendChild(teeZ);

			var noStalkees = document.createElement("P");
			var noStalkeesText = document.createTextNode("No stalkees online");
			noStalkees.appendChild(noStalkeesText);
			noStalkees.classList.add("title");
			stalkeeStatusWrapper.appendChild(noStalkees);
		} else {
			var stalkeeStatusTable = document.createElement("TABLE");
			stalkeeStatusTable.classList.add("stalkeeStatusTable");

			var stalkeeStatusHeadRow = document.createElement("TR");
			var headRow1 = document.createElement("TH");
			var headRow2 = document.createElement("TH");
			var headRow3 = document.createElement("TH");
			var headRow1Text = document.createTextNode("Stalkee");
			var headRow2Text = document.createTextNode("Gametype");
			var headRow3Text = document.createTextNode("Server IP");
			headRow1.appendChild(headRow1Text);
			headRow2.appendChild(headRow2Text);
			headRow3.appendChild(headRow3Text);
			stalkeeStatusHeadRow.appendChild(headRow1);
			stalkeeStatusHeadRow.appendChild(headRow2);
			stalkeeStatusHeadRow.appendChild(headRow3);
			stalkeeStatusTable.appendChild(stalkeeStatusHeadRow);

			for (var i = 0; i < stalkeeArray.length; i++) {
				var stalkeeStatusRow = document.createElement("TR");
				for (var j = 0; j < stalkeeArray[i].length; j++) {
					var tableCell = document.createElement("TD");
					var tableCellTextNode = document.createTextNode(stalkeeArray[i][j]);
					tableCell.appendChild(tableCellTextNode);
					stalkeeStatusRow.appendChild(tableCell);
				}
				stalkeeStatusTable.appendChild(stalkeeStatusRow);
			}
			stalkeeStatusWrapper.appendChild(stalkeeStatusTable);
		}
	}

	function _htmlDecode(input){
		// var e = document.createElement('div');
		// e.innerHTML = input;
		// return e.childNodes[0].nodeValue;
		var doc = new DOMParser().parseFromString(input, "text/html");
		return doc.documentElement.textContent;
	}
	function _initializeStalkeeStatusLoadingAnimation() {
		var loading = document.getElementById("dynamicLoading");

		var teeLove = document.createElement("IMG");
		teeLove.setAttribute("src", "includes/teeL.png");
		teeLove.setAttribute("alt", "Tee");
		teeLove.classList.add("teeArt");
		loading.appendChild(teeLove);

		var loadingTitle = document.createElement("P");
		var loadingTitleText = document.createTextNode("Loading Stalkees...");
		loadingTitle.appendChild(loadingTitleText);
		loadingTitle.classList.add("title");
		loading.appendChild(loadingTitle);

		loading.classList.add("direct");
	}
	function _hideLoading() {
		nav.unhideNewInSection();
	}
	function _loopAgainDelay(delay) {
		var loading = document.getElementById("dynamicLoading");
		// There is now content in stalkeeStatus, so we give the loading bar peripheral styling.
		loading.classList.remove("direct");
		loading.innerHTML = "Loading...";
		loading.classList.add("peripheral");

		// loop back
		setTimeout(function (){
			ajaxStalkees.updateContent();
		}, delay);
	}
	public.start = function() {
		if (!dontStartFlag) {
			// Display notice of loading stalkees, with "direct" styling - first time only
			_initializeStalkeeStatusLoadingAnimation();
			this.updateContent();
		} else {
			_hideLoading();
		}
	};
	public.setShowServerList = function (value) {
		// value should be a boolean
		showServerList = value ? 1 : 0;
	};
	// Called again through setTimeout later, so we loop from here
	public.updateContent = function () {
		var loading = document.getElementById("dynamicLoading");
		loading.classList.add("direct");
		loading.style.display = "block";
		// ajax(url, cFunction, usePOST, POSTData);
		_ajax("includes/control.ajax.php?serverList=" + showServerList, ajaxStalkees.processServerStalkeeData, true, stalkeeList);
	};
	// Callback function
	public.processServerStalkeeData = function (xhttp) {
		var JSONData = JSON.parse(xhttp.responseText);

		var masterServerStatusBox = document.getElementById("masterServerStatus");

		masterServerStatusBox.innerHTML = "<span class='number'>" + JSONData.masterServerTotalCount + "</span>" + " teeworlds servers fetched.<br>";
		masterServerStatusBox.innerHTML += "<span class='number'>" + JSONData.requestsFailed + "</span>" + " of those requests failed.<br>";

		var continueCondition = true;

		var failedContinueConditionMessage = document.createElement("DIV");
		failedContinueConditionMessage.classList.add("failed");

		if (JSONData.masterServerTotalCount < totalServerThreshold) {
			var failedP1 = document.createElement("P");
			var failedText1 = document.createTextNode("Total server count threshold not reached.");
			failedP1.appendChild(failedText1);
			failedContinueConditionMessage.appendChild(failedP1);
			continueCondition = false;
		}
		if (JSONData.requestsFailed / JSONData.masterServerTotalCount > requestsFailedRatioThreshold) {
			var failedP2 = document.createElement("P");
			var failedText2 = document.createTextNode("Too many failed server requests.");
			failedP2.appendChild(failedText2);
			failedContinueConditionMessage.appendChild(failedP2);
			continueCondition = false;
		}
		var loading = document.getElementById("dynamicLoading");
		if (continueCondition) {
			loading.style.display = "none";
			/* stalkeeStatus => [
				["stalkeeName", "gametype", "serverIP:port"]
			] */
			_displayStalkeeStatus(JSONData.stalkeeStatus);
			if (showServerList) {
				document.getElementById("serverList").innerHTML = JSONData.serverList === undefined ? "" : _htmlDecode(JSONData.serverList) + "<cite>End.</cite>";
			}

			nav.unhideNewInSection();

			notify.stalkeeStatus(JSONData.stalkeeStatus);

			_loopAgainDelay(requestLoopDelay);
		} else {
			loading.style.display = "none";

			var retryP = document.createElement("P");
			var retryText = document.createTextNode("Retrying in 60 seconds...");
			retryP.appendChild(retryText);
			failedContinueConditionMessage.appendChild(retryP);

			// If the stalkeeStatusTable is not already present, display a message
			var stalkeeStatusWrapper = document.getElementById("stalkeeStatus");
			var stalkeeStatusTableNotPresent = false;
			if (stalkeeStatus.length > 0) {
				for (var i = 0; i < stalkeeStatusWrapper.length; i++) {
					if (stalkeeStatusWrapper[i].id == "stalkeeStatusTable") {
						stalkeeStatusTableNotPresent = true;
					}
				}
			} else {
				stalkeeStatusTableNotPresent = true;
			}

			if (stalkeeStatusTableNotPresent) {
				stalkeeStatusWrapper.innerHTML = "";
				var teeW = document.createElement("IMG");
				teeW.setAttribute("src", "includes/teeW.png");
				teeW.setAttribute("alt", "Tee");
				teeW.classList.add("teeArt");
				stalkeeStatusWrapper.appendChild(teeW);

				var serverIssuesP = document.createElement("P");
				var serverIssuesText = document.createTextNode("Issues Requesting Teeworlds Servers");
				serverIssuesP.appendChild(serverIssuesText);
				serverIssuesP.classList.add("title");
				stalkeeStatusWrapper.appendChild(serverIssuesP);
			}

			masterServerStatusBox.appendChild(failedContinueConditionMessage);
			nav.unhideNewInSection();

			_loopAgainDelay(60000);
		}
	};
	public.skipRequest = function () {
		xhttp.abort();
		this.updateContent();
	};
	public.setRefreshDelay = function (value) {
		requestLoopDelay = value;
	};
	public.setStalkeeList = function (value) {
		stalkeeList = value;
	};
	public.setRequestsFailedTolerance = function (value) {
		// 0.1 = Low
		// 0.05 = Medium
		// 0.025 = High
		requestsFailedRatioThreshold = value;
	};
	public.setTotalServerThreshold = function (value) {
		totalServerThreshold = value;
	};
	public.dontStart = function () {
		dontStartFlag = true;
	};
	return public;
}(ajaxStalkees || {}, modalFunctions, browserStorage, nav));