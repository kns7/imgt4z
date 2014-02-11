$(document).ready(function(){
	$("#user").on("focus",function(){
		console.log("prout");
		if($(this).val() == "nom d'utilisateur"){
			$(this).val("");
		}
	});
	$("#pwd").on("focus",function(){
		if($(this).val() == "Password"){
			$(this).val("");
		}
	});
});
