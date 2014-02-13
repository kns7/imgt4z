var rpcfile = "inc/ajax_rpc.php";

$(document).ready(function(){
	/* Menu actions (click) */
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

/* Functions */
function buildTemplate(template){
	switch(template){
		case "images":
			
		break;
		
		case "upload":
			
		break;
		
		default:
		case "home":
			
		break;
	}
}
