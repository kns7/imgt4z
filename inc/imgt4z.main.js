var rpcfile = "inc/ajax_rpc.php";
$(document).ready(function(){
	$(document).on("click",".menu",function(){
		$(".loader").fadeIn();
		var action = $(this).attr('rel');
		console.log("Menu Click => Action: "+action);
		$(".menu").removeClass("active");
		$(this).addClass('active');
		$.post(rpcfile,{
			action: action
		},function(data){
			switch(action){
				default:
					$(".loader").fadeOut();
				break;
				case "logout":
					/* Reloading after logout */
					$(".loader").fadeOut();
					window.location.reload();
				break;
			}
		});
	});
});


