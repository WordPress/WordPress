(self["webpackChunkelementor"] = self["webpackChunkelementor"] || []).push([["vendors-node_modules_react-query_devtools_index_js"],{

/***/ "../node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js":
/*!**********************************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js ***!
  \**********************************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _objectWithoutPropertiesLoose)
/* harmony export */ });
function _objectWithoutPropertiesLoose(r, e) {
  if (null == r) return {};
  var t = {};
  for (var n in r) if ({}.hasOwnProperty.call(r, n)) {
    if (-1 !== e.indexOf(n)) continue;
    t[n] = r[n];
  }
  return t;
}


/***/ }),

/***/ "../node_modules/match-sorter/dist/match-sorter.esm.js":
/*!*************************************************************!*\
  !*** ../node_modules/match-sorter/dist/match-sorter.esm.js ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   defaultBaseSortFn: () => (/* binding */ defaultBaseSortFn),
/* harmony export */   matchSorter: () => (/* binding */ matchSorter),
/* harmony export */   rankings: () => (/* binding */ rankings)
/* harmony export */ });
/* harmony import */ var remove_accents__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! remove-accents */ "../node_modules/match-sorter/node_modules/remove-accents/index.js");
/* harmony import */ var remove_accents__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(remove_accents__WEBPACK_IMPORTED_MODULE_0__);


/**
 * @name match-sorter
 * @license MIT license.
 * @copyright (c) 2020 Kent C. Dodds
 * @author Kent C. Dodds <me@kentcdodds.com> (https://kentcdodds.com)
 */
const rankings = {
  CASE_SENSITIVE_EQUAL: 7,
  EQUAL: 6,
  STARTS_WITH: 5,
  WORD_STARTS_WITH: 4,
  CONTAINS: 3,
  ACRONYM: 2,
  MATCHES: 1,
  NO_MATCH: 0
};
const defaultBaseSortFn = (a, b) => String(a.rankedValue).localeCompare(String(b.rankedValue));

/**
 * Takes an array of items and a value and returns a new array with the items that match the given value
 * @param {Array} items - the items to sort
 * @param {String} value - the value to use for ranking
 * @param {Object} options - Some options to configure the sorter
 * @return {Array} - the new sorted array
 */
function matchSorter(items, value, options) {
  if (options === void 0) {
    options = {};
  }
  const {
    keys,
    threshold = rankings.MATCHES,
    baseSort = defaultBaseSortFn,
    sorter = matchedItems => matchedItems.sort((a, b) => sortRankedValues(a, b, baseSort))
  } = options;
  const matchedItems = items.reduce(reduceItemsToRanked, []);
  return sorter(matchedItems).map(_ref => {
    let {
      item
    } = _ref;
    return item;
  });
  function reduceItemsToRanked(matches, item, index) {
    const rankingInfo = getHighestRanking(item, keys, value, options);
    const {
      rank,
      keyThreshold = threshold
    } = rankingInfo;
    if (rank >= keyThreshold) {
      matches.push({
        ...rankingInfo,
        item,
        index
      });
    }
    return matches;
  }
}
matchSorter.rankings = rankings;

/**
 * Gets the highest ranking for value for the given item based on its values for the given keys
 * @param {*} item - the item to rank
 * @param {Array} keys - the keys to get values from the item for the ranking
 * @param {String} value - the value to rank against
 * @param {Object} options - options to control the ranking
 * @return {{rank: Number, keyIndex: Number, keyThreshold: Number}} - the highest ranking
 */
function getHighestRanking(item, keys, value, options) {
  if (!keys) {
    // if keys is not specified, then we assume the item given is ready to be matched
    const stringItem = item;
    return {
      // ends up being duplicate of 'item' in matches but consistent
      rankedValue: stringItem,
      rank: getMatchRanking(stringItem, value, options),
      keyIndex: -1,
      keyThreshold: options.threshold
    };
  }
  const valuesToRank = getAllValuesToRank(item, keys);
  return valuesToRank.reduce((_ref2, _ref3, i) => {
    let {
      rank,
      rankedValue,
      keyIndex,
      keyThreshold
    } = _ref2;
    let {
      itemValue,
      attributes
    } = _ref3;
    let newRank = getMatchRanking(itemValue, value, options);
    let newRankedValue = rankedValue;
    const {
      minRanking,
      maxRanking,
      threshold
    } = attributes;
    if (newRank < minRanking && newRank >= rankings.MATCHES) {
      newRank = minRanking;
    } else if (newRank > maxRanking) {
      newRank = maxRanking;
    }
    if (newRank > rank) {
      rank = newRank;
      keyIndex = i;
      keyThreshold = threshold;
      newRankedValue = itemValue;
    }
    return {
      rankedValue: newRankedValue,
      rank,
      keyIndex,
      keyThreshold
    };
  }, {
    rankedValue: item,
    rank: rankings.NO_MATCH,
    keyIndex: -1,
    keyThreshold: options.threshold
  });
}

/**
 * Gives a rankings score based on how well the two strings match.
 * @param {String} testString - the string to test against
 * @param {String} stringToRank - the string to rank
 * @param {Object} options - options for the match (like keepDiacritics for comparison)
 * @returns {Number} the ranking for how well stringToRank matches testString
 */
function getMatchRanking(testString, stringToRank, options) {
  testString = prepareValueForComparison(testString, options);
  stringToRank = prepareValueForComparison(stringToRank, options);

  // too long
  if (stringToRank.length > testString.length) {
    return rankings.NO_MATCH;
  }

  // case sensitive equals
  if (testString === stringToRank) {
    return rankings.CASE_SENSITIVE_EQUAL;
  }

  // Lower casing before further comparison
  testString = testString.toLowerCase();
  stringToRank = stringToRank.toLowerCase();

  // case insensitive equals
  if (testString === stringToRank) {
    return rankings.EQUAL;
  }

  // starts with
  if (testString.startsWith(stringToRank)) {
    return rankings.STARTS_WITH;
  }

  // word starts with
  if (testString.includes(` ${stringToRank}`)) {
    return rankings.WORD_STARTS_WITH;
  }

  // contains
  if (testString.includes(stringToRank)) {
    return rankings.CONTAINS;
  } else if (stringToRank.length === 1) {
    // If the only character in the given stringToRank
    //   isn't even contained in the testString, then
    //   it's definitely not a match.
    return rankings.NO_MATCH;
  }

  // acronym
  if (getAcronym(testString).includes(stringToRank)) {
    return rankings.ACRONYM;
  }

  // will return a number between rankings.MATCHES and
  // rankings.MATCHES + 1 depending  on how close of a match it is.
  return getClosenessRanking(testString, stringToRank);
}

/**
 * Generates an acronym for a string.
 *
 * @param {String} string the string for which to produce the acronym
 * @returns {String} the acronym
 */
function getAcronym(string) {
  let acronym = '';
  const wordsInString = string.split(' ');
  wordsInString.forEach(wordInString => {
    const splitByHyphenWords = wordInString.split('-');
    splitByHyphenWords.forEach(splitByHyphenWord => {
      acronym += splitByHyphenWord.substr(0, 1);
    });
  });
  return acronym;
}

/**
 * Returns a score based on how spread apart the
 * characters from the stringToRank are within the testString.
 * A number close to rankings.MATCHES represents a loose match. A number close
 * to rankings.MATCHES + 1 represents a tighter match.
 * @param {String} testString - the string to test against
 * @param {String} stringToRank - the string to rank
 * @returns {Number} the number between rankings.MATCHES and
 * rankings.MATCHES + 1 for how well stringToRank matches testString
 */
function getClosenessRanking(testString, stringToRank) {
  let matchingInOrderCharCount = 0;
  let charNumber = 0;
  function findMatchingCharacter(matchChar, string, index) {
    for (let j = index, J = string.length; j < J; j++) {
      const stringChar = string[j];
      if (stringChar === matchChar) {
        matchingInOrderCharCount += 1;
        return j + 1;
      }
    }
    return -1;
  }
  function getRanking(spread) {
    const spreadPercentage = 1 / spread;
    const inOrderPercentage = matchingInOrderCharCount / stringToRank.length;
    const ranking = rankings.MATCHES + inOrderPercentage * spreadPercentage;
    return ranking;
  }
  const firstIndex = findMatchingCharacter(stringToRank[0], testString, 0);
  if (firstIndex < 0) {
    return rankings.NO_MATCH;
  }
  charNumber = firstIndex;
  for (let i = 1, I = stringToRank.length; i < I; i++) {
    const matchChar = stringToRank[i];
    charNumber = findMatchingCharacter(matchChar, testString, charNumber);
    const found = charNumber > -1;
    if (!found) {
      return rankings.NO_MATCH;
    }
  }
  const spread = charNumber - firstIndex;
  return getRanking(spread);
}

/**
 * Sorts items that have a rank, index, and keyIndex
 * @param {Object} a - the first item to sort
 * @param {Object} b - the second item to sort
 * @return {Number} -1 if a should come first, 1 if b should come first, 0 if equal
 */
function sortRankedValues(a, b, baseSort) {
  const aFirst = -1;
  const bFirst = 1;
  const {
    rank: aRank,
    keyIndex: aKeyIndex
  } = a;
  const {
    rank: bRank,
    keyIndex: bKeyIndex
  } = b;
  const same = aRank === bRank;
  if (same) {
    if (aKeyIndex === bKeyIndex) {
      // use the base sort function as a tie-breaker
      return baseSort(a, b);
    } else {
      return aKeyIndex < bKeyIndex ? aFirst : bFirst;
    }
  } else {
    return aRank > bRank ? aFirst : bFirst;
  }
}

/**
 * Prepares value for comparison by stringifying it, removing diacritics (if specified)
 * @param {String} value - the value to clean
 * @param {Object} options - {keepDiacritics: whether to remove diacritics}
 * @return {String} the prepared value
 */
function prepareValueForComparison(value, _ref4) {
  let {
    keepDiacritics
  } = _ref4;
  // value might not actually be a string at this point (we don't get to choose)
  // so part of preparing the value for comparison is ensure that it is a string
  value = `${value}`; // toString
  if (!keepDiacritics) {
    value = remove_accents__WEBPACK_IMPORTED_MODULE_0___default()(value);
  }
  return value;
}

/**
 * Gets value for key in item at arbitrarily nested keypath
 * @param {Object} item - the item
 * @param {Object|Function} key - the potentially nested keypath or property callback
 * @return {Array} - an array containing the value(s) at the nested keypath
 */
function getItemValues(item, key) {
  if (typeof key === 'object') {
    key = key.key;
  }
  let value;
  if (typeof key === 'function') {
    value = key(item);
  } else if (item == null) {
    value = null;
  } else if (Object.hasOwnProperty.call(item, key)) {
    value = item[key];
  } else if (key.includes('.')) {
    // eslint-disable-next-line @typescript-eslint/no-unsafe-call
    return getNestedValues(key, item);
  } else {
    value = null;
  }

  // because `value` can also be undefined
  if (value == null) {
    return [];
  }
  if (Array.isArray(value)) {
    return value;
  }
  return [String(value)];
}

/**
 * Given path: "foo.bar.baz"
 * And item: {foo: {bar: {baz: 'buzz'}}}
 *   -> 'buzz'
 * @param path a dot-separated set of keys
 * @param item the item to get the value from
 */
