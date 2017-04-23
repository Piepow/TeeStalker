Array.prototype.equals = function (array) {
    // if the other array is a falsy value, return
    if (!array)
        return false;

    // compare lengths - can save a lot of time 
    if (this.length != array.length)
        return false;

    for (var i = 0, l = this.length; i < l; i++) {
        // Check if we have nested arrays
        if (this[i] instanceof Array && array[i] instanceof Array) {
            // recurse into the nested arrays
            if (!this[i].equals(array[i]))
                return false;       
        }           
        else if (this[i] != array[i]) { 
            // Warning - two different object instances will never be equal: {x:20} != {x:20}
            return false;
        }           
    }       
    return true;
};

// Hide method from for-in loops
Object.defineProperty(Array.prototype, "equals", {enumerable: false});

var arrayDifference = (function () {
	var public = {};
	function _compareArrayOrOther(var1, var2) {
		var varEquals = false;
		if (Object.prototype.toString.call(var1) === '[object Array]' && Object.prototype.toString.call(var2) !== '[object Array]') {
			// var1 is an array and var2 isn't
			// varEquals = false (unnecessary)
		} else if (Object.prototype.toString.call(var2) === '[object Array]' && Object.prototype.toString.call(var1) !== '[object Array]') {
			// var2 is an array and var1 isn't
			// varEquals = false (unnecessary)
		} else if (Object.prototype.toString.call(var1) === '[object Array]' && Object.prototype.toString.call(var2) === '[object Array]') {
			// Both var1 and var2 are arrays
			if (var1.equals(var2)) {
				varEquals = true;
			} else {
				// varEquals = false (unnecesary)
			}
		} else {
			// Both var1 and var2 are not arrays
			if (var1 == var2) {
				varEquals = true;
			}
		}
		return varEquals;
	}

	public.twoWay = function (array1, array2) {
		if (array1.equals(array2)) {
			return [];
		}
		var unmatched = [];
		this.oneWay(array1, array2, unmatched);
		this.oneWay(array2, array1, unmatched);
		return unmatched;
	};

	public.oneWay = function (array1, array2, unmatched) {
		for (var i = 0; i < array1.length; i++) {
			var foundMatch = false;
			for (var j = 0; j < array2.length; j++) {
				var elementEquals = _compareArrayOrOther(array1[i], array2[j]);
				if (elementEquals === true) {
					foundMatch = true;
					break;
				}
			}
			if (foundMatch === false) {
				if (unmatched.length > 0) {
					var foundMatchFromUnmatched = false;
					for (var k = 0; k < unmatched.length; k++) {
						var unmatchedEquals = _compareArrayOrOther(unmatched[i], array1[i]);
						if (unmatchedEquals === true) {
							foundMatchFromUnmatched = true;
							break;
						}
					}
					if (foundMatchFromUnmatched === false) {
						unmatched.push(array1[i]);
					}
				} else {
					unmatched.push(array1[i]);
				}
			}
		}
	};

	return public;
}(arrayDifference || {}));