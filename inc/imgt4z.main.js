/*
Pour l'upload AJAX:
http://www.script-tutorials.com/pure-html5-file-upload/
*/

var rpcfile = "inc/ajax_rpc.php";
var jsonarray;
var currentimage;

$(document).ready(function(){
	reload_json();
	/* Menu actions (click) */
	$(document).on("click",".menu",function(){
		var action = $(this).attr('rel');
		$(".menu").removeClass("active");
		$(this).addClass('active');
		/* On construit le template en Javascript */
		buildTemplate(action);
	})
	/* Images Gallery (Click on an image) */
	.on("click",".img-bloc",function(){
		$(".loader").fadeIn();
		var imgid = $(this).attr('id').split("_");
		var action = 'image';
		console.log("Image block Click => View Image ID: "+imgid[1]);
		$("#global").removeClass().hide().html("");
		buildTemplate(action,imgid[1]);
	})
	.on("click","textarea",function(){
		this.select();
	})
	.on("change","#imagecategorie,#imagetitle",function(){
		var imgid = $("#imageid").val();
		var what = $(this).attr('id');
		var val = $(this).val();
		$(".loader").fadeIn();
		console.log('Save Images Property Changes: '+what+', Image ID: '+ imgid);
		$.post(rpcfile,{
			action: 'save',
			what: what,
			imgid: imgid,
			val: val
		},function(data){
			reload_json();
			var d = JSON.parse(data);
			if(d.result == 1){
				if(what == "imagecategorie"){ $(".categorie-icon").css({'background-image': "url('../img/categories/32/cat_"+val+".png')"}); }
				$("#"+what).css({'background-image': 'url("../img/valid.png")', 'border-color': "#00FF00"});
			}else{
				$("#"+what).css({'background-image': 'url("../img/error.png")', 'border-color': "#FF0000"});
			}
			$(".loader").fadeOut();
		});
	})
	.on("click",".btn",function(){
		var what = $(this).attr('rel');
		/* On test si le bouton a l'attribut rev qui contient l'ID de l'image, sinon on prend le input caché #imageid */
		if(typeof($(this).attr('rev')) != "undefined"){ var imgid = $(this).attr('rev'); }else{ var imgid = $("#imageid").val(); }
		if( what != "resize"){
			if(what == "delete" && confirm("Veux-tu définitivement supprimer cette image?") || what == "rotatel" || what == 'rotater'){
				$(".loader").fadeIn();
				$(".overlay").show();
				$.post(rpcfile,{
					action: 'btn',
					what: what,
					imgid: imgid
				},function(data){
					reload_json();
					d = JSON.parse(data);
					switch(what){
						case "rotatel":
						case "rotater":
							var timestamp = Math.round(+new Date() / 1000);
							$("#imgpreview").attr('src', d.url+"?t="+timestamp).css({width: d.width+"px", height: d.height+"px"});
						break;
						
						case "delete":
							if(d.result == 1){
								$("#global").removeClass().hide().html("");
								buildTemplate('images');
							}else{
								
							}
						break;
					}
					$(".overlay").hide();
					$(".loader").fadeOut();
				})
			}else{
				return false;
			}
		}else{
			/* Only resize Image Preview */
			console.log("Resizing. Original Width: "+$("#imgpreview").width());
			$(".loader").fadeIn();
			switch($("#imgpreview").width()){
				case 400: w = "800px"; h = "600px"; break;
				case 300: w = "600px"; h = "800px"; break;
				case 800: w = "400px"; h = "300px"; break;
				case 600: w = "300px"; h = "400px"; break;
			}
			$("#imgpreview").css({width: w, height: h});
			$(".loader").fadeOut();
		}
	})
	.on("change","#image-categorie",function(){
		$(".categorie-icon").css({'background-image': "url('../img/categories/32/cat_"+$(this).val()+".png')"});
	})
});

