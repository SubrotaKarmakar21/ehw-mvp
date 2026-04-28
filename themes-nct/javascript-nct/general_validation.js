var $ = jQuery.noConflict();
$(document).ready(function(){
	/*Use for global */

	$.validator.addMethod('pagenm', function (value, element) {
		return /^[a-zA-Z0-9][a-zA-Z0-9\_\-]*$/.test(value);
	}, 'Page name is not valid. Only alphanumeric and _ are allowed');

	$.validator.addMethod('ip_address_required', function (value, element) {
		var ipPattern = /^(25[0-5]|2[0-4][0-9]|[0-1]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[0-1]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[0-1]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[0-1]?[0-9][0-9]?)$/;

		return this.optional(element) || ipPattern.test(value);
	}, 'Please enter a valid IP address (e.g., 192.168.0.1).');

	$.validator.addMethod('no_special_characters', function (value, element) {
		var containsForbiddenChars = /[`~!@#%^*\/\\{}\[\]<>\$€£¥₹₩₽₫₺₴₪₱₦₲č]/.test(value);

		return !containsForbiddenChars;
	}, 'Entered special characters are not allowed.');


	$.validator.addMethod('fname_lname_required', function (value, element) {
		var isValid = /^[a-zA-Z0-9\- ]*$/.test(value);
		var containsForbiddenChars = /[`~!@#%^*\/\\{}\[\]<>\$€£¥₹₩₽₫₺₴₪₱₦₲č]/.test(value);
		var urlPattern = /https?:\/\/[^\s]+/.test(value);
		var containsAlphabet = /[a-zA-Z]/.test(value);

		return isValid && containsAlphabet && !containsForbiddenChars && !urlPattern;
	}, 'Please use alphabets, numbers. The name must contain at least one alphabet letter.');

	$.validator.addMethod('fname_lname_validation', function (value, element) {

		// If value is null or empty, skip validation
		if (value == null || value.trim() === '') {
			return true;
		}

		var isValid = /^[a-zA-Z0-9\- ]*$/.test(value);
		var containsForbiddenChars = /[`~!@#%^*\/\\{}\[\]<>\$€£¥₹₩₽₫₺₴₪₱₦₲č]/.test(value);
		var urlPattern = /https?:\/\/[^\s]+/.test(value);
		var containsAlphabet = /[a-zA-Z]/.test(value);

		return isValid && containsAlphabet && !containsForbiddenChars && !urlPattern;
	}, 'Please use alphabets, numbers. The name must contain at least one alphabet letter.');


	$.validator.addMethod('alphanumeric_required', function (value, element) {
		var isValid = /^[a-zA-Z0-9][a-zA-Z0-9\s\-\.\ \,\(\)&|]*$/.test(value);
		var containsForbiddenChars = /[`~!@#%^*\/\\{}\[\]<>\$€£¥₹₩₽₫₺₴₪₱₦₲č]/.test(value);

        // Return true if the value is valid and contains at least one alphabet
		return isValid && !containsForbiddenChars;
	}, 'Please use alphabets, numbers, and/or any of these characters: - , . ( ) & |. The name must contain at least one alphabet letter.');

	$.validator.addMethod("letters_numbers_special", function(value, element) {
		return this.optional(element) || /^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[!@#$%&*])[a-zA-Z0-9!@#$%&*]+$/i.test(value);
	}, "Password must contains at least one uppercase letter, one lowercase letter, one digit and one special character.");

	$.validator.addMethod("noURL", function(value, element) {
		var urlPattern = /(?:https?:\/\/|www\.)[^\s]+|https?/i;
		return this.optional(element) || !urlPattern.test(value);
	}, "URLs are not allowed in the details");

	$.validator.addMethod('business_entity_validation', function (value, element) {
    	// If value is null or empty, skip validation
		if (value == null || value.trim() === '') {
			return true;
		}

		var containsForbiddenChars = /[`~!@#%^*\/\\{}\[\]<>\$€£¥₹₩₽₫₺₴₪₱₦₲č]/.test(value);
		var isValid = /^[a-zA-Z0-9][a-zA-Z0-9\s\-\.,\(\)&|]*$/.test(value);

    	// Check if the value matches the alphanumeric pattern
		return !containsForbiddenChars && isValid;
	}, 'Please use alphabets, numbers, and/or any of these characters: - , . : ( ) & |. The name must contain at least one alphabet letter.');

	$.validator.addMethod('faq_title_required', function (value, element) {
		var isValid = /^[a-zA-Z0-9\s\.\,\?\-]*$/.test(value);
		var containsForbiddenChars = /[`~!@#%^*\/\\{}\[\]<>\$€£¥₹₩₽₫₺₴₪₱₦₲č]/.test(value);
		var urlPattern = /https?:\/\/[^\s]+/.test(value);
		var containsAlphabet = /[a-zA-Z]/.test(value);

		// console.log("Value:", value);
		// console.log("isValid:", isValid);
		// console.log("containsAlphabet:", containsAlphabet);
		// console.log("containsForbiddenChars:", containsForbiddenChars);
		// console.log("urlPattern:", urlPattern);

		return isValid && containsAlphabet && !containsForbiddenChars && !urlPattern;
	}, 'Please use alphabets, numbers, and/or any of these characters: , . ?. The name must contain at least one alphabet letter.');

	$.validator.addMethod('business_id_validation', function (value, element) {
    	// If value is null or empty, skip validation
		if (value == null || value.trim() === '') {
			return true;
		}

		var containsForbiddenChars = /[`~!@#%^*\/\\{}\[\]<>\$€£¥₹₩₽₫₺₴₪₱₦₲č]/.test(value);
		var containsAlphabet = /^[a-zA-Z0-9]+$/.test(value);

    	// Check if the value matches the alphanumeric pattern
		return !containsForbiddenChars && containsAlphabet;
	}, 'Business ID is not valid. Only alphanumeric characters are allowed.');

	$.validator.addMethod('source_validation', function (value, element) {
	// If value is empty, skip validation
		if (value == null || value.trim() === '') {
			return true;
		}

	// Only allow a proper domain with optional path/query
		var isValid = /^(?:[a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}(\/[^\s]*)?(?:\?[^\s]*)?$/.test(value.trim());

		return isValid;
	}, 'Please enter a valid domain name like example.com, with optional path or query string.');



	$.validator.addMethod("validateURL", function(value, element) {
		var domainPattern = /^([a-zA-Z0-9][-a-zA-Z0-9]*\.[a-zA-Z]{2,63})(\.[a-zA-Z]{2,})?(\/.*)?$/;
		return this.optional(element) || domainPattern.test(value);
	}, "Please enter a valid url without http:// or https://");

	$.validator.addMethod('alphabets_required', function (value, element) {
		var isValid = /^[a-zA-Z0-9\s\-]*$/.test(value);
		var containsForbiddenChars = /[`~!@#%^*\/\\{}\[\]<>\$€£¥₹₩₽₫₺₴₪₱₦₲č]/.test(value);
		var urlPattern = /https?:\/\/[^\s]+/.test(value);
		var containsAlphabet = /[a-zA-Z]/.test(value);

		// console.log("Value:", value);
		// console.log("isValid:", isValid);
		// console.log("containsAlphabet:", containsAlphabet);
		// console.log("containsForbiddenChars:", containsForbiddenChars);
		// console.log("urlPattern:", urlPattern);

		return isValid && containsAlphabet && !containsForbiddenChars && !urlPattern;
	}, 'Please use alphabets, numbers. The name must contain at least one alphabet letter.');


	$.validator.addMethod('clinic_name_required', function (value, element) {
    	// Ensure the string starts with a valid character and contains only allowed characters
		var isValid = /^[a-zA-Z0-9][a-zA-Z0-9\s\-:.,()&|]*$/.test(value); 
    	// Ensure no forbidden characters are present
    	var containsForbiddenChars = /[`~!@#%^*/\\{}\[\]<>\$€£¥₹₩₽₫₺₴₪₱₦₲č]/.test(value); 
		var containsAlphabet = /[a-zA-Z]/.test(value);
		return isValid && !containsForbiddenChars && containsAlphabet;

	}, 'Please use alphabets, numbers, and/or any of these characters: - , . : ( ) & |. The name must contain at least one alphabet letter.');

	$.validator.addMethod('clinic_name_validation', function (value, element) {

		// If value is null or empty, skip validation
		if (value == null || value.trim() === '') {
			return true;
		}

    	// Ensure the string starts with a valid character and contains only allowed characters
		var isValid = /^[a-zA-Z0-9][a-zA-Z0-9\s\-:.,()&|]*$/.test(value); 
    	// Ensure no forbidden characters are present
    	var containsForbiddenChars = /[`~!@#%^*/\\{}\[\]<>\$€£¥₹₩₽₫₺₴₪₱₦₲č]/.test(value); 
		var containsAlphabet = /[a-zA-Z]/.test(value);
		return isValid && !containsForbiddenChars && containsAlphabet;

	}, 'Please use alphabets, numbers, and/or any of these characters: - , . : ( ) & |. The name must contain at least one alphabet letter.');


	$.validator.addMethod('compNameValidate_no_required', function (value, element) {

		// If value is null or empty, skip validation
		if (value == null || value.trim() === '') {
			return true;
		}
		
    	// Ensure the string starts with a valid character and contains only allowed characters
		var isValid = /^[a-zA-Z0-9][a-zA-Z0-9\s\-:.,()&|]*$/.test(value); 
    	// Ensure no forbidden characters are present
    	var containsForbiddenChars = /[`~!@#%^*/\\{}\[\]<>\$€£¥₹₩₽₫₺₴₪₱₦₲č]/.test(value); 
		var containsAlphabet = /[a-zA-Z]/.test(value);

		return isValid && !containsForbiddenChars && containsAlphabet;
	}, 'Please use alphabets, numbers, and/or any of these characters: - , . : ( ) & |. The name must contain at least one alphabet letter.');


	$.validator.addMethod('compSloganValidate', function (value, element) {

		// If value is null or empty, skip validation
		if (value == null || value.trim() === '') {
			return true;
		}
		
		var containsForbiddenChars = /[`~!@#%^*\/\\{}\[\]<>\$€£¥₹₩₽₫₺₴₪₱₦₲č]/.test(value);
		var containsAlphabet = /[a-zA-Z]/.test(value);

        /*Return true if there are no forbidden characters and contains at least one alphabet character*/
		return !containsForbiddenChars && containsAlphabet;
	}, 'Company slogan is not valid. The following characters are not allowed: ` ~ ! @ # % ^ * / * \\ { } [ ] < > $ € £ ¥ ₹ ₩ ₽ ₫ ₺ ₴ ₪ ₱ ₦ ₲ č. The name must contain at least one alphabet letter.');

	$.validator.addMethod('pageslugValidate', function (value, element) {
		return /^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(value);
	}, 'Country slug is not valid. Only alphabets and hyphens (-) are allowed, and hyphens cannot be at the beginning or end.');

});

