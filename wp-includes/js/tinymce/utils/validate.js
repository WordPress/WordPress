/**
 * $RCSfile: validate.js,v $
 * $Revision: 1.3 $
 * $Date: 2006/02/06 20:11:09 $
 *
 * Various form validation methods.
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2006, Moxiecode Systems AB, All rights reserved.
 */

function testRegExp(form_name, element_name, re) {
	return new RegExp(re).test(document.forms[form_name].elements[element_name].value);
}

function validateString(form_name, element_name) {
	return (document.forms[form_name].elements[element_name].value.length > 0);
}

function validateSelection(form_name, element_name) {
	return (document.forms[form_name].elements[element_name].selectedIndex > 0);
}

function validateCheckBox(form_name, element_name) {
	return document.forms[form_name].elements[element_name].checked;
}

function validateCleanString(form_name, element_name) {
	return testRegExp(form_name, element_name, '^[A-Za-z0-9_]+$');
}

function validateEmail(form_name, element_name) {
	return testRegExp(form_name, element_name, '^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+@[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$');
}

function validateAbsUrl(form_name, element_name) {
	return testRegExp(form_name, element_name, '^(news|telnet|nttp|file|http|ftp|https)://[-A-Za-z0-9\\.]+$');
}

function validateNumber(form_name, element_name, allow_blank) {
	return (!allow_blank && value == '') ? false : testRegExp(form_name, element_name, '^-?[0-9]*\\.?[0-9]*$');
}

function validateSize(form_name, element_name,) {
	return testRegExp(form_name, element_name, '^[0-9]+(px|%)?$');
}

function validateID(form_name, element_name,) {
	return testRegExp(form_name, element_name, '^[A-Za-z_]([A-Za-z0-9_])*$');
}