function getNestedValues(path, item) {
  const keys = path.split('.');
  let values = [item];
  for (let i = 0, I = keys.length; i < I; i++) {
    const nestedKey = keys[i];
    let nestedValues = [];
    for (let j = 0, J = values.length; j < J; j++) {
      const nestedItem = values[j];
      if (nestedItem == null) continue;
      if (Object.hasOwnProperty.call(nestedItem, nestedKey)) {
        const nestedValue = nestedItem[nestedKey];
        if (nestedValue != null) {
          nestedValues.push(nestedValue);
        }
      } else if (nestedKey === '*') {
        // ensure that values is an array
        nestedValues = nestedValues.concat(nestedItem);
      }
    }
    values = nestedValues;
  }
  if (Array.isArray(values[0])) {
    // keep allowing the implicit wildcard for an array of strings at the end of
    // the path; don't use `.flat()` because that's not available in node.js v10
    const result = [];
    return result.concat(...values);
  }
  // Based on our logic it should be an array of strings by now...
  // assuming the user's path terminated in strings
  return values;
}

/**
 * Gets all the values for the given keys in the given item and returns an array of those values
 * @param item - the item from which the values will be retrieved
 * @param keys - the keys to use to retrieve the values
 * @return objects with {itemValue, attributes}
 */
function getAllValuesToRank(item, keys) {
  const allValues = [];
  for (let j = 0, J = keys.length; j < J; j++) {
    const key = keys[j];
    const attributes = getKeyAttributes(key);
    const itemValues = getItemValues(item, key);
    for (let i = 0, I = itemValues.length; i < I; i++) {
      allValues.push({
        itemValue: itemValues[i],
        attributes
      });
    }
  }
  return allValues;
}
const defaultKeyAttributes = {
  maxRanking: Infinity,
  minRanking: -Infinity
};
/**
 * Gets all the attributes for the given key
 * @param key - the key from which the attributes will be retrieved
 * @return object containing the key's attributes
 */
function getKeyAttributes(key) {
  if (typeof key === 'string') {
    return defaultKeyAttributes;
  }
  return {
    ...defaultKeyAttributes,
    ...key
  };
}

/*
eslint
  no-continue: "off",
*/




/***/ }),

/***/ "../node_modules/match-sorter/node_modules/remove-accents/index.js":
/*!*************************************************************************!*\
  !*** ../node_modules/match-sorter/node_modules/remove-accents/index.js ***!
  \*************************************************************************/
/***/ ((module) => {

var characterMap = {
	"À": "A",
	"Á": "A",
	"Â": "A",
	"Ã": "A",
	"Ä": "A",
	"Å": "A",
	"Ấ": "A",
	"Ắ": "A",
	"Ẳ": "A",
	"Ẵ": "A",
	"Ặ": "A",
	"Æ": "AE",
	"Ầ": "A",
	"Ằ": "A",
	"Ȃ": "A",
	"Ả": "A",
	"Ạ": "A",
	"Ẩ": "A",
	"Ẫ": "A",
	"Ậ": "A",
	"Ç": "C",
	"Ḉ": "C",
	"È": "E",
	"É": "E",
	"Ê": "E",
	"Ë": "E",
	"Ế": "E",
	"Ḗ": "E",
	"Ề": "E",
	"Ḕ": "E",
	"Ḝ": "E",
	"Ȇ": "E",
	"Ẻ": "E",
	"Ẽ": "E",
	"Ẹ": "E",
	"Ể": "E",
	"Ễ": "E",
	"Ệ": "E",
	"Ì": "I",
	"Í": "I",
	"Î": "I",
	"Ï": "I",
	"Ḯ": "I",
	"Ȋ": "I",
	"Ỉ": "I",
	"Ị": "I",
	"Ð": "D",
	"Ñ": "N",
	"Ò": "O",
	"Ó": "O",
	"Ô": "O",
	"Õ": "O",
	"Ö": "O",
	"Ø": "O",
	"Ố": "O",
	"Ṍ": "O",
	"Ṓ": "O",
	"Ȏ": "O",
	"Ỏ": "O",
	"Ọ": "O",
	"Ổ": "O",
	"Ỗ": "O",
	"Ộ": "O",
	"Ờ": "O",
	"Ở": "O",
	"Ỡ": "O",
	"Ớ": "O",
	"Ợ": "O",
	"Ù": "U",
	"Ú": "U",
	"Û": "U",
	"Ü": "U",
	"Ủ": "U",
	"Ụ": "U",
	"Ử": "U",
	"Ữ": "U",
	"Ự": "U",
	"Ý": "Y",
	"à": "a",
	"á": "a",
	"â": "a",
	"ã": "a",
	"ä": "a",
	"å": "a",
	"ấ": "a",
	"ắ": "a",
	"ẳ": "a",
	"ẵ": "a",
	"ặ": "a",
	"æ": "ae",
	"ầ": "a",
	"ằ": "a",
	"ȃ": "a",
	"ả": "a",
	"ạ": "a",
	"ẩ": "a",
	"ẫ": "a",
	"ậ": "a",
	"ç": "c",
	"ḉ": "c",
	"è": "e",
	"é": "e",
	"ê": "e",
	"ë": "e",
	"ế": "e",
	"ḗ": "e",
	"ề": "e",
	"ḕ": "e",
	"ḝ": "e",
	"ȇ": "e",
	"ẻ": "e",
	"ẽ": "e",
	"ẹ": "e",
	"ể": "e",
	"ễ": "e",
	"ệ": "e",
	"ì": "i",
	"í": "i",
	"î": "i",
	"ï": "i",
	"ḯ": "i",
	"ȋ": "i",
	"ỉ": "i",
	"ị": "i",
	"ð": "d",
	"ñ": "n",
	"ò": "o",
	"ó": "o",
	"ô": "o",
	"õ": "o",
	"ö": "o",
	"ø": "o",
	"ố": "o",
	"ṍ": "o",
	"ṓ": "o",
	"ȏ": "o",
	"ỏ": "o",
	"ọ": "o",
	"ổ": "o",
	"ỗ": "o",
	"ộ": "o",
	"ờ": "o",
	"ở": "o",
	"ỡ": "o",
	"ớ": "o",
	"ợ": "o",
	"ù": "u",
	"ú": "u",
	"û": "u",
	"ü": "u",
	"ủ": "u",
	"ụ": "u",
	"ử": "u",
	"ữ": "u",
	"ự": "u",
	"ý": "y",
	"ÿ": "y",
	"Ā": "A",
	"ā": "a",
	"Ă": "A",
	"ă": "a",
	"Ą": "A",
	"ą": "a",
	"Ć": "C",
	"ć": "c",
	"Ĉ": "C",
	"ĉ": "c",
	"Ċ": "C",
	"ċ": "c",
	"Č": "C",
	"č": "c",
	"C̆": "C",
	"c̆": "c",
	"Ď": "D",
	"ď": "d",
	"Đ": "D",
	"đ": "d",
	"Ē": "E",
	"ē": "e",
	"Ĕ": "E",
	"ĕ": "e",
	"Ė": "E",
	"ė": "e",
	"Ę": "E",
	"ę": "e",
	"Ě": "E",
	"ě": "e",
	"Ĝ": "G",
	"Ǵ": "G",
	"ĝ": "g",
	"ǵ": "g",
	"Ğ": "G",
	"ğ": "g",
	"Ġ": "G",
	"ġ": "g",
	"Ģ": "G",
	"ģ": "g",
	"Ĥ": "H",
	"ĥ": "h",
	"Ħ": "H",
	"ħ": "h",
	"Ḫ": "H",
	"ḫ": "h",
	"Ĩ": "I",
	"ĩ": "i",
	"Ī": "I",
	"ī": "i",
	"Ĭ": "I",
	"ĭ": "i",
	"Į": "I",
	"į": "i",
	"İ": "I",
	"ı": "i",
	"Ĳ": "IJ",
	"ĳ": "ij",
	"Ĵ": "J",
	"ĵ": "j",
	"Ķ": "K",
	"ķ": "k",
	"Ḱ": "K",
	"ḱ": "k",
	"K̆": "K",
	"k̆": "k",
	"Ĺ": "L",
	"ĺ": "l",
	"Ļ": "L",
	"ļ": "l",
	"Ľ": "L",
	"ľ": "l",
	"Ŀ": "L",
	"ŀ": "l",
	"Ł": "l",
	"ł": "l",
	"Ḿ": "M",
	"ḿ": "m",
	"M̆": "M",
	"m̆": "m",
	"Ń": "N",
	"ń": "n",
	"Ņ": "N",
	"ņ": "n",
	"Ň": "N",
	"ň": "n",
	"ŉ": "n",
	"N̆": "N",
	"n̆": "n",
	"Ō": "O",
	"ō": "o",
	"Ŏ": "O",
	"ŏ": "o",
	"Ő": "O",
	"ő": "o",
	"Œ": "OE",
	"œ": "oe",
	"P̆": "P",
	"p̆": "p",
	"Ŕ": "R",
	"ŕ": "r",
	"Ŗ": "R",
	"ŗ": "r",
	"Ř": "R",
	"ř": "r",
	"R̆": "R",
	"r̆": "r",
	"Ȓ": "R",
	"ȓ": "r",
	"Ś": "S",
	"ś": "s",
	"Ŝ": "S",
	"ŝ": "s",
	"Ş": "S",
	"Ș": "S",
	"ș": "s",
	"ş": "s",
	"Š": "S",
	"š": "s",
	"Ţ": "T",
	"ţ": "t",
	"ț": "t",
	"Ț": "T",
	"Ť": "T",
	"ť": "t",
	"Ŧ": "T",
	"ŧ": "t",
	"T̆": "T",
	"t̆": "t",
	"Ũ": "U",
	"ũ": "u",
	"Ū": "U",
	"ū": "u",
	"Ŭ": "U",
	"ŭ": "u",
	"Ů": "U",
	"ů": "u",
	"Ű": "U",
	"ű": "u",
	"Ų": "U",
	"ų": "u",
	"Ȗ": "U",
	"ȗ": "u",
	"V̆": "V",
	"v̆": "v",
	"Ŵ": "W",
	"ŵ": "w",
	"Ẃ": "W",
	"ẃ": "w",
	"X̆": "X",
	"x̆": "x",
	"Ŷ": "Y",
	"ŷ": "y",
	"Ÿ": "Y",
	"Y̆": "Y",
	"y̆": "y",
	"Ź": "Z",
	"ź": "z",
	"Ż": "Z",
	"ż": "z",
	"Ž": "Z",
	"ž": "z",
	"ſ": "s",
	"ƒ": "f",
	"Ơ": "O",
	"ơ": "o",
	"Ư": "U",
	"ư": "u",
	"Ǎ": "A",
	"ǎ": "a",
	"Ǐ": "I",
	"ǐ": "i",
	"Ǒ": "O",
	"ǒ": "o",
	"Ǔ": "U",
	"ǔ": "u",
	"Ǖ": "U",
	"ǖ": "u",
	"Ǘ": "U",
	"ǘ": "u",
	"Ǚ": "U",
	"ǚ": "u",
	"Ǜ": "U",
	"ǜ": "u",
	"Ứ": "U",
	"ứ": "u",
	"Ṹ": "U",
	"ṹ": "u",
	"Ǻ": "A",
	"ǻ": "a",
	"Ǽ": "AE",
	"ǽ": "ae",
	"Ǿ": "O",
	"ǿ": "o",
	"Þ": "TH",
	"þ": "th",
	"Ṕ": "P",
	"ṕ": "p",
	"Ṥ": "S",
	"ṥ": "s",
	"X́": "X",
	"x́": "x",
	"Ѓ": "Г",
	"ѓ": "г",
	"Ќ": "К",
	"ќ": "к",
	"A̋": "A",
	"a̋": "a",
	"E̋": "E",
	"e̋": "e",
	"I̋": "I",
	"i̋": "i",
	"Ǹ": "N",
	"ǹ": "n",
	"Ồ": "O",
	"ồ": "o",
	"Ṑ": "O",
	"ṑ": "o",
	"Ừ": "U",
	"ừ": "u",
	"Ẁ": "W",
	"ẁ": "w",
	"Ỳ": "Y",
	"ỳ": "y",
	"Ȁ": "A",
	"ȁ": "a",
	"Ȅ": "E",
	"ȅ": "e",
	"Ȉ": "I",
	"ȉ": "i",
	"Ȍ": "O",
	"ȍ": "o",
	"Ȑ": "R",
	"ȑ": "r",
	"Ȕ": "U",
	"ȕ": "u",
	"B̌": "B",
	"b̌": "b",
	"Č̣": "C",
	"č̣": "c",
	"Ê̌": "E",
	"ê̌": "e",
	"F̌": "F",
	"f̌": "f",
	"Ǧ": "G",
	"ǧ": "g",
	"Ȟ": "H",
	"ȟ": "h",
	"J̌": "J",
	"ǰ": "j",
	"Ǩ": "K",
	"ǩ": "k",
	"M̌": "M",
	"m̌": "m",
	"P̌": "P",
	"p̌": "p",
	"Q̌": "Q",
	"q̌": "q",
	"Ř̩": "R",
	"ř̩": "r",
	"Ṧ": "S",
	"ṧ": "s",
	"V̌": "V",
	"v̌": "v",
	"W̌": "W",
	"w̌": "w",
	"X̌": "X",
	"x̌": "x",
	"Y̌": "Y",
	"y̌": "y",
	"A̧": "A",
	"a̧": "a",
	"B̧": "B",
	"b̧": "b",
	"Ḑ": "D",
	"ḑ": "d",
	"Ȩ": "E",
	"ȩ": "e",
	"Ɛ̧": "E",
	"ɛ̧": "e",
	"Ḩ": "H",
	"ḩ": "h",
	"I̧": "I",
	"i̧": "i",
	"Ɨ̧": "I",
	"ɨ̧": "i",
	"M̧": "M",
	"m̧": "m",
	"O̧": "O",
	"o̧": "o",
	"Q̧": "Q",
	"q̧": "q",
	"U̧": "U",
	"u̧": "u",
	"X̧": "X",
	"x̧": "x",
	"Z̧": "Z",
	"z̧": "z",
	"й":"и",
	"Й":"И",
	"ё":"е",
	"Ё":"Е",
};

var chars = Object.keys(characterMap).join('|');
var allAccents = new RegExp(chars, 'g');
var firstAccent = new RegExp(chars, '');

function matcher(match) {
	return characterMap[match];
}

var removeAccents = function(string) {
	return string.replace(allAccents, matcher);
};

var hasAccents = function(string) {
	return !!string.match(firstAccent);
};

module.exports = removeAccents;
module.exports.has = hasAccents;
module.exports.remove = removeAccents;


/***/ }),

