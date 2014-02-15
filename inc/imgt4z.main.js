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
	})
	.on("click","textarea",function(){
		this.select();
	})
});

/* Functions */
function buildTemplate(template,datas){
	var d = JSON.parse(datas);
	$("#global").addClass(template);
	switch(template){
		case "images":
			if(typeof(d.images) != 'undefined'){
				for(var i = 0; i < d.images.length; i++){
					var image = d.images[i];
					/* Test if Title is filled */
					if(image.title == ""){ var title = "Sans Titre"; }else{ var title = image.title; }
					/* Test Image orientation */
					if(image.orientation == "1"){ 
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
			}else{
				$("#global").append("<div class='noresults'>Tu n'as aucune image dans ta bibliothèque!</div>");
			}
		break;
		
		case "image":
			if(typeof(d.image) != 'undefined'){
				if(d.image.title == ""){ var title = "Sans Titre"; }else{ var title = d.image.title; }
				if(d.image.orientation == "1"){ 
					/* Vertical */
					var w = "300" ; var h = "400"; 
				}else{
					/* Horizontal */
					var w = "400" ; var h = "300"; 
				}
				
				var imgdetail = $("<div>",{ class: "img-detail"});
				var imgpreview = $("<div>",{ class: "img-preview" });
				var img = $("<img>",{ src: '/storage/'+d.image.userid+'/'+d.image.timestamp+'.jpg', width: w, height: h });
				var imgctrl = $("<div>",{class: "img-ctrl"});
				var imgedit = $("<div>",{class: "img-edit", html: "<table></table>"});
				
				/* Buttons for Actions */
				var imgactions = $("<div>",{class: "img-actions"});
				var btndelete = $("<div>",{class: "btn", rel: "delete"});
				var btnrotater = $("<div>",{class: "btn", rel: "rotater"});
				var btnrotatel = $("<div>",{class: "btn", rel: "rotatel"});
				var btnresize = $("<div>",{class: "btn", rel: "resize", rev:"1"});
				
				/* Categories */
				var select = $("<select>",{id: "imagecategorie"});
				if(typeof(d.categories) != 'undefined'){
					for(var i = 0; i < d.categories.length; i++){
						var categorie = d.categories[i];
						if(categorie.id == d.image.categorieid){ 
							var option = $("<option>",{ value: categorie.id, text: categorie.name, selected: 'selected' });
						}else{ 
							var option = $("<option>",{ value: categorie.id, text: categorie.name });
						}
						select.append(option);
					}
				}else{
					
				}
				
				imgactions.append(btndelete,btnrotater,btnrotatel,btnresize);
				imgctrl.append(imgedit,imgactions);
				imgpreview.append(img);
				imgdetail.append(imgpreview,imgctrl);
				
				$("#global").append(imgdetail);
				$("table").append("<tr><th>Catégorie</th><td id='selection'></td></tr>");
				$("#selection").append(select);
				$("table").append("<tr><th>Titre</th><td><input type='text' id='imagetitle' value='"+title+"'/></td></tr>");
				$("table").append("<tr><th>Lien pour le Forum</th><td><textarea readonly>[img]http://img.t4zone.org/i.php?i="+d.image.timestamp+"d"+d.image.userid+"[/img]</textarea></td></tr>");
				
			}else{
				$("#global").append("<div class='noresults'>L'image demandée n'a pas été trouvée...</div>");
			}
		break;
		
		case "upload":
			var help = $("<article>",{
				class: "help",
				html: d.help
			});
			var uploadform = $("<div>",{
				class: "upload-form",
				html: "<table></table>"
			});
			var btns = $("<div>",{
				class: "upload-btns",
				html: "<div class='btn' rel='send'>Envoyer</div><div class='btn' rel='cancel'>Annuler</div><div class='clear'></div>"
			});
			$("#global").append(help,uploadform,btns);
			$("table").append("<tr><th>Fichier image</th><td><input type='file' id='image-file'/></td></tr>");
			$("table").append("<tr><th>Titre</th><td><input type='text' id='image-title' value=''/></td></tr>");
			$("table").append("<tr><th>Catégorie</th><td><select id='image-category'><option value='1' selected>Mon Transporter</option><option value='2'>Report Voyages</option><option value='3'>Report Rassos</option><option value='4'>Tutos</option></select></td></tr>");
		break;
		
		default:
		case "home":
			
		break;
	}
	$(".loader").fadeOut({complete:function(){
		$("#global").show();
	}});
}
