// Password strength meter

function passwordStrength(password,username) {
    var shortPass = 1, badPass = 2, goodPass = 3, strongPass = 4;

	//password < 4
    if (password.length < 4 ) { return shortPass };

    //password == username
    if (password.toLowerCase()==username.toLowerCase()) return badPass;

	var symbolSize = 0;
	if (password.match(/[0-9]/)) symbolSize +=10;
	if (password.match(/[a-z]/)) symbolSize +=26;
	if (password.match(/[A-Z]/)) symbolSize +=26;
	if (password.match(/[^a-zA-Z0-9]/)) symbolSize +=31;

	var natLog = Math.log( Math.pow(symbolSize,password.length) );
	var score = natLog / Math.LN2;
	if (score < 40 )  return badPass
	if (score < 56 )  return goodPass
    return strongPass;
}