/***/ "../node_modules/react-query/devtools/index.js":
/*!*****************************************************!*\
  !*** ../node_modules/react-query/devtools/index.js ***!
  \*****************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

if (false) // removed by dead control flow
{} else {
  module.exports = __webpack_require__(/*! ./development */ "../node_modules/react-query/es/devtools/index.js")
}


/***/ }),

/***/ "../node_modules/react-query/es/devtools/Explorer.js":
/*!***********************************************************!*\
  !*** ../node_modules/react-query/es/devtools/Explorer.js ***!
  \***********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DefaultRenderer: () => (/* binding */ DefaultRenderer),
/* harmony export */   Entry: () => (/* binding */ Entry),
/* harmony export */   ExpandButton: () => (/* binding */ ExpandButton),
/* harmony export */   Expander: () => (/* binding */ Expander),
/* harmony export */   Info: () => (/* binding */ Info),
/* harmony export */   Label: () => (/* binding */ Label),
/* harmony export */   LabelButton: () => (/* binding */ LabelButton),
/* harmony export */   SubEntries: () => (/* binding */ SubEntries),
/* harmony export */   Value: () => (/* binding */ Value),
/* harmony export */   chunkArray: () => (/* binding */ chunkArray),
/* harmony export */   "default": () => (/* binding */ Explorer)
/* harmony export */ });
/* harmony import */ var _babel_runtime_helpers_esm_objectWithoutPropertiesLoose__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectWithoutPropertiesLoose */ "../node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js");
/* harmony import */ var _babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/extends */ "../node_modules/@babel/runtime/helpers/esm/extends.js");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./utils */ "../node_modules/react-query/es/devtools/utils.js");




var Entry = (0,_utils__WEBPACK_IMPORTED_MODULE_3__.styled)('div', {
  fontFamily: 'Menlo, monospace',
  fontSize: '1em',
  lineHeight: '1.7',
  outline: 'none',
  wordBreak: 'break-word'
});
var Label = (0,_utils__WEBPACK_IMPORTED_MODULE_3__.styled)('span', {
  color: 'white'
});
var LabelButton = (0,_utils__WEBPACK_IMPORTED_MODULE_3__.styled)('button', {
  cursor: 'pointer',
  color: 'white'
});
var ExpandButton = (0,_utils__WEBPACK_IMPORTED_MODULE_3__.styled)('button', {
  cursor: 'pointer',
  color: 'inherit',
  font: 'inherit',
  outline: 'inherit',
  background: 'transparent',
  border: 'none',
  padding: 0
});
var Value = (0,_utils__WEBPACK_IMPORTED_MODULE_3__.styled)('span', function (_props, theme) {
  return {
    color: theme.danger
  };
});
var SubEntries = (0,_utils__WEBPACK_IMPORTED_MODULE_3__.styled)('div', {
  marginLeft: '.1em',
  paddingLeft: '1em',
  borderLeft: '2px solid rgba(0,0,0,.15)'
});
var Info = (0,_utils__WEBPACK_IMPORTED_MODULE_3__.styled)('span', {
  color: 'grey',
  fontSize: '.7em'
});
var Expander = function Expander(_ref) {
  var expanded = _ref.expanded,
      _ref$style = _ref.style,
      style = _ref$style === void 0 ? {} : _ref$style;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement("span", {
    style: (0,_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_1__["default"])({
      display: 'inline-block',
      transition: 'all .1s ease',
      transform: "rotate(" + (expanded ? 90 : 0) + "deg) " + (style.transform || '')
    }, style)
  }, "\u25B6");
};

/**
 * Chunk elements in the array by size
 *
 * when the array cannot be chunked evenly by size, the last chunk will be
 * filled with the remaining elements
 *
 * @example
 * chunkArray(['a','b', 'c', 'd', 'e'], 2) // returns [['a','b'], ['c', 'd'], ['e']]
 */
function chunkArray(array, size) {
  if (size < 1) return [];
  var i = 0;
  var result = [];

  while (i < array.length) {
    result.push(array.slice(i, i + size));
    i = i + size;
  }

  return result;
}
var DefaultRenderer = function DefaultRenderer(_ref2) {
  var HandleEntry = _ref2.HandleEntry,
      label = _ref2.label,
      value = _ref2.value,
      _ref2$subEntries = _ref2.subEntries,
      subEntries = _ref2$subEntries === void 0 ? [] : _ref2$subEntries,
      _ref2$subEntryPages = _ref2.subEntryPages,
      subEntryPages = _ref2$subEntryPages === void 0 ? [] : _ref2$subEntryPages,
      type = _ref2.type,
      _ref2$expanded = _ref2.expanded,
      expanded = _ref2$expanded === void 0 ? false : _ref2$expanded,
      toggleExpanded = _ref2.toggleExpanded,
      pageSize = _ref2.pageSize;

  var _React$useState = react__WEBPACK_IMPORTED_MODULE_2___default().useState([]),
      expandedPages = _React$useState[0],
      setExpandedPages = _React$useState[1];

  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(Entry, {
    key: label
  }, (subEntryPages == null ? void 0 : subEntryPages.length) ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement((react__WEBPACK_IMPORTED_MODULE_2___default().Fragment), null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(ExpandButton, {
    onClick: function onClick() {
      return toggleExpanded();
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(Expander, {
    expanded: expanded
  }), " ", label, ' ', /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(Info, null, String(type).toLowerCase() === 'iterable' ? '(Iterable) ' : '', subEntries.length, " ", subEntries.length > 1 ? "items" : "item")), expanded ? subEntryPages.length === 1 ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(SubEntries, null, subEntries.map(function (entry) {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(HandleEntry, {
      key: entry.label,
      entry: entry
    });
  })) : /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(SubEntries, null, subEntryPages.map(function (entries, index) {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement("div", {
      key: index
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(Entry, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(LabelButton, {
      onClick: function onClick() {
        return setExpandedPages(function (old) {
          return old.includes(index) ? old.filter(function (d) {
            return d !== index;
          }) : [].concat(old, [index]);
        });
      }
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(Expander, {
      expanded: expanded
    }), " [", index * pageSize, " ...", ' ', index * pageSize + pageSize - 1, "]"), expandedPages.includes(index) ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(SubEntries, null, entries.map(function (entry) {
      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(HandleEntry, {
        key: entry.label,
        entry: entry
      });
    })) : null));
  })) : null) : /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement((react__WEBPACK_IMPORTED_MODULE_2___default().Fragment), null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(Label, null, label, ":"), " ", /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(Value, null, (0,_utils__WEBPACK_IMPORTED_MODULE_3__.displayValue)(value))));
};

function isIterable(x) {
  return Symbol.iterator in x;
}

function Explorer(_ref3) {
  var value = _ref3.value,
      defaultExpanded = _ref3.defaultExpanded,
      _ref3$renderer = _ref3.renderer,
      renderer = _ref3$renderer === void 0 ? DefaultRenderer : _ref3$renderer,
      _ref3$pageSize = _ref3.pageSize,
      pageSize = _ref3$pageSize === void 0 ? 100 : _ref3$pageSize,
      rest = (0,_babel_runtime_helpers_esm_objectWithoutPropertiesLoose__WEBPACK_IMPORTED_MODULE_0__["default"])(_ref3, ["value", "defaultExpanded", "renderer", "pageSize"]);

  var _React$useState2 = react__WEBPACK_IMPORTED_MODULE_2___default().useState(Boolean(defaultExpanded)),
      expanded = _React$useState2[0],
      setExpanded = _React$useState2[1];

  var toggleExpanded = react__WEBPACK_IMPORTED_MODULE_2___default().useCallback(function () {
    return setExpanded(function (old) {
      return !old;
    });
  }, []);
  var type = typeof value;
  var subEntries = [];

  var makeProperty = function makeProperty(sub) {
    var _ref4;

    var subDefaultExpanded = defaultExpanded === true ? (_ref4 = {}, _ref4[sub.label] = true, _ref4) : defaultExpanded == null ? void 0 : defaultExpanded[sub.label];
    return (0,_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_1__["default"])({}, sub, {
      defaultExpanded: subDefaultExpanded
    });
  };

  if (Array.isArray(value)) {
    type = 'array';
    subEntries = value.map(function (d, i) {
      return makeProperty({
        label: i.toString(),
        value: d
      });
    });
  } else if (value !== null && typeof value === 'object' && isIterable(value) && typeof value[Symbol.iterator] === 'function') {
    type = 'Iterable';
    subEntries = Array.from(value, function (val, i) {
      return makeProperty({
        label: i.toString(),
        value: val
      });
    });
  } else if (typeof value === 'object' && value !== null) {
    type = 'object';
    subEntries = Object.entries(value).map(function (_ref5) {
      var key = _ref5[0],
          val = _ref5[1];
      return makeProperty({
        label: key,
        value: val
      });
    });
  }

  var subEntryPages = chunkArray(subEntries, pageSize);
  return renderer((0,_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_1__["default"])({
    HandleEntry: function HandleEntry(_ref6) {
      var entry = _ref6.entry;
      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(Explorer, (0,_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_1__["default"])({
        value: value,
        renderer: renderer
      }, rest, entry));
    },
    type: type,
    subEntries: subEntries,
    subEntryPages: subEntryPages,
    value: value,
    expanded: expanded,
    toggleExpanded: toggleExpanded,
    pageSize: pageSize
  }, rest));
}

/***/ }),

/***/ "../node_modules/react-query/es/devtools/Logo.js":
/*!*******************************************************!*\
  !*** ../node_modules/react-query/es/devtools/Logo.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Logo)
/* harmony export */ });
/* harmony import */ var _babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/extends */ "../node_modules/@babel/runtime/helpers/esm/extends.js");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_1__);


