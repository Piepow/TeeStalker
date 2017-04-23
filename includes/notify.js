/* For detail on modulation structure:
 * http://www.adequatelygood.com/JavaScript-Module-Pattern-In-Depth.html */
var notify = (function (public, arrayDifference) {
	// -1 so we know when its the first run
    var previousStalkeeStatus = -1;
	var notificationsMuted = false;
	var muteOnOffline = false;
	function _generateNotification(cFunction) {
		if (notificationsMuted === false) {
		    // Let's check if the browser supports notifications
		    if (!("Notification" in window)) {
		    	alert("This browser does not support desktop notification");
		    }

		    // Let's check whether notification permissions have already been granted
			else if (Notification.permission === "granted") {
				// If it's okay let's create a notification
				cFunction();
			}

		    // Otherwise, we need to ask the user for permission
		    else if (Notification.permission !== 'denied') {
		    	Notification.requestPermission(function (permission) {
			        // If the user accepts, let's create a notification
			        if (permission === "granted") {
			        	cFunction();
			        }
		    	});
		    }

		    // At last, if the user has denied notifications, and you 
		    // want to be respectful there is no need to bother them any more.
		}
	}
	function _notifyStalkeeStatus(stalkeeName, gametype, stalkeeAction) {
		var notification;
		if (stalkeeAction == "JOIN") {
			notification = new Notification("\"" + stalkeeName + "\"" + " joined " + gametype); 
			setTimeout(function() { notification.close(); }, 3000);
		} else if (stalkeeAction == "LEAVE") {
			if (!muteOnOffline) {
				notification = new Notification("\"" + stalkeeName + "\"" + " went offline"); 
				setTimeout(function() { notification.close(); }, 3000);
			}
		} else if (stalkeeAction == "ON") { 
		    // Ran only on the first time
		    notification = new Notification("\"" + stalkeeName + "\"" + " is on " + gametype); 
			setTimeout(function() { notification.close(); }, 3000);
		} else {
			console.warn("Invalid stalkeeAction sent to notification!");
		}
	}

	function _returnCallbackStalkeeStatus(stalkeeName, gametype, stalkeeAction) {
		return function() {
  			_notifyStalkeeStatus(stalkeeName, gametype, stalkeeAction);
  		};
	}

	function _notifyMovingStalkeeStatus(stalkeeName, gametypeFrom, gametypeTo) {
		var notification;
		if (gametypeFrom == gametypeTo) {
			notification = new Notification("\"" + stalkeeName + "\"" + " joined another " + gametypeTo + " server"); 
		} else {
			notification = new Notification("\"" + stalkeeName + "\"" + " left " + gametypeFrom + " and joined " + gametypeTo); 
		}
		setTimeout(function() { notification.close(); }, 3000);
	}

	function _returnCallbackMovingStalkeeStatus(stalkeeName, gametypeFrom, gametypeTo) {
		return function() {
			_notifyMovingStalkeeStatus(stalkeeName, gametypeFrom, gametypeTo);
		};
	}

	// If somebody goes to another server, both will be listed in diffStalkees - need to remove old one
	function _checkIfStalkeesMovedToAnother(diffStalkees, stalkeeArray) {
		for (var i = 0; i < diffStalkees.length; i++) {
			for (var j = 0; j < diffStalkees.length; j++) {
				// If the nicknames are the same
				if (i != j && diffStalkees[i][0] == diffStalkees[j][0]) {
					for (var k = 0; k < stalkeeArray.length; k++) {
						if (diffStalkees[i] == stalkeeArray[k]) {
							_generateNotification(_returnCallbackMovingStalkeeStatus(diffStalkees[i][0], diffStalkees[j][1], diffStalkees[i][1]));
						} else if (diffStalkees[j] == stalkeeArray[k]) {
							_generateNotification(_returnCallbackMovingStalkeeStatus(diffStalkees[i][0], diffStalkees[i][1], diffStalkees[j][1]));
						}
					}
					if (i < j) {
						diffStalkees.splice(j, 1);
						diffStalkees.splice(i, 1);
					} else {
						diffStalkees.splice(i, 1);
						diffStalkees.splice(j, 1);
					}
				}
			}
		}
		return diffStalkees;
	}

    public.stalkeeStatus = function (stalkeeArray) {
    	if (stalkeeArray.length > 0) {
        	// Returned an array of stalkees
    		if (previousStalkeeStatus == -1) {
            	// First time
            	// Notify all stalkees online
            	for (var i = 0; i < stalkeeArray.length; i++) {
              		_generateNotification(_returnCallbackStalkeeStatus(stalkeeArray[i][0], stalkeeArray[i][1], "ON"));
            	}
        	} else {
            	// Not the first time
            	var diffStalkees = arrayDifference.twoWay(previousStalkeeStatus, stalkeeArray);
            	// If somebody goes to another server, both will be listed in diffStalkees
            	// This is a special case that is handled by the function
            	// Duplicated stalkees are found and notified, and then they are spliced out
            	diffStalkees = _checkIfStalkeesMovedToAnother(diffStalkees, stalkeeArray);
            	for (var j = 0; j < diffStalkees.length; j++) {
            		for (var k = 0; k < stalkeeArray.length; k++) {
            			if (diffStalkees[j] == stalkeeArray[k]) {
	            			_generateNotification(_returnCallbackStalkeeStatus(diffStalkees[j][0], diffStalkees[j][1], "JOIN"));
            			}
            		}
            		for (var l = 0; l < previousStalkeeStatus.length; l++) {
            			if (diffStalkees[j] == previousStalkeeStatus[l]) {
	            			_generateNotification(_returnCallbackStalkeeStatus(diffStalkees[j][0], diffStalkees[j][1], "LEAVE"));
            			}
            		}
            	}
    	    }
    	} else {
			for (var m = 0; m < previousStalkeeStatus.length; m++) {
		  		_generateNotification(_returnCallbackStalkeeStatus(previousStalkeeStatus[m][0], previousStalkeeStatus[m][1], "LEAVE"));
			}
    	}
    	previousStalkeeStatus = stalkeeArray;
    };
    public.setMute = function (value) {
    	// value should be a boolean
    	notificationsMuted = value;
    };
    public.setMuteOnOffline = function (value) {
    	// value should be a boolean
    	muteOnOffline = value;
    };

    return public; // expose externally
}(notify || {}, arrayDifference));