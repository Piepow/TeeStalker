/* For detail on modulation structure:
 * http://www.adequatelygood.com/JavaScript-Module-Pattern-In-Depth.html */
var browserStorage = (function (public) {
	// All things in browser storage are prefixed by this
    var stalkerFlag = "stalkerStorage-";
    
    /* Currently there are these names:
     * 	-> stalkees
     *	-> loadServerListState
     *	-> muteNotificationsState
     *	-> showNotificationsOnOfflineState
     *	-> refreshDelay
     *  -> requestsFailedTolerance
     *  -> totalServerThreshold
     */
    // Check if the new stalkee has the same name as an existing one
    function _checkIfDuplicateStalkees(newStalkees) {
        /* stalkees currently contains the old array of all the stalkees
         * newStalkees contains the new array of all the stalkees */
        for (var i = 0; i < newStalkees.length; i++) {
            for (var j = 0; j < newStalkees.length; j++) {
                if (i != j && newStalkees[i][1] == newStalkees[j][1]) {
                    return true;
                }
            }
        }
        return false;
    }

    public.save = function (varName, value) {
        if (varName == "stalkees" && _checkIfDuplicateStalkees(value)) {
            alert("You can't have stalkees with the same name.");
            return -1;
        }
        localStorage.setItem(stalkerFlag + varName, JSON.stringify(value));
    };
    public.load = function (varName, createIfNew, newValue) {
        // Default parameters in for pre-2015 JavaScript
        createIfNew = typeof createIfNew !== 'undefined' ? createIfNew : false;
        
		var storageData = JSON.parse(localStorage.getItem(stalkerFlag + varName));
		if (createIfNew) {
			if (storageData === null) {
				this.save(varName, newValue);
				return newValue;
			}
		}
		return storageData;
	};
    public.remove = function (varName) {
        localStorage.removeItem(stalkerFlag + varName);
    };
    return public; // expose externally
}(browserStorage || {}));