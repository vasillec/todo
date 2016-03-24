$(function () {

	$.validator.addMethod('strongPassword', function (value, element) {
		return this.optional(element) || value.length >= 6;
	}, 'Your password must be at least 6 characters long.')

	$.validator.methods.email = function( value, element ) {
		return this.optional( element ) || /[a-z]+@[a-z]+\.[a-z]+/.test( value );
	}


	$("#register-form").validate({
		rules: {
			email: {
				required: true,
				email: true
			},
			password: {
				required: true,
				strongPassword: true
			},
			password2: {
				required: true,
				equalTo: "#password"
			}
		},
		messages: {
			email: {
				required: 'Please enter an email address.',
				email: 'Please enter a <b>valid</b> email address.'
			}
		}
	});
});