/* Functions */
function buildTemplate(template,id){
	console.log("Call function *buildTemplate* => ["+template+"]");
	$(".loader").fadeIn();
	if(id === undefined){ id = 0; }
	var d = jsonarray;
	$("#global").removeClass().hide().html("").addClass(template);
	switch(template){
		case "images":
			if(d.images.length > 0){
				var timestamp = Math.round(+new Date() / 1000);
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
						html: "<img src='/storage/"+image.userid+"/"+image.timestamp+".jpg?t="+timestamp+"' width='"+w+"' height='"+h+"'/>"
					});
					var infos = $("<div>",{
						class: 'infos',
						html: "<div class='title' rel='"+image.categorie+"'>"+title+"<br/><em>"+image.dateadd+"</em></div><div class='btn delete' rel='delete' rev='"+image.id+"' title='Supprimer'></div>"
					});
					$("#global").append(imgbloc);
					$("#image_"+image.id).append(img,infos);
				}
			}else{
				$("#global").append("<div class='noresults'>Tu n'as aucune image dans ta bibliothèque!</div>");
			}
		break;
		
		case "image":
			for(i=0;i< d.images.length;i++){
				if(d.images[i].id == id){ currentimage = d.images[i];}
			}
			if(currentimage.id === undefined){
				$("#global").append("<div class='noresults'>L'image demandée n'a pas été trouvée...</div>");
			}else{
				var timestamp = Math.round(+new Date() / 1000);
				if(currentimage.title == ""){ var title = ""; }else{ var title = currentimage.title; }
				if(currentimage.orientation == "1"){ 
					/* Vertical */
					var w = "300" ; var h = "400"; 
				}else{
					/* Horizontal */
					var w = "400" ; var h = "300"; 
				}
				
				var imgdetail = $("<div>",{ class: "img-detail"});
				var imgpreview = $("<div>",{ class: "img-preview" });
				var img = $("<img>",{ id: "imgpreview", src: '/storage/'+currentimage.userid+'/'+currentimage.timestamp+'.jpg?t='+timestamp}).css({width: w+"px", height: h+"px"});
				var imgctrl = $("<div>",{class: "img-ctrl"});
				var imgedit = $("<div>",{class: "img-edit", html: "<table></table>"});
				
				/* Buttons for Actions */
				var imgactions = $("<div>",{class: "img-actions"});
				var btndelete = $("<div>",{class: "btn", rel: "delete"});
				var btnrotater = $("<div>",{class: "btn", rel: "rotater"});
				var btnrotatel = $("<div>",{class: "btn", rel: "rotatel"});
				var btnresize = $("<div>",{class: "btn", rel: "resize"});
				var imgid = $("<input>",{id: "imageid", value: currentimage.id, type: "hidden"});
				
				/* Categories */
				var select = $("<select>",{id: "imagecategorie"});
				if(jsonarray.categories.length > 0){
					for(var i = 0; i < jsonarray.categories.length; i++){
						var categorie = jsonarray.categories[i];
						if(categorie.id == currentimage.categorieid){
							var option = $("<option>",{ value: categorie.id, text: categorie.name, selected: 'selected' });
						}else{ 
							var option = $("<option>",{ value: categorie.id, text: categorie.name });
						}
						select.append(option);
					}
				}
				
				imgactions.append(btndelete,btnrotater,btnrotatel,btnresize);
				imgctrl.append(imgedit,imgactions,imgid);
				imgpreview.append(img);
				imgdetail.append(imgpreview,imgctrl);
				
				$("#global").append(imgdetail);
				$("table").append("<tr><th>Catégorie</th><td id='selection'><div class='categorie-icon'></div></td></tr>");
				$("#selection").append(select);
				$("table").append("<tr><th>Titre</th><td><input type='text' id='imagetitle' value='"+title+"'/></td></tr>");
				$("table").append("<tr><th>Lien pour le Forum</th><td><textarea readonly>[img]http://img.t4zone.org/i.php?i="+currentimage.timestamp+"d"+currentimage.userid+"[/img]</textarea></td></tr>");
				$(".categorie-icon").css({'background-image': "url('../img/categories/32/cat_"+currentimage.categorie+".png')"});
			}
		break;
		
		case "upload":
			var help = $("<article>",{
				class: "help",
				html: d.help.upload
			});
			var uploadform = $("<div>",{
				class: "upload-form",
				html: "<table></table>"
			});
			var btns = $("<div>",{
				class: "upload-btns",
				html: "<div class='btn' rel='send'>Envoyer</div><div class='btn' rel='cancel'>Annuler</div><div class='clear'></div>"
			});
			var select = $("<select>",{id: "image-categorie"});
			if(d.categories.length > 0){
					for(var i = 0; i < d.categories.length; i++){
						var categorie = d.categories[i];
					if(categorie.id == 1){ 
						var option = $("<option>",{ value: categorie.id, text: categorie.name, selected: 'selected' });
					}else{
						var option = $("<option>",{ value: categorie.id, text: categorie.name });
					}
					select.append(option);
				}
			}
			$("#global").append(help,uploadform,btns);
			$("table").append("<tr><th>Fichier image</th><td><input type='file' id='image-file'/></td></tr>");
			$("table").append("<tr><th>Titre</th><td><input type='text' id='image-title' value=''/></td></tr>");
			$("table").append("<tr><th>Catégorie</th><td id='selection'><div class='categorie-icon'></div></td></tr>");
			$("#selection").append(select);
		break;
		
		case "logout":
			/* On envoie une requete au PHP pour destruction de la session */
			$.post(rpcfile,{
				action: action
			},function(data){
				/* Reloading after logout */
				$(".loader").fadeOut();
				window.location.reload();
			});
		break;
		
		default:
		case "home":
			
		break;
	}
	$(".loader").fadeOut({complete:function(){
		$("#global").show();
	}});
}
function reload_json(){
	$.post(rpcfile,{ 
		action: 'initload'
	},function(data){
		jsonarray = JSON.parse(data);
	});
}
