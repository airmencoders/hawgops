function validateContactForm() {
	var name = $("#name").val();
	var email = $("#email").val();
	var message = $("#message").val();

	$("#name").removeClass("is-invalid");
	$("#name").removeClass("is-valid");
	$("#email").removeClass("is-invalid");
	$("#email").removeClass("is-valid");
	$("#message").removeClass("is-invalid");
	$("#message").removeClass("is-valid");

	var response = true;

	if(name == "") {
		$("#name").addClass("is-invalid");
		response = false;
	} else {
		$("#name").addClass("is-valid");
	}

	if(email == "") {
		$("#email").addClass("is-invalid");
		response = false;
	} else {
		$("#email").addClass("is-valid");
	}

	if(message == "") {
		$("#message").addClass("is-invalid");
		response = false;
	} else {
		$("#message").addClass("is-valid");
	}

	return response;
}

function validateLoginForm() {
	var email = $("#email").val();
	var password = $("#password").val();

	$("#email").removeClass("is-invalid");
	$("#email").removeClass("is-valid");
	$("#password").removeClass("is-invalid");
	$("#password").removeClass("is-valid");

	var response = true;

	if(email == "") {
		$("#email").addClass("is-invalid");
		response = false;
	} else {
		$("#email").addClass("is-valid");
	}

	if(password == "") {
		$("#password").addClass("is-invalid");
		response = false;
	} else {
		$("#password").addClass("is-valid");
	}

	return response;
}

function validateCreateAccountForm() {
	var fname = $("#fname").val();
	var lname = $("#lname").val();
	var email = $("#email").val();
	var password = $("#password").val();
	var confirm = $("#confirm-password").val();
	var policies = $("#read-policies").prop("checked");

	$("#fname").removeClass("is-invalid");
	$("#fname").removeClass("is-valid");
	$("#lname").removeClass("is-invalid");
	$("#lname").removeClass("is-valid");
	$("#email").removeClass("is-invalid");
	$("#email").removeClass("is-valid");
	$("#password").removeClass("is-invalid");
	$("#password").removeClass("is-valid");
	$("#confirm-password").removeClass("is-invalid");
	$("#confirm-password").removeClass("is-valid");
	$("#read-policies").removeClass("is-invalid");
	$("#read-policies").removeClass("is-valid");
	$("#password-feedback").html("");
	$("#confirm-feedback").html("");

	var response = true;

	if(fname == "") {
		$("#fname").addClass("is-invalid");
		response = false;
	} else {
		$("#fname").addClass("is-valid");
	}
	
	if(lname == "") {
		$("#lname").addClass("is-invalid");
		response = false;
	} else {
		$("#lname").addClass("is-valid");
	}
	
	if(email == "") {
		$("#email").addClass("is-invalid");
		response = false;
	} else {
		$("#email").addClass("is-valid");
	}
	
	var upperPattern = new RegExp("[A-Z]");
	var lowerPattern = new RegExp("[a-z]");
	var numberPattern = new RegExp("[0-9]");
	var specialPattern = new RegExp("[!@#$%^&*]");

	var uppercase = upperPattern.test(password);
	var lowercase = lowerPattern.test(password);
	var number = numberPattern.test(password);
	var special = specialPattern.test(password);

	if(password == "") {
		$("#password").addClass("is-invalid");
		$("#password-feedback").html("Password is required.");
		response = false;
	} else if(password.length < 8) {
		$("#password").addClass("is-invalid");
		$("#password-feedback").html("Password is too short.");
		response = false;
	} else if(!uppercase || !lowercase || !number || !special) {
		$("#password").addClass("is-invalid");
		$("#password-feedback").html("Password is too simple.");
		response = false;
	} else {
		$("#password").addClass("is-valid");
	}
	
	if(confirm == "") {
		$("#confirm-password").addClass("is-invalid");
		$("#confirm-feedback").html("Please confirm your password.");
		response = false;
	} else if(confirm != password) {
		$("#confirm-password").addClass("is-invalid");
		$("#confirm-feedback").html("Passwords do not match.");
		response = false;
	} else {
		$("#confirm-password").addClass("is-valid");
	}
	
	if(!policies) {
		$("#read-policies").addClass("is-invalid");
		response = false;
	} else {
		$("#read-policies").addClass("is-valid");
	}

	return response;
}

function validateForgotPasswordForm() {
	var email = $("#email").val();

	$("#email").removeClass("is-invalid");
	$("#email").removeClass("is-valid");

	var response = true;

	if(email == "") {
		$("#email").addClass("is-invalid");
		response = false;
	} else {
		$("#email").addClass("is-valid");
	}

	return response;
}

function validateChangePasswordForm() {
	var old_password = $("#old-password").val();
	var new_password = $("#new-password").val();
	var confirm_password = $("#confirm-password").val();
	var response = true;

	$("#old-password").removeClass("is-invalid");
	$("#old-password").removeClass("is-valid");
	$("#new-password").removeClass("is-invalid");
	$("#new-password").removeClass("is-valid");
	$("#confirm-password").removeClass("is-invalid");
	$("#confirm-password").removeClass("is-valid");
	$("#confirm-feedback").html("");

	if(old_password == "") {
		$("#old-password").addClass("is-invalid");
		response = false;
	} else {
		$("#old-password").addClass("is-valid");
	}

	if(new_password == "") {
		$("#new-password").addClass("is-invalid");
		response = false;
	} else {
		$("#new-password").addClass("is-valid");
	}

	if(confirm_password == "") {
		$("#confirm-password").addClass("is-invalid");
		$("#confirm-feedback").html("You must confirm your new password.");
		response = false;
	} else if(confirm_password != new_password) {
		$("#confirm-password").addClass("is-invalid");
		$("#confirm-feedback").html("Passwords do not match.");
		response = false;
	} else {
		$("#confirm-password").addClass("is-valid");
	}			

	return response;
}