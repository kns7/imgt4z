var rpcfile = "inc/ajax_rpc.php";

$(document).ready(function(){
	/* Menu actions (click) */
	$(document).on("click",".menu",function(){
		$(".loader").fadeIn();
		var action = $(this).attr('rel');
		console.log("Menu Click => Action: "+action);
		$(".menu").removeClass("active");
		$(this).addClass('active');
		$("#global").removeClass().hide().html("");
		$.post(rpcfile,{
			action: action
		},function(data){
			switch(action){
				default:
					buildTemplate(action,data);
				break;
				case "logout":
					/* Reloading after logout */
					$(".loader").fadeOut();
					window.location.reload();
				break;
			}
		});
	})
	/* Images Gallery (Click on an image) */
	.on("click",".img-bloc",function(){
		$(".loader").fadeIn();
		var imgid = $(this).attr('id').split("_");
		var action = 'image';
		console.log("Image block Click => View Image ID: "+imgid[1]);
		$("#global").removeClass().hide().html("");
		$.post(rpcfile,{
			action: action,
			id: imgid[1]
		},function(data){
			buildTemplate(action,data);
		});
	});
});

/* Functions */
function buildTemplate(template,datas){
	var d = JSON.parse(datas);
	$("#global").addClass(template);
	switch(template){
		case "images":
			for(var i = 0; i < d.image.length; i++){
				var image = d.image[i];
				/* Test if Title is filled */
				if(image.title === ""){ var title = "Sans Titre"; }else{ var title = image.title; }
				/* Test Image orientation */
				if(image.orientation === "1"){ 
					/* Vertical */
					var w = "150" ; var h = "200"; 
				}else{
					/* Horizontal */
					var w = "200" ; var h = "150"; 
				}
				var imgbloc = $("<div>",{
					id: 'image_'+image.id,
					class: 'img-bloc'
				});
				var img = $("<div>",{
					class: 'img',
					html: "<img src='/storage/"+image.userid+"/"+image.timestamp+".jpg' width='"+w+"' height='"+h+"'/>"
				});
				var infos = $("<div>",{
					class: 'infos',
					html: "<div class='title'>"+title+"<br/><em>"+image.dateadd+"</em></div><div class='delete' rel='"+image.id+"' title='Supprimer'></div>"
				});
				$("#global").append(imgbloc);
				$("#image_"+image.id).append(img,infos);
			}
		break;
		
		case "image":
			
		break;
		
		case "upload":
			
		break;
		
		default:
		case "home":
			
		break;
	}
	$(".loader").fadeOut();
	$("#global").fadeIn();
}