function Logo(props) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_1__.createElement("svg", (0,_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__["default"])({
    width: "40px",
    height: "40px",
    viewBox: "0 0 190 190",
    version: "1.1"
  }, props), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_1__.createElement("g", {
    stroke: "none",
    strokeWidth: "1",
    fill: "none",
    fillRule: "evenodd"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_1__.createElement("g", {
    transform: "translate(-33.000000, 0.000000)"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_1__.createElement("path", {
    d: "M72.7239712,61.3436237 C69.631224,46.362877 68.9675112,34.8727722 70.9666331,26.5293551 C72.1555965,21.5671678 74.3293088,17.5190846 77.6346064,14.5984631 C81.1241394,11.5150478 85.5360327,10.0020122 90.493257,10.0020122 C98.6712013,10.0020122 107.26826,13.7273214 116.455725,20.8044264 C120.20312,23.6910458 124.092437,27.170411 128.131651,31.2444746 C128.45314,30.8310265 128.816542,30.4410453 129.22143,30.0806152 C140.64098,19.9149716 150.255245,13.5989272 158.478408,11.1636507 C163.367899,9.715636 167.958526,9.57768202 172.138936,10.983031 C176.551631,12.4664684 180.06766,15.5329489 182.548314,19.8281091 C186.642288,26.9166735 187.721918,36.2310983 186.195595,47.7320243 C185.573451,52.4199112 184.50985,57.5263831 183.007094,63.0593153 C183.574045,63.1277086 184.142416,63.2532808 184.705041,63.4395297 C199.193932,68.2358678 209.453582,73.3937462 215.665021,79.2882839 C219.360669,82.7953831 221.773972,86.6998434 222.646365,91.0218204 C223.567176,95.5836746 222.669313,100.159332 220.191548,104.451297 C216.105211,111.529614 208.591643,117.11221 197.887587,121.534031 C193.589552,123.309539 188.726579,124.917559 183.293259,126.363748 C183.541176,126.92292 183.733521,127.516759 183.862138,128.139758 C186.954886,143.120505 187.618598,154.61061 185.619477,162.954027 C184.430513,167.916214 182.256801,171.964297 178.951503,174.884919 C175.46197,177.968334 171.050077,179.48137 166.092853,179.48137 C157.914908,179.48137 149.31785,175.756061 140.130385,168.678956 C136.343104,165.761613 132.410866,162.238839 128.325434,158.108619 C127.905075,158.765474 127.388968,159.376011 126.77857,159.919385 C115.35902,170.085028 105.744755,176.401073 97.5215915,178.836349 C92.6321009,180.284364 88.0414736,180.422318 83.8610636,179.016969 C79.4483686,177.533532 75.9323404,174.467051 73.4516862,170.171891 C69.3577116,163.083327 68.2780823,153.768902 69.8044053,142.267976 C70.449038,137.410634 71.56762,132.103898 73.1575891,126.339009 C72.5361041,126.276104 71.9120754,126.144816 71.2949591,125.940529 C56.8060684,121.144191 46.5464184,115.986312 40.3349789,110.091775 C36.6393312,106.584675 34.2260275,102.680215 33.3536352,98.3582381 C32.4328237,93.7963839 33.3306866,89.2207269 35.8084524,84.9287618 C39.8947886,77.8504443 47.4083565,72.2678481 58.1124133,67.8460273 C62.5385143,66.0176154 67.5637208,64.366822 73.1939394,62.8874674 C72.9933393,62.3969171 72.8349374,61.8811235 72.7239712,61.3436237 Z",
    fill: "#002C4B",
    fillRule: "nonzero",
    transform: "translate(128.000000, 95.000000) scale(-1, 1) translate(-128.000000, -95.000000) "
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_1__.createElement("path", {
    d: "M113.396882,64 L142.608177,64 C144.399254,64 146.053521,64.958025 146.944933,66.5115174 L161.577138,92.0115174 C162.461464,93.5526583 162.461464,95.4473417 161.577138,96.9884826 L146.944933,122.488483 C146.053521,124.041975 144.399254,125 142.608177,125 L113.396882,125 C111.605806,125 109.951539,124.041975 109.060126,122.488483 L94.4279211,96.9884826 C93.543596,95.4473417 93.543596,93.5526583 94.4279211,92.0115174 L109.060126,66.5115174 C109.951539,64.958025 111.605806,64 113.396882,64 Z M138.987827,70.2765273 C140.779849,70.2765273 142.434839,71.2355558 143.325899,72.7903404 L154.343038,92.0138131 C155.225607,93.5537825 155.225607,95.4462175 154.343038,96.9861869 L143.325899,116.20966 C142.434839,117.764444 140.779849,118.723473 138.987827,118.723473 L117.017233,118.723473 C115.225211,118.723473 113.570221,117.764444 112.67916,116.20966 L101.662022,96.9861869 C100.779452,95.4462175 100.779452,93.5537825 101.662022,92.0138131 L112.67916,72.7903404 C113.570221,71.2355558 115.225211,70.2765273 117.017233,70.2765273 L138.987827,70.2765273 Z M135.080648,77.1414791 L120.924411,77.1414791 C119.134228,77.1414791 117.480644,78.0985567 116.5889,79.6508285 L116.5889,79.6508285 L109.489217,92.0093494 C108.603232,93.5515958 108.603232,95.4484042 109.489217,96.9906506 L109.489217,96.9906506 L116.5889,109.349172 C117.480644,110.901443 119.134228,111.858521 120.924411,111.858521 L120.924411,111.858521 L135.080648,111.858521 C136.870831,111.858521 138.524416,110.901443 139.41616,109.349172 L139.41616,109.349172 L146.515843,96.9906506 C147.401828,95.4484042 147.401828,93.5515958 146.515843,92.0093494 L146.515843,92.0093494 L139.41616,79.6508285 C138.524416,78.0985567 136.870831,77.1414791 135.080648,77.1414791 L135.080648,77.1414791 Z M131.319186,83.7122186 C133.108028,83.7122186 134.760587,84.6678753 135.652827,86.2183156 L138.983552,92.0060969 C139.87203,93.5500005 139.87203,95.4499995 138.983552,96.9939031 L135.652827,102.781684 C134.760587,104.332125 133.108028,105.287781 131.319186,105.287781 L124.685874,105.287781 C122.897032,105.287781 121.244473,104.332125 120.352233,102.781684 L117.021508,96.9939031 C116.13303,95.4499995 116.13303,93.5500005 117.021508,92.0060969 L120.352233,86.2183156 C121.244473,84.6678753 122.897032,83.7122186 124.685874,83.7122186 L131.319186,83.7122186 Z M128.003794,90.1848875 C126.459294,90.1848875 125.034382,91.0072828 124.263005,92.3424437 C123.491732,93.6774232 123.491732,95.3225768 124.263005,96.6575563 C125.034382,97.9927172 126.459294,98.8151125 128.001266,98.8151125 L128.001266,98.8151125 C129.545766,98.8151125 130.970678,97.9927172 131.742055,96.6575563 C132.513327,95.3225768 132.513327,93.6774232 131.742055,92.3424437 C130.970678,91.0072828 129.545766,90.1848875 128.003794,90.1848875 L128.003794,90.1848875 Z M93,94.5009646 L100.767764,94.5009646",
    fill: "#FFD94C"
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_1__.createElement("path", {
    d: "M87.8601729,108.357758 C89.1715224,107.608286 90.8360246,108.074601 91.5779424,109.399303 L91.5779424,109.399303 L92.0525843,110.24352 C95.8563392,116.982993 99.8190116,123.380176 103.940602,129.435068 C108.807881,136.585427 114.28184,143.82411 120.362479,151.151115 C121.316878,152.30114 121.184944,154.011176 120.065686,154.997937 L120.065686,154.997937 L119.454208,155.534625 C99.3465389,173.103314 86.2778188,176.612552 80.2480482,166.062341 C74.3500652,155.742717 76.4844915,136.982888 86.6513274,109.782853 C86.876818,109.179582 87.3045861,108.675291 87.8601729,108.357758 Z M173.534177,129.041504 C174.986131,128.785177 176.375496,129.742138 176.65963,131.194242 L176.65963,131.194242 L176.812815,131.986376 C181.782365,157.995459 178.283348,171 166.315764,171 C154.609745,171 139.708724,159.909007 121.612702,137.727022 C121.211349,137.235047 120.994572,136.617371 121,135.981509 C121.013158,134.480686 122.235785,133.274651 123.730918,133.287756 L123.730918,133.287756 L124.684654,133.294531 C132.305698,133.335994 139.714387,133.071591 146.910723,132.501323 C155.409039,131.82788 164.283523,130.674607 173.534177,129.041504 Z M180.408726,73.8119663 C180.932139,72.4026903 182.508386,71.6634537 183.954581,72.149012 L183.954581,72.149012 L184.742552,72.4154854 C210.583763,81.217922 220.402356,90.8916805 214.198332,101.436761 C208.129904,111.751366 190.484347,119.260339 161.26166,123.963678 C160.613529,124.067994 159.948643,123.945969 159.382735,123.618843 C158.047025,122.846729 157.602046,121.158214 158.388848,119.847438 L158.388848,119.847438 L158.889328,119.0105 C162.877183,112.31633 166.481358,105.654262 169.701854,99.0242957 C173.50501,91.1948179 177.073967,82.7907081 180.408726,73.8119663 Z M94.7383398,66.0363218 C95.3864708,65.9320063 96.0513565,66.0540315 96.6172646,66.3811573 C97.9529754,67.153271 98.3979538,68.8417862 97.6111517,70.1525615 L97.6111517,70.1525615 L97.1106718,70.9895001 C93.1228168,77.6836699 89.5186416,84.3457379 86.2981462,90.9757043 C82.49499,98.8051821 78.9260328,107.209292 75.5912744,116.188034 C75.0678608,117.59731 73.4916142,118.336546 72.045419,117.850988 L72.045419,117.850988 L71.2574475,117.584515 C45.4162372,108.782078 35.597644,99.1083195 41.8016679,88.5632391 C47.8700957,78.2486335 65.515653,70.7396611 94.7383398,66.0363218 Z M136.545792,34.4653746 C156.653461,16.8966864 169.722181,13.3874478 175.751952,23.9376587 C181.649935,34.2572826 179.515508,53.0171122 169.348673,80.2171474 C169.123182,80.8204179 168.695414,81.324709 168.139827,81.6422422 C166.828478,82.3917144 165.163975,81.9253986 164.422058,80.6006966 L164.422058,80.6006966 L163.947416,79.7564798 C160.143661,73.0170065 156.180988,66.6198239 152.059398,60.564932 C147.192119,53.4145727 141.71816,46.1758903 135.637521,38.8488847 C134.683122,37.6988602 134.815056,35.9888243 135.934314,35.0020629 L135.934314,35.0020629 Z M90.6842361,18 C102.390255,18 117.291276,29.0909926 135.387298,51.2729777 C135.788651,51.7649527 136.005428,52.3826288 136,53.0184911 C135.986842,54.5193144 134.764215,55.7253489 133.269082,55.7122445 L133.269082,55.7122445 L132.315346,55.7054689 C124.694302,55.6640063 117.285613,55.9284091 110.089277,56.4986773 C101.590961,57.17212 92.7164767,58.325393 83.4658235,59.9584962 C82.0138691,60.2148231 80.6245044,59.2578618 80.3403697,57.805758 L80.3403697,57.805758 L80.1871846,57.0136235 C75.2176347,31.0045412 78.7166519,18 90.6842361,18 Z",
    fill: "#FF4154"
  }))));
}

/***/ }),

/***/ "../node_modules/react-query/es/devtools/devtools.js":
/*!***********************************************************!*\
  !*** ../node_modules/react-query/es/devtools/devtools.js ***!
  \***********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ReactQueryDevtools: () => (/* binding */ ReactQueryDevtools),
/* harmony export */   ReactQueryDevtoolsPanel: () => (/* binding */ ReactQueryDevtoolsPanel)
/* harmony export */ });
/* harmony import */ var _babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/extends */ "../node_modules/@babel/runtime/helpers/esm/extends.js");
/* harmony import */ var _babel_runtime_helpers_esm_objectWithoutPropertiesLoose__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectWithoutPropertiesLoose */ "../node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var react_query__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! react-query */ "../node_modules/react-query/es/index.js");
/* harmony import */ var match_sorter__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! match-sorter */ "../node_modules/match-sorter/dist/match-sorter.esm.js");
/* harmony import */ var _useLocalStorage__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./useLocalStorage */ "../node_modules/react-query/es/devtools/useLocalStorage.js");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./utils */ "../node_modules/react-query/es/devtools/utils.js");
/* harmony import */ var _styledComponents__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./styledComponents */ "../node_modules/react-query/es/devtools/styledComponents.js");
/* harmony import */ var _theme__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./theme */ "../node_modules/react-query/es/devtools/theme.js");
/* harmony import */ var _Explorer__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./Explorer */ "../node_modules/react-query/es/devtools/Explorer.js");
/* harmony import */ var _Logo__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./Logo */ "../node_modules/react-query/es/devtools/Logo.js");
/* harmony import */ var _core_utils__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../core/utils */ "../node_modules/react-query/es/core/utils.js");













