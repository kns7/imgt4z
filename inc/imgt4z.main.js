/*
Pour l'upload AJAX:
http://www.script-tutorials.com/pure-html5-file-upload/
*/

var rpcfile = "inc/ajax_rpc.php"; /* Fichier PHP pour les requêtes AJAX */
var jsonarray = new Object(); /* JSON Array avec toutes les informations (Images/Categories/Help) */
var currentimage = new Object(); /* Image Actuelle */
var smartphone = false; /* Navigation sur Smartphone? */
var pagination = new Object(); /* Informations de Pagination des images */
pagination.step = 10;
pagination.start = 0;
pagination.end = 10;

var ordre = new Object(); /* Ordre d'affichage des images */
ordre.field = "dateadd";
ordre.asc = "desc";

$(window).on("resize",function(){ resizeBlocs(); });

$(document).ready(function(){
	resizeBlocs();
	reloadJson();
	
	/* Actions du menu principal */
	$(document).on("click",".menu",function(){
		var action = $(this).attr('rel');
		if(action == "back"){
			hideshowMenu(false);
		}else{
			$(".menu").removeClass("active");
			$(this).addClass('active');
			/* On construit le template en Javascript */
			buildTemplate(action);
			if($(".menu[rel='back']").is(":visible")){
				hideshowMenu(false);
			}
		}
	})
	
	.on("click","#showmenu",function(){
		hideshowMenu(true);
	})
	
	/* Edition d'une image, clic sur une image de la galerie */
	.on("click",".img-bloc",function(){
		$(".loader").show();
		var imgid = $(this).attr('id').split("_");
		var action = 'image';
		console.log("Image block Click => View Image ID: "+imgid[1]);
		$("#global").removeClass().hide().html("");
		buildTemplate(action,imgid[1]);
	})
	
	/* Selectionner le texte en cliquant sur le Textarea */
	.on("click","textarea",function(){ this.select(); })
	
	/* Sauvegarder les changements de categorie et titre */
	.on("change","#imagecategorie,#imagetitle",function(){
		var imgid = $("#imageid").val();
		var what = $(this).attr('id');
		var val = $(this).val();
		$(".loader").show();
		console.log('Save Images Property Changes: '+what+', Image ID: '+ imgid);
		$.post(rpcfile,{
			action: 'save',
			what: what,
			imgid: imgid,
			val: val
		},function(data){
			reloadJson();
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
	
	/* Action en cliquant sur un bouton */
	.on("click",".btn",function(){
		if(!($(this).hasClass('disable'))){
			console.log("Clicked on a Button: ");
			$(".loader").show();
			if($(this).hasClass("withoverlay")){ $(".overlay").show(); }
			var what = $(this).attr('rel');
			/* On test si le bouton a l'attribut rev qui contient l'ID de l'image, sinon on prend le input caché #imageid */
			if(typeof($(this).attr('rev')) != "undefined"){ var imgid = $(this).attr('rev'); }else{ var imgid = $("#imageid").val(); }
			switch(what){
				case "resize":
					/* Redimensionnement de l'image */
					console.log("  => Resizing image preview");
					switch($("#imgpreview").width()){
						case 400: w = "800px"; h = "600px"; break;
						case 300: w = "600px"; h = "800px"; break;
						case 800: w = "400px"; h = "300px"; break;
						case 600: w = "300px"; h = "400px"; break;
					}
					$("#imgpreview").css({width: w, height: h});
					$(".loader").fadeOut();
				break;

				case "rotatel":
				case "rotater":
					/* Rotation de l'image */
					console.log("  => Rotating image");
					$.post(rpcfile,{ action: 'btn', what: what, imgid: imgid },function(data){
						reloadJson();
						d = JSON.parse(data);
						var timestamp = Math.round(+new Date() / 1000);
						$("#imgpreview").attr('src', d.url+"?t="+timestamp).css({width: d.width+"px", height: d.height+"px"});
						$(".overlay").hide();
						$(".loader").fadeOut();
					});
				break;

				case "delete":
					/* Suppression de l'image */
					if(confirm("Veux-tu définitivement supprimer cette image?")){
						console.log("  => Deleting image");
						$.post(rpcfile,{ action: 'btn', what: what, imgid: imgid },function(data){
							reloadJson();
							d = JSON.parse(data);
							if(d.result == 1){
								buildTemplate('images');
							}
						});
					}else{
						return false;
					}
				break;

				case "show":
					/* Reglage du nombre d'images affichées */
					$(".btn[rel='show']").removeClass('active');
					$(this).addClass('active');
					pagination.step = Math.round($(this).attr('amount'));
					/* Update User Settings */
					$.post(rpcfile,{action: 'usersettings', step: pagination.step});
					buildTemplate('images');
				break;

				case "next":
					/* Afficher les images suivantes */
					pagination.start = Math.round(pagination.start + pagination.step);
					pagination.end = Math.round(pagination.end + pagination.step);
					buildTemplate('images');
				break;

				case "prev":
					/* Afficher les images précédentes */
					pagination.start = Math.round(pagination.start - pagination.step);
					pagination.end = Math.round(pagination.end - pagination.step);
					buildTemplate('images');
				break;
				
				case "filter":
					
				break;
			}
		}
	})
	
	/* Changement de l'icone Categorie en changeant le select */
	.on("change","#image-categorie",function(){
		$(".categorie-icon").css({'background-image': "url('../img/categories/32/cat_"+$(this).val()+".png')"});
	})
});

/* Fonctions */
function buildTemplate(template,id){
	console.log("Call function *buildTemplate* => ["+template+"]");
	$(".loader").show();
	if(id === undefined){ id = 0; }
	var d = jsonarray;
	$("#global").removeClass().hide().html("").addClass(template);
	switch(template){
		case "images":
			if(d.images.length > 0){
				pagination.end = Math.round(pagination.start + pagination.step);
				var timestamp = Math.round(+new Date() / 1000);
				if(pagination.end > d.images.length){
					var limit = d.images.length;
				}else{
					var limit = pagination.end;
				}
				console.log("  => Affichage des Images depuis "+pagination.start +" jusqu'à "+ pagination.end);
				for(var i = pagination.start; i < limit; i++){
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
				var clear = $("<div>",{ class: 'clear'});
				var imgmenu = $("<div>",{ class: 'img-menu', text: d.count.total +' images'});
				var btnfilter = $("<div>",{class: 'btn', rel: 'filter'});
				var btnshow10 = $("<div>",{class: 'btn', rel: 'show', amount: '10'});
				var btnshow20 = $("<div>",{class: 'btn', rel: 'show', amount: '20'});
				var btnshow50 = $("<div>",{class: 'btn', rel: 'show', amount: '50'});
				var imgnext = $("<div>",{class: "btn", rel: "next", html: "Suivant"});
				var imgprev = $("<div>",{class: "btn", rel: "prev", html: "Précédent"});
				var boxfilter = $("<div>",{class: "filter-box"});
				
				imgmenu.append(btnshow10,btnshow20,btnshow50,imgnext,imgprev,clear);
				resizeBlocs();
				$("#global").append(clear,imgmenu);
				/* Activer le bouton du nombre d'images */
				$(".btn[amount="+pagination.step+"]").addClass('active');
				/* Desactiver Prev ou Next en fonction */
				if(pagination.start == 0){
					$(".btn[rel='prev']").addClass('disable');
				}
				if(pagination.end >= d.images.length){
					$(".btn[rel='next']").addClass('disable');
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
				if(currentimage.orientation == "1"){ classOrientation = "vertical"; }else{ classOrientation = "horizontal"; }
				
				var imgdetail = $("<div>",{ class: "img-detail"});
				var imgpreview = $("<div>",{ class: "img-preview" });
				var img = $("<img>",{ id: "imgpreview", class: classOrientation, src: '/storage/'+currentimage.userid+'/'+currentimage.timestamp+'.jpg?t='+timestamp});
				var imgctrl = $("<div>",{class: "img-ctrl"});
				var imgedit = $("<div>",{class: "img-edit", html: "<table></table>"});
				var imgprev = $("<div>",{class: "img-prev", image: ""});
				var imgnext = $("<div>",{class: "img-next", image: ""});
				
				/* Buttons for Actions */
				var imgactions = $("<div>",{class: "img-actions"});
				var btndelete = $("<div>",{class: "btn withoverlay", rel: "delete"});
				var btnrotater = $("<div>",{class: "btn withoverlay", rel: "rotater"});
				var btnrotatel = $("<div>",{class: "btn withoverlay", rel: "rotatel"});
				var btnresize = $("<div>",{class: "btn", rel: "resize"});
				var imgid = $("<input>",{id: "imageid", value: currentimage.id, type: "hidden"});
				
				/* Categories */
				var select = $("<select>",{id: "imagecategorie"});
				if(d.categories.length > 0){
					for(var i = 0; i < d.categories.length; i++){
						var categorie = d.categories[i];
						if(categorie.id == currentimage.categorie){
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
				if(smartphone){
					$("table").append("<tr><th>Catégorie</th></tr><tr><td id='selection'><div class='categorie-icon'></div></td></tr>");
					$("#selection").append(select);
					$("table").append("<tr><th>Titre</th></tr><tr><td><input type='text' id='imagetitle' value='"+title+"'/></td></tr>");
					$("table").append("<tr><th>Lien pour le Forum</th></tr><tr><td><textarea readonly>[img]http://img.t4zone.org/i.php?i="+currentimage.timestamp+"d"+currentimage.userid+"[/img]</textarea></td></tr>");
				}else{
					$("table").append("<tr><th>Catégorie</th><td id='selection'><div class='categorie-icon'></div></td></tr>");
					$("#selection").append(select);
					$("table").append("<tr><th>Titre</th><td><input type='text' id='imagetitle' value='"+title+"'/></td></tr>");
					$("table").append("<tr><th>Lien pour le Forum</th><td><textarea readonly>[img]http://img.t4zone.org/i.php?i="+currentimage.timestamp+"d"+currentimage.userid+"[/img]</textarea></td></tr>");
				}
				$(".categorie-icon").css({'background-image': "url('../img/categories/32/cat_"+currentimage.categorie+".png')"});
			}
		break;
		
		case "upload":
			var helpbox = $("<article>",{
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
			$("#global").append(helpbox,uploadform,btns);
			if(smartphone){
				$("table").append("<tr><th>Fichier image</th></tr><tr><td><input type='file' id='image-file'/></td></tr>");
				$("table").append("<tr><th>Titre</th></tr><tr><td><input type='text' id='image-title' value=''/></td></tr>");
				$("table").append("<tr><th>Catégorie</th></tr><tr><td id='selection'><div class='categorie-icon'></div></td></tr>");
			}else{
				$("table").append("<tr><th>Fichier image</th><td><input type='file' id='image-file'/></td></tr>");
				$("table").append("<tr><th>Titre</th><td><input type='text' id='image-title' value=''/></td></tr>");
				$("table").append("<tr><th>Catégorie</th><td id='selection'><div class='categorie-icon'></div></td></tr>");
			}
			$("#selection").append(select);
		break;
		
		case "logout":
			/* On envoie une requete au PHP pour destruction de la session */
			$.post(rpcfile,{
				action: 'logout'
			},function(data){
				/* Reloading after logout */
				$(".loader").fadeOut();
				window.location.reload();
			});
		break;
		
		default:
		case "home":
			$("#global").html("Window Size: "+ $(window).width()+"x"+$(window).height()+"px<br/>Document Size: "+ $(document).width()+"x"+$(document).height()+"px");
		break;
	}
	$(".loader").fadeOut({complete:function(){
		$("#global").show();
		resizeBlocs();
	}});
}

function reloadJson(){
	console.log("Call function *reloadJson*");
	$(".overlay").show();
	$(".loader").show();
	$.post(rpcfile,{ 
		action: 'initload'
	},function(data){
		jsonarray = JSON.parse(data);
		pagination.step = jsonarray.user.step;
		ordre.asc = jsonarray.user.ordre;
		ordre.field = jsonarray.user.field;
		console.log("JSON Array reloaded!");
		$(".overlay").hide();
		$(".loader").fadeOut();
	});
}

function resizeBlocs(){
	var gwidth = $("#global").width();
	var gheight = $("#global").height();
	var wwidth = $(window).width();
	var wheight = $(window).height();
	if(Math.floor(wwidth) < 640){
		smartphone = true;
	}
	if(!smartphone){
		$(".img-menu").css({width: Math.round(gwidth - 6)+"px"}); 
		$(".loader").css({left: Math.round((wwidth - 139)/2)+"px", top: Math.round((wheight - 47)/2)+"px"});
	}
}

function hideshowMenu(show){
	if(show){
		console.log("Show menu");
		$("#global").animate({marginLeft: "+=100px"},100);
		if($(".img-menu").length > 0){ $(".img-menu").animate({marginLeft: "+=100px"},100); }
		$("nav").animate({left: "+=100px"},100);
		$("#showmenu").fadeOut(50);
	}else{
		console.log("Hide menu");
		$("nav").animate({left: "-=100px"},100,function(){$("#showmenu").fadeIn(50);});
		$("#global").animate({marginLeft: "-=100px"},100);
		console.log($(".img-menu").css('margin-left'));
		if($(".img-menu").length > 0 && $(".img-menu").css('margin-left') > '0px'){ $(".img-menu").animate({marginLeft: "-=100px"},100); }
	}
}

function dump(arr,level){
	var dumped_text = "";
	if(!level) level = 0;
	var level_padding = "";
	for(var j=0;j<level+1;j++) level_padding += "    ";
	if(typeof(arr) == 'object'){
		for(var item in arr){
			var value = arr[item];
			if(typeof(value) == 'object'){ 
				dumped_text += level_padding + "'" + item + "' ...\n";
				dumped_text += dump(value,level+1);
			}else{
				dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
			}
		}
	}else{
		dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
	}
	return dumped_text;
}
