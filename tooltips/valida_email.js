function validate() {

  let emailsString = document.getElementById("id_ema_0").value;
	emailsString = document.getElementById("id_ema_0").value = emailsString.toString().replace(/\s/g, '')
 
	if(emailsString.length > 499) {
		alert("ERROR - excedes los 500 de caracteres ")
	}else {

		let emailsSplited = emailsString.split(";")
		let invalidEmails = ""

		emailsSplited.forEach((email) => {
			 let finalEmail = email.trim()

			 if(!validateEmail(finalEmail) && invalidEmails.length==0) {
			 	invalidEmails += finalEmail
			 }else if(!validateEmail(finalEmail)) {
			 	invalidEmails += ", "+ finalEmail
			 }
		})

		if(invalidEmails.length!=0){
			alert("error con los emails ="+invalidEmails)
			document.getElementById("errormail").value=1;
		}
		else
		{
			document.getElementById("errormail").value=0;
		}
	}
 
  let emailsString2 = document.getElementById("id_ema_1").value;
	emailsString2 = document.getElementById("id_ema_1").value = emailsString2.toString().replace(/\s/g, '')
 
	if(emailsString2.length > 499) {
		alert("ERROR - excedes los 500 de caracteres ")
	}else {

		let emailsSplited2 = emailsString2.split(";")
		let invalidEmails2 = ""

		emailsSplited2.forEach((email2) => {
			 let finalEmail2 = email2.trim()

			 if(!validateEmail(finalEmail2) && invalidEmails2.length==0) {
			 	invalidEmails2 += finalEmail2
			 }else if(!validateEmail(finalEmail2)) {
			 	invalidEmails2 += ", "+ finalEmail2
			 }
		})

		if(invalidEmails2.length!=0){
			alert("error con los emails ="+invalidEmails2)
			document.getElementById("errormail").value=1;
		}
		else
		{
			document.getElementById("errormail").value=0;
		}
	}
 
}


function validateEmail(email) 
{

	let cadena = email;
	let termino = ".coop";
	let posicion = cadena.indexOf(termino);

	if (posicion !== -1)
	{
		let mailRegex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/;

	 	if(email.match(mailRegex)){
	 		return true
	 	}else {
	 		return false
	 	}
	}
	else
	{
		let mailRegex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;

	 	if(email.match(mailRegex)){
	 		return true
	 	}else {
	 		return false
	 	}

	}

}
