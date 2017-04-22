var modalFunctions = (function (public, browserStorage) {
	public.displayEditStalkee = function (stalkeeID, stalkeeName, stalkeeClan, ignoreClan) {
		document.getElementById("modalHeaderText").innerHTML = "Edit Stalkee";
		_getElementInsideContainer("editAddForm", "stalkeeName").value = stalkeeName;
		_getElementInsideContainer("editAddForm", "stalkeeClan").value = stalkeeClan;
		_getElementInsideContainer("editAddForm", "stalkeeID").value = stalkeeID;
		_getElementInsideContainer("editAddForm", "submitButton").value = "Edit Stalkee";
		if (ignoreClan === true) {
			_getElementInsideContainer("editAddForm", "ignoreClan").checked = true;
		} else if (ignoreClan === false) {
			_getElementInsideContainer("editAddForm", "ignoreClan").checked = false;
		} else {
			console.log("Unknown ignoreClan value");
		}
		var modal = document.getElementById("mainModal");
		modal.style.display = "block";
		var editAddForm = document.getElementById("editAddForm");
		_getElementInsideContainer("editAddForm", "submitButton").onclick = function() {return _editStalkee();};
		editAddForm.style.display = "block";
	};

	public.displayAddStalkee = function () {
		document.getElementById("modalHeaderText").innerHTML = "Add Stalkee";
		_getElementInsideContainer("editAddForm", "submitButton").value = "Add Stalkee";
		var modal = document.getElementById("mainModal");
		modal.style.display = "block";
		var editAddForm = document.getElementById("editAddForm");
		_resetEditAddForm();
		_getElementInsideContainer("editAddForm", "submitButton").onclick = function() {return _addStalkee();};
		editAddForm.style.display = "block";
	};

	public.displayDeleteStalkee = function (stalkeeID) {
		document.getElementById("modalHeaderText").innerHTML = "Delete Stalkee";
		var modal = document.getElementById("mainModal");
		modal.style.display = "block";
		var deleteForm = document.getElementById("deleteForm");
		_getElementInsideContainer("deleteForm", "submitButton").onclick = function() {return _deleteStalkee(stalkeeID);};
		deleteForm.style.display = "block";
	};

	public.displayClearAll = function () {
		document.getElementById("modalHeaderText").innerHTML = "Clear All Settings";
		var modal = document.getElementById("mainModal");
		modal.style.display = "block";
		document.getElementById("clearAllForm").style.display = "block";
	};

	function _editStalkee() {
		var stalkeeIDValue = _getElementInsideContainer("editAddForm", "stalkeeID").value;
		var stalkeeNameValue = _getElementInsideContainer("editAddForm", "stalkeeName").value;
		var stalkeeClanValue = _getElementInsideContainer("editAddForm", "stalkeeClan").value;
		var ignoreClanValue = _getElementInsideContainer("editAddForm", "ignoreClan").checked;
		// createIfNew is false because that was already done in fetchStalkees() in start.js
		var stalkees = browserStorage.load("stalkees", false);
		for (var i = 0; i < stalkees.length; i++) {
			for (var j = 0; j < stalkees[i].length; j++) {
				// stalkees[i] => [stalkeeID, stalkeeName, stalkeeClan, ignoreClan]
				if (stalkees[i][0] == stalkeeIDValue) {
					stalkees[i][1] = stalkeeNameValue;
					stalkees[i][2] = stalkeeClanValue;
					stalkees[i][3] = ignoreClanValue;
					break;
				}
			}
		}
		browserStorage.save("stalkees", stalkees);
		window.location.reload(false);
	}

	function _addStalkee() {
		var stalkeeNameValue = _getElementInsideContainer("editAddForm", "stalkeeName").value;
		var stalkeeClanValue = _getElementInsideContainer("editAddForm", "stalkeeClan").value;
		var ignoreClanValue = _getElementInsideContainer("editAddForm", "ignoreClan").checked;
		// createIfNew is false because that was already done in fetchStalkees() in start.js
		var stalkees = browserStorage.load("stalkees", false);
		// Get New ID by getting one above the highest
		var newStalkeeID = 1;	// 1 is the minimum ID value 
		for (var i = 0; i < stalkees.length; i++) {
			for (var j = 0; j < stalkees[i].length; j++) {
				// stalkees[i] => [stalkeeID, stalkeeName, stalkeeClan, ignoreClan]
				if (stalkees[i][0] > newStalkeeID) {
					newStalkeeID = stalkees[i][0];
				}
			}
			newStalkeeID++;
		}
		var stalkeeValueArray = [newStalkeeID, stalkeeNameValue, stalkeeClanValue, ignoreClanValue];
		stalkees.push(stalkeeValueArray);
		browserStorage.save("stalkees", stalkees);
		window.location.reload(false);
	}

	function _deleteStalkee(stalkeeID) {
		var stalkees = browserStorage.load("stalkees", false);
		for (var i = 0; i < stalkees.length; i++) {
			for (var j = 0; j < stalkees[i].length; j++) {
				// stalkees[i] => [stalkeeID, stalkeeName, stalkeeClan, ignoreClan]
				if (stalkees[i][0] == stalkeeID) {
					stalkees.splice(i, 1);
					break;
				}
			}
		}
		browserStorage.save("stalkees", stalkees);
		window.location.reload(false);
	}

	function _capitalizeFirstLetter(string) {
	    return string.charAt(0).toUpperCase() + string.slice(1);
	}

	public.closeModal = function () {
		var modal = document.getElementById("mainModal");
		modal.classList.add("modal-out");
		setTimeout(function(){
			var editAddForm = document.getElementById("editAddForm");
			var deleteForm = document.getElementById("deleteForm");
			var clearAllForm = document.getElementById("clearAllForm");

			deleteForm.style.display = "none";
			editAddForm.style.display = "none";
			clearAllForm.style.display = "none";
			modal.style.display = "none";
			_resetEditAddForm();
			modal.classList.remove("modal-out");
		}, 300);
		// Needs to be same amount of time as modal-out transition
	};

	function _getElementInsideContainer(containerID, childID) {
	    var elm = {};
	    var elms = document.getElementById(containerID).getElementsByTagName("*");
	    for (var i = 0; i < elms.length; i++) {
	        if (elms[i].id === childID) {
	            elm = elms[i];
	            break;
	        }
	    }
	    return elm;
	}

	function _resetEditAddForm() {
		_getElementInsideContainer("editAddForm", "stalkeeID").value = "";
		_getElementInsideContainer("editAddForm", "stalkeeName").value = "";
		_getElementInsideContainer("editAddForm", "stalkeeClan").value = "";
		_getElementInsideContainer("editAddForm", "ignoreClan").checked = false;
	}

	return public;
}(modalFunctions || {}, browserStorage));

document.addEventListener("DOMContentLoaded", function(event) { 
	var modal = document.getElementById("mainModal");
	window.onclick = function(event) {
	    if (event.target == modal) {
			modalFunctions.closeModal();
	    }
	};
});