var isServer = typeof window === 'undefined';
function ReactQueryDevtools(_ref) {
  var initialIsOpen = _ref.initialIsOpen,
      _ref$panelProps = _ref.panelProps,
      panelProps = _ref$panelProps === void 0 ? {} : _ref$panelProps,
      _ref$closeButtonProps = _ref.closeButtonProps,
      closeButtonProps = _ref$closeButtonProps === void 0 ? {} : _ref$closeButtonProps,
      _ref$toggleButtonProp = _ref.toggleButtonProps,
      toggleButtonProps = _ref$toggleButtonProp === void 0 ? {} : _ref$toggleButtonProp,
      _ref$position = _ref.position,
      position = _ref$position === void 0 ? 'bottom-left' : _ref$position,
      _ref$containerElement = _ref.containerElement,
      Container = _ref$containerElement === void 0 ? 'aside' : _ref$containerElement,
      styleNonce = _ref.styleNonce;
  var rootRef = react__WEBPACK_IMPORTED_MODULE_2___default().useRef(null);
  var panelRef = react__WEBPACK_IMPORTED_MODULE_2___default().useRef(null);

  var _useLocalStorage = (0,_useLocalStorage__WEBPACK_IMPORTED_MODULE_5__["default"])('reactQueryDevtoolsOpen', initialIsOpen),
      isOpen = _useLocalStorage[0],
      setIsOpen = _useLocalStorage[1];

  var _useLocalStorage2 = (0,_useLocalStorage__WEBPACK_IMPORTED_MODULE_5__["default"])('reactQueryDevtoolsHeight', null),
      devtoolsHeight = _useLocalStorage2[0],
      setDevtoolsHeight = _useLocalStorage2[1];

  var _useSafeState = (0,_utils__WEBPACK_IMPORTED_MODULE_6__.useSafeState)(false),
      isResolvedOpen = _useSafeState[0],
      setIsResolvedOpen = _useSafeState[1];

  var _useSafeState2 = (0,_utils__WEBPACK_IMPORTED_MODULE_6__.useSafeState)(false),
      isResizing = _useSafeState2[0],
      setIsResizing = _useSafeState2[1];

  var isMounted = (0,_utils__WEBPACK_IMPORTED_MODULE_6__.useIsMounted)();

  var _handleDragStart = function handleDragStart(panelElement, startEvent) {
    var _panelElement$getBoun;

    if (startEvent.button !== 0) return; // Only allow left click for drag

    setIsResizing(true);
    var dragInfo = {
      originalHeight: (_panelElement$getBoun = panelElement == null ? void 0 : panelElement.getBoundingClientRect().height) != null ? _panelElement$getBoun : 0,
      pageY: startEvent.pageY
    };

    var run = function run(moveEvent) {
      var delta = dragInfo.pageY - moveEvent.pageY;
      var newHeight = (dragInfo == null ? void 0 : dragInfo.originalHeight) + delta;
      setDevtoolsHeight(newHeight);

      if (newHeight < 70) {
        setIsOpen(false);
      } else {
        setIsOpen(true);
      }
    };

    var unsub = function unsub() {
      setIsResizing(false);
      document.removeEventListener('mousemove', run);
      document.removeEventListener('mouseUp', unsub);
    };

    document.addEventListener('mousemove', run);
    document.addEventListener('mouseup', unsub);
  };

  react__WEBPACK_IMPORTED_MODULE_2___default().useEffect(function () {
    setIsResolvedOpen(isOpen != null ? isOpen : false);
  }, [isOpen, isResolvedOpen, setIsResolvedOpen]); // Toggle panel visibility before/after transition (depending on direction).
  // Prevents focusing in a closed panel.

  react__WEBPACK_IMPORTED_MODULE_2___default().useEffect(function () {
    var ref = panelRef.current;

    if (ref) {
      var handlePanelTransitionStart = function handlePanelTransitionStart() {
        if (ref && isResolvedOpen) {
          ref.style.visibility = 'visible';
        }
      };

      var handlePanelTransitionEnd = function handlePanelTransitionEnd() {
        if (ref && !isResolvedOpen) {
          ref.style.visibility = 'hidden';
        }
      };

      ref.addEventListener('transitionstart', handlePanelTransitionStart);
      ref.addEventListener('transitionend', handlePanelTransitionEnd);
      return function () {
        ref.removeEventListener('transitionstart', handlePanelTransitionStart);
        ref.removeEventListener('transitionend', handlePanelTransitionEnd);
      };
    }
  }, [isResolvedOpen]);
  (react__WEBPACK_IMPORTED_MODULE_2___default())[isServer ? 'useEffect' : 'useLayoutEffect'](function () {
    if (isResolvedOpen) {
      var _rootRef$current, _rootRef$current$pare;

      var previousValue = (_rootRef$current = rootRef.current) == null ? void 0 : (_rootRef$current$pare = _rootRef$current.parentElement) == null ? void 0 : _rootRef$current$pare.style.paddingBottom;

      var run = function run() {
        var _panelRef$current, _rootRef$current2;

        var containerHeight = (_panelRef$current = panelRef.current) == null ? void 0 : _panelRef$current.getBoundingClientRect().height;

        if ((_rootRef$current2 = rootRef.current) == null ? void 0 : _rootRef$current2.parentElement) {
          rootRef.current.parentElement.style.paddingBottom = containerHeight + "px";
        }
      };

      run();

      if (typeof window !== 'undefined') {
        window.addEventListener('resize', run);
        return function () {
          var _rootRef$current3;

          window.removeEventListener('resize', run);

          if (((_rootRef$current3 = rootRef.current) == null ? void 0 : _rootRef$current3.parentElement) && typeof previousValue === 'string') {
            rootRef.current.parentElement.style.paddingBottom = previousValue;
          }
        };
      }
    }
  }, [isResolvedOpen]);

  var _panelProps$style = panelProps.style,
      panelStyle = _panelProps$style === void 0 ? {} : _panelProps$style,
      otherPanelProps = (0,_babel_runtime_helpers_esm_objectWithoutPropertiesLoose__WEBPACK_IMPORTED_MODULE_1__["default"])(panelProps, ["style"]);

  var _closeButtonProps$sty = closeButtonProps.style,
      closeButtonStyle = _closeButtonProps$sty === void 0 ? {} : _closeButtonProps$sty,
      onCloseClick = closeButtonProps.onClick,
      otherCloseButtonProps = (0,_babel_runtime_helpers_esm_objectWithoutPropertiesLoose__WEBPACK_IMPORTED_MODULE_1__["default"])(closeButtonProps, ["style", "onClick"]);

  var _toggleButtonProps$st = toggleButtonProps.style,
      toggleButtonStyle = _toggleButtonProps$st === void 0 ? {} : _toggleButtonProps$st,
      onToggleClick = toggleButtonProps.onClick,
      otherToggleButtonProps = (0,_babel_runtime_helpers_esm_objectWithoutPropertiesLoose__WEBPACK_IMPORTED_MODULE_1__["default"])(toggleButtonProps, ["style", "onClick"]); // Do not render on the server


  if (!isMounted()) return null;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(Container, {
    ref: rootRef,
    className: "ReactQueryDevtools",
    "aria-label": "React Query Devtools"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(_theme__WEBPACK_IMPORTED_MODULE_7__.ThemeProvider, {
    theme: _theme__WEBPACK_IMPORTED_MODULE_7__.defaultTheme
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(ReactQueryDevtoolsPanel, (0,_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__["default"])({
    ref: panelRef,
    styleNonce: styleNonce
  }, otherPanelProps, {
    style: (0,_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__["default"])({
      position: 'fixed',
      bottom: '0',
      right: '0',
      zIndex: 99999,
      width: '100%',
      height: devtoolsHeight != null ? devtoolsHeight : 500,
      maxHeight: '90%',
      boxShadow: '0 0 20px rgba(0,0,0,.3)',
      borderTop: "1px solid " + _theme__WEBPACK_IMPORTED_MODULE_7__.defaultTheme.gray,
      transformOrigin: 'top',
      // visibility will be toggled after transitions, but set initial state here
      visibility: isOpen ? 'visible' : 'hidden'
    }, panelStyle, isResizing ? {
      transition: "none"
    } : {
      transition: "all .2s ease"
    }, isResolvedOpen ? {
      opacity: 1,
      pointerEvents: 'all',
      transform: "translateY(0) scale(1)"
    } : {
      opacity: 0,
      pointerEvents: 'none',
      transform: "translateY(15px) scale(1.02)"
    }),
    isOpen: isResolvedOpen,
    setIsOpen: setIsOpen,
    handleDragStart: function handleDragStart(e) {
      return _handleDragStart(panelRef.current, e);
    }
  })), isResolvedOpen ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(_styledComponents__WEBPACK_IMPORTED_MODULE_8__.Button, (0,_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__["default"])({
    type: "button",
    "aria-controls": "ReactQueryDevtoolsPanel",
    "aria-haspopup": "true",
    "aria-expanded": "true"
  }, otherCloseButtonProps, {
    onClick: function onClick(e) {
      setIsOpen(false);
      onCloseClick && onCloseClick(e);
    },
    style: (0,_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__["default"])({
      position: 'fixed',
      zIndex: 99999,
      margin: '.5em',
      bottom: 0
    }, position === 'top-right' ? {
      right: '0'
    } : position === 'top-left' ? {
      left: '0'
    } : position === 'bottom-right' ? {
      right: '0'
    } : {
      left: '0'
    }, closeButtonStyle)
  }), "Close") : null), !isResolvedOpen ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement("button", (0,_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__["default"])({
    type: "button"
  }, otherToggleButtonProps, {
    "aria-label": "Open React Query Devtools",
    "aria-controls": "ReactQueryDevtoolsPanel",
    "aria-haspopup": "true",
    "aria-expanded": "false",
    onClick: function onClick(e) {
      setIsOpen(true);
      onToggleClick && onToggleClick(e);
    },
    style: (0,_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__["default"])({
      background: 'none',
      border: 0,
      padding: 0,
      position: 'fixed',
      zIndex: 99999,
      display: 'inline-flex',
      fontSize: '1.5em',
      margin: '.5em',
      cursor: 'pointer',
      width: 'fit-content'
    }, position === 'top-right' ? {
      top: '0',
      right: '0'
    } : position === 'top-left' ? {
      top: '0',
      left: '0'
    } : position === 'bottom-right' ? {
      bottom: '0',
      right: '0'
    } : {
      bottom: '0',
      left: '0'
    }, toggleButtonStyle)
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(_Logo__WEBPACK_IMPORTED_MODULE_9__["default"], {
    "aria-hidden": true
  })) : null);
}

var getStatusRank = function getStatusRank(q) {
  return q.state.isFetching ? 0 : !q.getObserversCount() ? 3 : q.isStale() ? 2 : 1;
};

var sortFns = {
  'Status > Last Updated': function StatusLastUpdated(a, b) {
    var _sortFns$LastUpdated;

    return getStatusRank(a) === getStatusRank(b) ? (_sortFns$LastUpdated = sortFns['Last Updated']) == null ? void 0 : _sortFns$LastUpdated.call(sortFns, a, b) : getStatusRank(a) > getStatusRank(b) ? 1 : -1;
  },
  'Query Hash': function QueryHash(a, b) {
    return a.queryHash > b.queryHash ? 1 : -1;
  },
  'Last Updated': function LastUpdated(a, b) {
    return a.state.dataUpdatedAt < b.state.dataUpdatedAt ? 1 : -1;
  }
};
var ReactQueryDevtoolsPanel = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().forwardRef(function ReactQueryDevtoolsPanel(props, ref) {
  var _activeQuery$state;

  var _props$isOpen = props.isOpen,
      isOpen = _props$isOpen === void 0 ? true : _props$isOpen,
      styleNonce = props.styleNonce,
      setIsOpen = props.setIsOpen,
      handleDragStart = props.handleDragStart,
      panelProps = (0,_babel_runtime_helpers_esm_objectWithoutPropertiesLoose__WEBPACK_IMPORTED_MODULE_1__["default"])(props, ["isOpen", "styleNonce", "setIsOpen", "handleDragStart"]);

  var queryClient = (0,react_query__WEBPACK_IMPORTED_MODULE_3__.useQueryClient)();
  var queryCache = queryClient.getQueryCache();

  var _useLocalStorage3 = (0,_useLocalStorage__WEBPACK_IMPORTED_MODULE_5__["default"])('reactQueryDevtoolsSortFn', Object.keys(sortFns)[0]),
      sort = _useLocalStorage3[0],
      setSort = _useLocalStorage3[1];

  var _useLocalStorage4 = (0,_useLocalStorage__WEBPACK_IMPORTED_MODULE_5__["default"])('reactQueryDevtoolsFilter', ''),
      filter = _useLocalStorage4[0],
      setFilter = _useLocalStorage4[1];

  var _useLocalStorage5 = (0,_useLocalStorage__WEBPACK_IMPORTED_MODULE_5__["default"])('reactQueryDevtoolsSortDesc', false),
      sortDesc = _useLocalStorage5[0],
      setSortDesc = _useLocalStorage5[1];

  var sortFn = react__WEBPACK_IMPORTED_MODULE_2___default().useMemo(function () {
    return sortFns[sort];
  }, [sort]);
  (react__WEBPACK_IMPORTED_MODULE_2___default())[isServer ? 'useEffect' : 'useLayoutEffect'](function () {
    if (!sortFn) {
      setSort(Object.keys(sortFns)[0]);
    }
  }, [setSort, sortFn]);

  var _useSafeState3 = (0,_utils__WEBPACK_IMPORTED_MODULE_6__.useSafeState)(Object.values(queryCache.findAll())),
      unsortedQueries = _useSafeState3[0],
      setUnsortedQueries = _useSafeState3[1];

  var _useLocalStorage6 = (0,_useLocalStorage__WEBPACK_IMPORTED_MODULE_5__["default"])('reactQueryDevtoolsActiveQueryHash', ''),
      activeQueryHash = _useLocalStorage6[0],
      setActiveQueryHash = _useLocalStorage6[1];

  var queries = react__WEBPACK_IMPORTED_MODULE_2___default().useMemo(function () {
    var sorted = [].concat(unsortedQueries).sort(sortFn);

    if (sortDesc) {
      sorted.reverse();
    }

    if (!filter) {
      return sorted;
    }

    return (0,match_sorter__WEBPACK_IMPORTED_MODULE_4__.matchSorter)(sorted, filter, {
      keys: ['queryHash']
    }).filter(function (d) {
      return d.queryHash;
    });
  }, [sortDesc, sortFn, unsortedQueries, filter]);
  var activeQuery = react__WEBPACK_IMPORTED_MODULE_2___default().useMemo(function () {
    return queries.find(function (query) {
      return query.queryHash === activeQueryHash;
    });
  }, [activeQueryHash, queries]);
  var hasFresh = queries.filter(function (q) {
    return (0,_utils__WEBPACK_IMPORTED_MODULE_6__.getQueryStatusLabel)(q) === 'fresh';
  }).length;
  var hasFetching = queries.filter(function (q) {
    return (0,_utils__WEBPACK_IMPORTED_MODULE_6__.getQueryStatusLabel)(q) === 'fetching';
  }).length;
  var hasStale = queries.filter(function (q) {
    return (0,_utils__WEBPACK_IMPORTED_MODULE_6__.getQueryStatusLabel)(q) === 'stale';
  }).length;
  var hasInactive = queries.filter(function (q) {
    return (0,_utils__WEBPACK_IMPORTED_MODULE_6__.getQueryStatusLabel)(q) === 'inactive';
  }).length;
  react__WEBPACK_IMPORTED_MODULE_2___default().useEffect(function () {
    if (isOpen) {
      var unsubscribe = queryCache.subscribe(function () {
        setUnsortedQueries(Object.values(queryCache.getAll()));
      }); // re-subscribing after the panel is closed and re-opened won't trigger the callback,
      // So we'll manually populate our state

      setUnsortedQueries(Object.values(queryCache.getAll()));
      return unsubscribe;
    }

    return undefined;
  }, [isOpen, sort, sortFn, sortDesc, setUnsortedQueries, queryCache]);

  var handleRefetch = function handleRefetch() {
    var promise = activeQuery == null ? void 0 : activeQuery.fetch();
    promise == null ? void 0 : promise.catch(_core_utils__WEBPACK_IMPORTED_MODULE_10__.noop);
  };

  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(_theme__WEBPACK_IMPORTED_MODULE_7__.ThemeProvider, {
    theme: _theme__WEBPACK_IMPORTED_MODULE_7__.defaultTheme
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(_styledComponents__WEBPACK_IMPORTED_MODULE_8__.Panel, (0,_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__["default"])({
    ref: ref,
    className: "ReactQueryDevtoolsPanel",
    "aria-label": "React Query Devtools Panel",
    id: "ReactQueryDevtoolsPanel"
  }, panelProps), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement("style", {
    nonce: styleNonce,
    dangerouslySetInnerHTML: {
      __html: "\n            .ReactQueryDevtoolsPanel * {\n              scrollbar-color: " + _theme__WEBPACK_IMPORTED_MODULE_7__.defaultTheme.backgroundAlt + " " + _theme__WEBPACK_IMPORTED_MODULE_7__.defaultTheme.gray + ";\n            }\n\n            .ReactQueryDevtoolsPanel *::-webkit-scrollbar, .ReactQueryDevtoolsPanel scrollbar {\n              width: 1em;\n              height: 1em;\n            }\n\n            .ReactQueryDevtoolsPanel *::-webkit-scrollbar-track, .ReactQueryDevtoolsPanel scrollbar-track {\n              background: " + _theme__WEBPACK_IMPORTED_MODULE_7__.defaultTheme.backgroundAlt + ";\n            }\n\n            .ReactQueryDevtoolsPanel *::-webkit-scrollbar-thumb, .ReactQueryDevtoolsPanel scrollbar-thumb {\n              background: " + _theme__WEBPACK_IMPORTED_MODULE_7__.defaultTheme.gray + ";\n              border-radius: .5em;\n              border: 3px solid " + _theme__WEBPACK_IMPORTED_MODULE_7__.defaultTheme.backgroundAlt + ";\n            }\n          "
    }
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement("div", {
    style: {
      position: 'absolute',
      left: 0,
      top: 0,
      width: '100%',
      height: '4px',
      marginBottom: '-4px',
      cursor: 'row-resize',
      zIndex: 100000
    },
    onMouseDown: handleDragStart
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement("div", {
    style: {
      flex: '1 1 500px',
      minHeight: '40%',
      maxHeight: '100%',
      overflow: 'auto',
      borderRight: "1px solid " + _theme__WEBPACK_IMPORTED_MODULE_7__.defaultTheme.grayAlt,
      display: isOpen ? 'flex' : 'none',
      flexDirection: 'column'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement("div", {
    style: {
      padding: '.5em',
      background: _theme__WEBPACK_IMPORTED_MODULE_7__.defaultTheme.backgroundAlt,
      display: 'flex',
      justifyContent: 'space-between',
      alignItems: 'center'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement("button", {
    type: "button",
    "aria-label": "Close React Query Devtools",
    "aria-controls": "ReactQueryDevtoolsPanel",
    "aria-haspopup": "true",
    "aria-expanded": "true",
    onClick: function onClick() {
      return setIsOpen(false);
    },
    style: {
      display: 'inline-flex',
      background: 'none',
      border: 0,
      padding: 0,
      marginRight: '.5em',
      cursor: 'pointer'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(_Logo__WEBPACK_IMPORTED_MODULE_9__["default"], {
    "aria-hidden": true
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement("div", {
    style: {
      display: 'flex',
      flexDirection: 'column'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(_styledComponents__WEBPACK_IMPORTED_MODULE_8__.QueryKeys, {
    style: {
      marginBottom: '.5em'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(_styledComponents__WEBPACK_IMPORTED_MODULE_8__.QueryKey, {
    style: {
      background: _theme__WEBPACK_IMPORTED_MODULE_7__.defaultTheme.success,
      opacity: hasFresh ? 1 : 0.3
    }
  }, "fresh ", /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(_styledComponents__WEBPACK_IMPORTED_MODULE_8__.Code, null, "(", hasFresh, ")")), ' ', /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(_styledComponents__WEBPACK_IMPORTED_MODULE_8__.QueryKey, {
    style: {
      background: _theme__WEBPACK_IMPORTED_MODULE_7__.defaultTheme.active,
      opacity: hasFetching ? 1 : 0.3
    }
  }, "fetching ", /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(_styledComponents__WEBPACK_IMPORTED_MODULE_8__.Code, null, "(", hasFetching, ")")), ' ', /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(_styledComponents__WEBPACK_IMPORTED_MODULE_8__.QueryKey, {
    style: {
      background: _theme__WEBPACK_IMPORTED_MODULE_7__.defaultTheme.warning,
      color: 'black',
      textShadow: '0',
      opacity: hasStale ? 1 : 0.3
    }
  }, "stale ", /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(_styledComponents__WEBPACK_IMPORTED_MODULE_8__.Code, null, "(", hasStale, ")")), ' ', /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(_styledComponents__WEBPACK_IMPORTED_MODULE_8__.QueryKey, {
    style: {
      background: _theme__WEBPACK_IMPORTED_MODULE_7__.defaultTheme.gray,
      opacity: hasInactive ? 1 : 0.3
    }
  }, "inactive ", /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(_styledComponents__WEBPACK_IMPORTED_MODULE_8__.Code, null, "(", hasInactive, ")"))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement("div", {
    style: {
      display: 'flex',
      alignItems: 'center'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(_styledComponents__WEBPACK_IMPORTED_MODULE_8__.Input, {
    placeholder: "Filter",
    "aria-label": "Filter by queryhash",
    value: filter != null ? filter : '',
    onChange: function onChange(e) {
      return setFilter(e.target.value);
    },
    onKeyDown: function onKeyDown(e) {
      if (e.key === 'Escape') setFilter('');
    },
    style: {
      flex: '1',
      marginRight: '.5em',
      width: '100%'
    }
  }), !filter ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement((react__WEBPACK_IMPORTED_MODULE_2___default().Fragment), null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(_styledComponents__WEBPACK_IMPORTED_MODULE_8__.Select, {
    "aria-label": "Sort queries",
    value: sort,
    onChange: function onChange(e) {
      return setSort(e.target.value);
    },
    style: {
      flex: '1',
      minWidth: 75,
      marginRight: '.5em'
    }
  }, Object.keys(sortFns).map(function (key) {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement("option", {
      key: key,
      value: key
    }, "Sort by ", key);
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(_styledComponents__WEBPACK_IMPORTED_MODULE_8__.Button, {
    type: "button",
    onClick: function onClick() {
      return setSortDesc(function (old) {
        return !old;
      });
    },
    style: {
      padding: '.3em .4em'
    }
  }, sortDesc ? '⬇ Desc' : '⬆ Asc')) : null))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement("div", {
    style: {
      overflowY: 'auto',
      flex: '1'
    }
  }, queries.map(function (query, i) {
    var isDisabled = query.getObserversCount() > 0 && !query.isActive();
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement("div", {
      key: query.queryHash || i,
      role: "button",
      "aria-label": "Open query details for " + query.queryHash,
      onClick: function onClick() {
        return setActiveQueryHash(activeQueryHash === query.queryHash ? '' : query.queryHash);
      },
      style: {
        display: 'flex',
        borderBottom: "solid 1px " + _theme__WEBPACK_IMPORTED_MODULE_7__.defaultTheme.grayAlt,
        cursor: 'pointer',
        background: query === activeQuery ? 'rgba(255,255,255,.1)' : undefined
      }
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement("div", {
      style: {
        flex: '0 0 auto',
        width: '2em',
        height: '2em',
        background: (0,_utils__WEBPACK_IMPORTED_MODULE_6__.getQueryStatusColor)(query, _theme__WEBPACK_IMPORTED_MODULE_7__.defaultTheme),
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        fontWeight: 'bold',
        textShadow: (0,_utils__WEBPACK_IMPORTED_MODULE_6__.getQueryStatusLabel)(query) === 'stale' ? '0' : '0 0 10px black',
        color: (0,_utils__WEBPACK_IMPORTED_MODULE_6__.getQueryStatusLabel)(query) === 'stale' ? 'black' : 'white'
      }
    }, query.getObserversCount()), isDisabled ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement("div", {
      style: {
        flex: '0 0 auto',
        height: '2em',
        background: _theme__WEBPACK_IMPORTED_MODULE_7__.defaultTheme.gray,
        display: 'flex',
        alignItems: 'center',
        fontWeight: 'bold',
        padding: '0 0.5em'
      }
    }, "disabled") : null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(_styledComponents__WEBPACK_IMPORTED_MODULE_8__.Code, {
      style: {
        padding: '.5em'
      }
    }, "" + query.queryHash));
  }))), activeQuery ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(_styledComponents__WEBPACK_IMPORTED_MODULE_8__.ActiveQueryPanel, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement("div", {
    style: {
      padding: '.5em',
      background: _theme__WEBPACK_IMPORTED_MODULE_7__.defaultTheme.backgroundAlt,
      position: 'sticky',
      top: 0,
      zIndex: 1
    }
  }, "Query Details"), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement("div", {
    style: {
      padding: '.5em'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement("div", {
    style: {
      marginBottom: '.5em',
      display: 'flex',
      alignItems: 'start',
      justifyContent: 'space-between'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(_styledComponents__WEBPACK_IMPORTED_MODULE_8__.Code, {
    style: {
      lineHeight: '1.8em'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement("pre", {
    style: {
      margin: 0,
      padding: 0,
      overflow: 'auto'
    }
  }, JSON.stringify(activeQuery.queryKey, null, 2))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement("span", {
    style: {
      padding: '0.3em .6em',
      borderRadius: '0.4em',
      fontWeight: 'bold',
      textShadow: '0 2px 10px black',
      background: (0,_utils__WEBPACK_IMPORTED_MODULE_6__.getQueryStatusColor)(activeQuery, _theme__WEBPACK_IMPORTED_MODULE_7__.defaultTheme),
      flexShrink: 0
    }
  }, (0,_utils__WEBPACK_IMPORTED_MODULE_6__.getQueryStatusLabel)(activeQuery))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement("div", {
    style: {
      marginBottom: '.5em',
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'space-between'
    }
  }, "Observers: ", /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(_styledComponents__WEBPACK_IMPORTED_MODULE_8__.Code, null, activeQuery.getObserversCount())), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement("div", {
    style: {
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'space-between'
    }
  }, "Last Updated:", ' ', /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(_styledComponents__WEBPACK_IMPORTED_MODULE_8__.Code, null, new Date(activeQuery.state.dataUpdatedAt).toLocaleTimeString()))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement("div", {
    style: {
      background: _theme__WEBPACK_IMPORTED_MODULE_7__.defaultTheme.backgroundAlt,
      padding: '.5em',
      position: 'sticky',
      top: 0,
      zIndex: 1
    }
  }, "Actions"), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement("div", {
    style: {
      padding: '0.5em'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(_styledComponents__WEBPACK_IMPORTED_MODULE_8__.Button, {
    type: "button",
    onClick: handleRefetch,
    disabled: activeQuery.state.isFetching,
    style: {
      background: _theme__WEBPACK_IMPORTED_MODULE_7__.defaultTheme.active
    }
  }, "Refetch"), ' ', /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(_styledComponents__WEBPACK_IMPORTED_MODULE_8__.Button, {
    type: "button",
    onClick: function onClick() {
      return queryClient.invalidateQueries(activeQuery);
    },
    style: {
      background: _theme__WEBPACK_IMPORTED_MODULE_7__.defaultTheme.warning,
      color: _theme__WEBPACK_IMPORTED_MODULE_7__.defaultTheme.inputTextColor
    }
  }, "Invalidate"), ' ', /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(_styledComponents__WEBPACK_IMPORTED_MODULE_8__.Button, {
    type: "button",
    onClick: function onClick() {
      return queryClient.resetQueries(activeQuery);
    },
    style: {
      background: _theme__WEBPACK_IMPORTED_MODULE_7__.defaultTheme.gray
    }
  }, "Reset"), ' ', /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(_styledComponents__WEBPACK_IMPORTED_MODULE_8__.Button, {
    type: "button",
    onClick: function onClick() {
      return queryClient.removeQueries(activeQuery);
    },
    style: {
      background: _theme__WEBPACK_IMPORTED_MODULE_7__.defaultTheme.danger
    }
  }, "Remove")), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement("div", {
    style: {
      background: _theme__WEBPACK_IMPORTED_MODULE_7__.defaultTheme.backgroundAlt,
      padding: '.5em',
      position: 'sticky',
      top: 0,
      zIndex: 1
    }
  }, "Data Explorer"), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement("div", {
    style: {
      padding: '.5em'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(_Explorer__WEBPACK_IMPORTED_MODULE_11__["default"], {
    label: "Data",
    value: activeQuery == null ? void 0 : (_activeQuery$state = activeQuery.state) == null ? void 0 : _activeQuery$state.data,
    defaultExpanded: {}
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement("div", {
    style: {
      background: _theme__WEBPACK_IMPORTED_MODULE_7__.defaultTheme.backgroundAlt,
      padding: '.5em',
      position: 'sticky',
      top: 0,
      zIndex: 1
    }
  }, "Query Explorer"), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement("div", {
    style: {
      padding: '.5em'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(_Explorer__WEBPACK_IMPORTED_MODULE_11__["default"], {
    label: "Query",
    value: activeQuery,
    defaultExpanded: {
      queryKey: true
    }
  }))) : null));
});

/***/ }),

/***/ "../node_modules/react-query/es/devtools/index.js":
/*!********************************************************!*\
  !*** ../node_modules/react-query/es/devtools/index.js ***!
  \********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ReactQueryDevtools: () => (/* reexport safe */ _devtools__WEBPACK_IMPORTED_MODULE_0__.ReactQueryDevtools),
/* harmony export */   ReactQueryDevtoolsPanel: () => (/* reexport safe */ _devtools__WEBPACK_IMPORTED_MODULE_0__.ReactQueryDevtoolsPanel)
/* harmony export */ });
/* harmony import */ var _devtools__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./devtools */ "../node_modules/react-query/es/devtools/devtools.js");


/***/ }),

/***/ "../node_modules/react-query/es/devtools/styledComponents.js":
/*!*******************************************************************!*\
  !*** ../node_modules/react-query/es/devtools/styledComponents.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ActiveQueryPanel: () => (/* binding */ ActiveQueryPanel),
/* harmony export */   Button: () => (/* binding */ Button),
/* harmony export */   Code: () => (/* binding */ Code),
/* harmony export */   Input: () => (/* binding */ Input),
/* harmony export */   Panel: () => (/* binding */ Panel),
/* harmony export */   QueryKey: () => (/* binding */ QueryKey),
/* harmony export */   QueryKeys: () => (/* binding */ QueryKeys),
/* harmony export */   Select: () => (/* binding */ Select)
/* harmony export */ });
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./utils */ "../node_modules/react-query/es/devtools/utils.js");

var Panel = (0,_utils__WEBPACK_IMPORTED_MODULE_0__.styled)('div', function (_props, theme) {
  return {
    fontSize: 'clamp(12px, 1.5vw, 14px)',
    fontFamily: "sans-serif",
    display: 'flex',
    backgroundColor: theme.background,
    color: theme.foreground
  };
}, {
  '(max-width: 700px)': {
    flexDirection: 'column'
  },
  '(max-width: 600px)': {
    fontSize: '.9em' // flexDirection: 'column',

  }
});
var ActiveQueryPanel = (0,_utils__WEBPACK_IMPORTED_MODULE_0__.styled)('div', function () {
  return {
    flex: '1 1 500px',
    display: 'flex',
    flexDirection: 'column',
    overflow: 'auto',
    height: '100%'
  };
}, {
  '(max-width: 700px)': function maxWidth700px(_props, theme) {
    return {
      borderTop: "2px solid " + theme.gray
    };
  }
});
var Button = (0,_utils__WEBPACK_IMPORTED_MODULE_0__.styled)('button', function (props, theme) {
  return {
    appearance: 'none',
    fontSize: '.9em',
    fontWeight: 'bold',
    background: theme.gray,
    border: '0',
    borderRadius: '.3em',
    color: 'white',
    padding: '.5em',
    opacity: props.disabled ? '.5' : undefined,
    cursor: 'pointer'
  };
});
var QueryKeys = (0,_utils__WEBPACK_IMPORTED_MODULE_0__.styled)('span', {
  display: 'inline-block',
  fontSize: '0.9em'
});
var QueryKey = (0,_utils__WEBPACK_IMPORTED_MODULE_0__.styled)('span', {
  display: 'inline-flex',
  alignItems: 'center',
  padding: '.2em .4em',
  fontWeight: 'bold',
  textShadow: '0 0 10px black',
  borderRadius: '.2em'
});
var Code = (0,_utils__WEBPACK_IMPORTED_MODULE_0__.styled)('code', {
  fontSize: '.9em',
  color: 'inherit',
  background: 'inherit'
});
var Input = (0,_utils__WEBPACK_IMPORTED_MODULE_0__.styled)('input', function (_props, theme) {
  return {
    backgroundColor: theme.inputBackgroundColor,
    border: 0,
    borderRadius: '.2em',
    color: theme.inputTextColor,
    fontSize: '.9em',
    lineHeight: "1.3",
    padding: '.3em .4em'
  };
});
var Select = (0,_utils__WEBPACK_IMPORTED_MODULE_0__.styled)('select', function (_props, theme) {
  return {
    display: "inline-block",
    fontSize: ".9em",
    fontFamily: "sans-serif",
    fontWeight: 'normal',
    lineHeight: "1.3",
    padding: ".3em 1.5em .3em .5em",
    height: 'auto',
    border: 0,
    borderRadius: ".2em",
    appearance: "none",
    WebkitAppearance: 'none',
    backgroundColor: theme.inputBackgroundColor,
    backgroundImage: "url(\"data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='100' height='100' fill='%23444444'><polygon points='0,25 100,25 50,75'/></svg>\")",
    backgroundRepeat: "no-repeat",
    backgroundPosition: "right .55em center",
    backgroundSize: ".65em auto, 100%",
    color: theme.inputTextColor
  };
}, {
  '(max-width: 500px)': {
    display: 'none'
  }
});

/***/ }),

/***/ "../node_modules/react-query/es/devtools/theme.js":
/*!********************************************************!*\
  !*** ../node_modules/react-query/es/devtools/theme.js ***!
  \********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ThemeProvider: () => (/* binding */ ThemeProvider),
/* harmony export */   defaultTheme: () => (/* binding */ defaultTheme),
/* harmony export */   useTheme: () => (/* binding */ useTheme)
/* harmony export */ });
/* harmony import */ var _babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/extends */ "../node_modules/@babel/runtime/helpers/esm/extends.js");
/* harmony import */ var _babel_runtime_helpers_esm_objectWithoutPropertiesLoose__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectWithoutPropertiesLoose */ "../node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_2__);



var defaultTheme = {
  background: '#0b1521',
  backgroundAlt: '#132337',
  foreground: 'white',
  gray: '#3f4e60',
  grayAlt: '#222e3e',
  inputBackgroundColor: '#fff',
  inputTextColor: '#000',
  success: '#00ab52',
  danger: '#ff0085',
  active: '#006bff',
  warning: '#ffb200'
};
var ThemeContext = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createContext(defaultTheme);
function ThemeProvider(_ref) {
  var theme = _ref.theme,
      rest = (0,_babel_runtime_helpers_esm_objectWithoutPropertiesLoose__WEBPACK_IMPORTED_MODULE_1__["default"])(_ref, ["theme"]);

  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(ThemeContext.Provider, (0,_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__["default"])({
    value: theme
  }, rest));
}
function useTheme() {
  return react__WEBPACK_IMPORTED_MODULE_2___default().useContext(ThemeContext);
}

/***/ }),

/***/ "../node_modules/react-query/es/devtools/useLocalStorage.js":
/*!******************************************************************!*\
  !*** ../node_modules/react-query/es/devtools/useLocalStorage.js ***!
  \******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ useLocalStorage)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);


var getItem = function getItem(key) {
  try {
    var itemValue = localStorage.getItem(key);

    if (typeof itemValue === 'string') {
      return JSON.parse(itemValue);
    }

    return undefined;
  } catch (_unused) {
    return undefined;
  }
};

function useLocalStorage(key, defaultValue) {
  var _React$useState = react__WEBPACK_IMPORTED_MODULE_0___default().useState(),
      value = _React$useState[0],
      setValue = _React$useState[1];

  react__WEBPACK_IMPORTED_MODULE_0___default().useEffect(function () {
    var initialValue = getItem(key);

    if (typeof initialValue === 'undefined' || initialValue === null) {
      setValue(typeof defaultValue === 'function' ? defaultValue() : defaultValue);
    } else {
      setValue(initialValue);
    }
  }, [defaultValue, key]);
  var setter = react__WEBPACK_IMPORTED_MODULE_0___default().useCallback(function (updater) {
    setValue(function (old) {
      var newVal = updater;

      if (typeof updater == 'function') {
        newVal = updater(old);
      }

      try {
        localStorage.setItem(key, JSON.stringify(newVal));
      } catch (_unused2) {}

      return newVal;
    });
  }, [key]);
  return [value, setter];
}

/***/ }),

/***/ "../node_modules/react-query/es/devtools/useMediaQuery.js":
/*!****************************************************************!*\
  !*** ../node_modules/react-query/es/devtools/useMediaQuery.js ***!
  \****************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ useMediaQuery)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

function useMediaQuery(query) {
  // Keep track of the preference in state, start with the current match
  var _React$useState = react__WEBPACK_IMPORTED_MODULE_0___default().useState(function () {
    if (typeof window !== 'undefined') {
      return window.matchMedia && window.matchMedia(query).matches;
    }
  }),
      isMatch = _React$useState[0],
      setIsMatch = _React$useState[1]; // Watch for changes


  react__WEBPACK_IMPORTED_MODULE_0___default().useEffect(function () {
    if (typeof window !== 'undefined') {
      if (!window.matchMedia) {
        return;
      } // Create a matcher


      var matcher = window.matchMedia(query); // Create our handler

      var onChange = function onChange(_ref) {
        var matches = _ref.matches;
        return setIsMatch(matches);
      }; // Listen for changes


      matcher.addListener(onChange);
      return function () {
        // Stop listening for changes
        matcher.removeListener(onChange);
      };
    }
  }, [isMatch, query, setIsMatch]);
  return isMatch;
}

/***/ }),

/***/ "../node_modules/react-query/es/devtools/utils.js":
/*!********************************************************!*\
  !*** ../node_modules/react-query/es/devtools/utils.js ***!
  \********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   displayValue: () => (/* binding */ displayValue),
/* harmony export */   getQueryStatusColor: () => (/* binding */ getQueryStatusColor),
/* harmony export */   getQueryStatusLabel: () => (/* binding */ getQueryStatusLabel),
/* harmony export */   isServer: () => (/* binding */ isServer),
/* harmony export */   styled: () => (/* binding */ styled),
/* harmony export */   useIsMounted: () => (/* binding */ useIsMounted),
/* harmony export */   useSafeState: () => (/* binding */ useSafeState)
/* harmony export */ });
/* harmony import */ var _babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/extends */ "../node_modules/@babel/runtime/helpers/esm/extends.js");
/* harmony import */ var _babel_runtime_helpers_esm_objectWithoutPropertiesLoose__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectWithoutPropertiesLoose */ "../node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _theme__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./theme */ "../node_modules/react-query/es/devtools/theme.js");
/* harmony import */ var _useMediaQuery__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./useMediaQuery */ "../node_modules/react-query/es/devtools/useMediaQuery.js");





var isServer = typeof window === 'undefined';
function getQueryStatusColor(query, theme) {
  return query.state.isFetching ? theme.active : !query.getObserversCount() ? theme.gray : query.isStale() ? theme.warning : theme.success;
}
function getQueryStatusLabel(query) {
  return query.state.isFetching ? 'fetching' : !query.getObserversCount() ? 'inactive' : query.isStale() ? 'stale' : 'fresh';
}
function styled(type, newStyles, queries) {
  if (queries === void 0) {
    queries = {};
  }

  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().forwardRef(function (_ref, ref) {
    var style = _ref.style,
        rest = (0,_babel_runtime_helpers_esm_objectWithoutPropertiesLoose__WEBPACK_IMPORTED_MODULE_1__["default"])(_ref, ["style"]);

    var theme = (0,_theme__WEBPACK_IMPORTED_MODULE_3__.useTheme)();
    var mediaStyles = Object.entries(queries).reduce(function (current, _ref2) {
      var key = _ref2[0],
          value = _ref2[1];
      // eslint-disable-next-line react-hooks/rules-of-hooks
      return (0,_useMediaQuery__WEBPACK_IMPORTED_MODULE_4__["default"])(key) ? (0,_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__["default"])({}, current, typeof value === 'function' ? value(rest, theme) : value) : current;
    }, {});
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_2___default().createElement(type, (0,_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__["default"])({}, rest, {
      style: (0,_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__["default"])({}, typeof newStyles === 'function' ? newStyles(rest, theme) : newStyles, style, mediaStyles),
      ref: ref
    }));
  });
}
function useIsMounted() {
  var mountedRef = react__WEBPACK_IMPORTED_MODULE_2___default().useRef(false);
  var isMounted = react__WEBPACK_IMPORTED_MODULE_2___default().useCallback(function () {
    return mountedRef.current;
  }, []);
  (react__WEBPACK_IMPORTED_MODULE_2___default())[isServer ? 'useEffect' : 'useLayoutEffect'](function () {
    mountedRef.current = true;
    return function () {
      mountedRef.current = false;
    };
  }, []);
  return isMounted;
}
/**
 * This hook is a safe useState version which schedules state updates in microtasks
 * to prevent updating a component state while React is rendering different components
 * or when the component is not mounted anymore.
 */

function useSafeState(initialState) {
  var isMounted = useIsMounted();

  var _React$useState = react__WEBPACK_IMPORTED_MODULE_2___default().useState(initialState),
      state = _React$useState[0],
      setState = _React$useState[1];

  var safeSetState = react__WEBPACK_IMPORTED_MODULE_2___default().useCallback(function (value) {
    scheduleMicrotask(function () {
      if (isMounted()) {
        setState(value);
      }
    });
  }, [isMounted]);
  return [state, safeSetState];
}
/**
 * Displays a string regardless the type of the data
 * @param {unknown} value Value to be stringified
 */

var displayValue = function displayValue(value) {
  var name = Object.getOwnPropertyNames(Object(value));
  var newValue = typeof value === 'bigint' ? value.toString() + "n" : value;
  return JSON.stringify(newValue, name);
};
/**
 * Schedules a microtask.
 * This can be useful to schedule state updates after rendering.
 */

function scheduleMicrotask(callback) {
  Promise.resolve().then(callback).catch(function (error) {
    return setTimeout(function () {
      throw error;
    });
  });
}

/***/ })

}]);
//# sourceMappingURL=bb8b6cce5ae5b36077e0.bundle.js.map