function passwordStrength(password1, username, password2) {
	if (password1 != password2 && password2.length > 0)
		return 5;
	var result = zxcvbn( password1, [ username ] );
	return result.score;
}
