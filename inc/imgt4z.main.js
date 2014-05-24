/*
Pour l'upload AJAX:
http://www.script-tutorials.com/pure-html5-file-upload/
*/

var rpcfile = "inc/ajax_rpc.php"; /* Fichier PHP pour les requêtes AJAX */
var jsonarray = new Object(); /* JSON Array avec toutes les informations (Images/Categories/Help) */
var image = new Object(); /* Image Actuelle */
var album = new Object(); /* Album actuel */
var smartphone = false; /* Navigation sur Smartphone? */
var pagination = new Object(); /* Informations de Pagination des images */
pagination.step = 10;
pagination.start = 0;
pagination.end = 10;
var maxuploadsize = 5242880;
var current = new Object();
current.album = 0;
current.image = 0;

var ordre = new Object(); /* Ordre d'affichage des images */
ordre.field = "dateadd";
ordre.asc = "desc";

$(window).on("resize",function(){ resizeBlocs(); });

$(document).ready(function(){
	resizeBlocs();
	reloadJson("albums");
	
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
	/* Liste des images d'un album, clic sur un album */
	.on("click",".album-bloc",function(){
		$(".loader").show();
		var albid = $(this).attr('id').split("_");
		current.album = albid[1];
		var action = 'images';
		console.log("Album block Click => View Album ID: "+current.album);
		$("#global").removeClass().hide().html("");
		buildTemplate(action);
	})
	
	/* Edition d'une image, clic sur une image de la galerie */
	.on("click",".img-bloc",function(){
		$(".loader").show();
		var imgid = $(this).attr('id').split("_");
		current.image = imgid[1];
		var action = 'image';
		console.log("Image block Click => View Image ID: "+current.image);
		$("#global").removeClass().hide().html("");
		buildTemplate(action);
	})
	
	/* Selectionner le texte en cliquant sur le Textarea */
	.on("click","textarea",function(){ if(!smartphone){ this.select();} })
	
	/* Afficher / Cacher le formulaire en cliquant sur le titre */
	.on("click",".bloc-title",function(){
		var titleobj = this;
		var formid = $(this).attr('rel');
		if($(".form-bloc[rel="+formid+"]").is(":visible")){
			$(".form-bloc[rel="+formid+"]").slideUp('200',function(){ $(titleobj).css({"border-bottom": "1px solid","border-bottom-left-radius": "5px","border-bottom-right-radius": "5px"});});
		}else{
			$(this).css({"border-bottom": "0px solid","border-bottom-left-radius": "0px","border-bottom-right-radius": "0px"});
			$(".form-bloc:visible").slideUp('200',function(){$(this).css({"border-bottom": "1px solid","border-bottom-left-radius": "5px","border-bottom-right-radius": "5px"});});
			$(".form-bloc[rel="+formid+"]").slideDown('200');
		}
	})
	
	/* Sauvegarder les changements de album et titre */
	.on("change","#imagealbum,#imagetitle",function(){
		var imgid = $("#imageid").val();
		var field = $(this).attr('id');
		var val = $(this).val();
		$(".loader").show();
		console.log('Save Images Property Changes: '+field+', Image ID: '+ imgid);
		$.post(rpcfile,{
			action: 'save',
			what: 'image',
			field: field,
			imgid: imgid,
			val: val
		},function(data){
			reloadJson();
			var d = JSON.parse(data);
			if(d.result == 1){
				if(field == "imagealbum"){ current.album = val; }
				$("#"+field).css({'background-image': 'url("../img/valid.png")', 'border-color': "#00FF00"});
			}else{
				$("#"+field).css({'background-image': 'url("../img/error.png")', 'border-color': "#FF0000"});
			}
			$(".loader").fadeOut();
		});
	})
	
	/* Action en cliquant sur un bouton */
	.on("click",".btn",function(){
		if(!($(this).hasClass('disable'))){
			var what = $(this).attr('rel');
			var rev = $(this).attr('rev');
			console.log("Clicked on a Button: {"+what+":"+rev+"}");
			if(!$(this).hasClass("noload")){ $(".loader").show(); }
			if($(this).hasClass("withoverlay")){ $(".overlay").show(); }
			/* On test si le bouton a l'attribut rev qui contient l'ID de l'image, sinon on prend le input caché #imageid */
			if(typeof(rev != "undefined")){ var imgid = rev; }else{ var imgid = $("#imageid").val(); }
			switch(what){
				case "add":
					switch(rev){
						case "album":
							console.log("  => Add an Album");
							buildTemplate('addalbum');
						break;
					}
				break;
				
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
				
				case "backto":
					/* Retour à l'album */
					if(current.image != 0){
						console.log("  => Back to Album (ID:"+current.album+")");
						buildTemplate('images');
					}else{
						console.log("  => Back to Album List");
						buildTemplate('albums');
					}
				break;

				case "rotatel":
				case "rotater":
					/* Rotation de l'image */
					console.log("  => Rotating image");
					$.post(rpcfile,{ action: 'btn', what: what, imgid: imgid },function(data){
						reloadJson();
						d = JSON.parse(data);
						var timestamp = Math.round(+new Date() / 1000);
						$("#imgpreview").attr('src', d.url+"?t="+timestamp).removeClass().addClass(d.orientation);
						$(".overlay").hide();
						$(".loader").fadeOut();
					});
				break;

				case "delete":
					/* Suppression de l'image */
					if(confirm("Veux-tu définitivement supprimer cette image?")){
						console.log("  => Deleting image");
						$.post(rpcfile,{ action: 'btn', what: what, imgid: imgid },function(data){
							
							d = JSON.parse(data);
							if(d.result == 1){
								reloadJson('images');
							}
						});
					}else{
						return false;
					}
				break;
				
				case "editalbum":
					/* Edition d'un album */
					current.album = rev;
					console.log("  => Editing album (ID: "+current.album+")");
					buildTemplate('editalbum');
					return false;
				break;
				
				case "deletealbum":
					/* Suppression de l'album (et de toutes ses images) */
					if(confirm("Veux-tu définitivement supprimer cet album ?")){
						var albumid = $("#albumid").val();
						console.log("  => Deleting album ID "+albumid);
						if(album.hasOwnProperty('images')){
							if(confirm("Veux-tu aussi supprimer les photos qu'il contient ? ")){
								var deleteimages = 1;
							}else{
								var deleteimages = 0;
							}
						}else{
							var deleteimages = 2;
						}
						$.post(rpcfile,{ action: 'btn', what: what, albumid: albumid, deleteimages: deleteimages },function(data){
							d = JSON.parse(data);
							if(d.result == 1){
								reloadJson('albums');
								$(".overlay").hide();
							}
						});
					}else{
						$(".loader").fadeOut();
						$(".overlay").hide();
						return false;
					}
				break;
				
				case "show":
					/* Affiche ou cache le menu du nbr d'images affichées */
					if($(".btn-menu[rel='show']").is(':visible')){
						$(".btn-menu[rel='show']").hide();
					}else{
						$(".btn-menu[rel='show']").show();
					}
				break;

				case "changeshow":
					/* Reglage du nombre d'images affichées */
					$(".btn[rel='changeshow']").removeClass('active');
					$(this).addClass('active');
					$(".btn[rel='show']").attr('amount');
					$(".btn-menu[rel='show']").hide();
					pagination.step = Math.round($(this).attr('amount'));
					/* Update User Settings */
					$.post(rpcfile,{action: 'usersettings', step: pagination.step});
					jsonarray.user.step = Math.round($(this).attr('amount'));
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
				
				case "send":
					/* Upload d'une photo */					
					$('#upload_form').ajaxSubmit({ 
						beforeSubmit:	uploadBeforeSubmit,
						success:		uploadSuccess,
						error:			uploadError,
						resetForm:		false
					});            
					return false; 
				break;
				
				case "save":
					switch(rev){
						case "album":
							/* Sauvegarder un Formulaire */
							$.post(rpcfile,{
								action: 'save', 
								what: "album",
								albumtitle: $("#albumtitle").val(),
								albumid: $("#albumid").val()
							},function(data){
								reloadJson("albums");
								$(".overlay").hide();
							});
						break;
						
						case "changepwd":
							/* Sauvegarder le nouveau mot de passe d'un utilisateur */
							if($("#passwd").val() == $("#passwd2").val()){
								$.post(rpcfile,{
									action: 'save', 
									what: "user",
									field: "password",
									passwd: $("#passwd").val()
								},function(data){
									$(".overlay").hide();
									$(".loader").fadeOut();
								});
							}else{
								alert("Les mots de passes ne correspondent pas!");
								return false;
							}
						break;
						
						case "settings":
							/* Sauvegarder les préférences d'un utilisateur */
							$.post(rpcfile,{
								action: 'save', 
								what: "user",
								field: "settings",
								ordre: $("#ordre").val(),
								champ: $("#champ").val(),
								step: $("#step").val()
							},function(data){
								reloadJson("compte");
								$(".overlay").hide();
							});
						break;
					}
				break;
				
				case "cancel":
					buildTemplate(rev);
				break;
			}
		}
	})
	
	/* Changement de l'icone Categorie en changeant le select */
	.on("change","#image-album",function(){
		$(".album-icon").css({'background-image': "url('../img/albums/32/cat_"+$(this).val()+".png')"});
	})
});

/* Fonctions */
function buildTemplate(template){
	console.log("Call function *buildTemplate* => ["+template+"]");
	$(".loader").show();
	var d = jsonarray;
	$("#global").removeClass().hide().html("").addClass(template);
	switch(template){
		default:
		case "albums":
			current.image = 0;
			current.album = 0;
			image = "";
			album = "";
			$("#global").append(albumbloc);
			$("#album_0").append(img,infos);
			if(d.hasOwnProperty('albums')){
				for(var i = 0; i < d.albums.length ; i++){
					var albumbloc = $("<div>",{
						id: 'album_'+d.albums[i].id,
						class: 'album-bloc'
					});
					if(d.albums[i].hasOwnProperty('images')){
						amountimages = d.albums[i].images.length;
					}else{
						amountimages = "0";
					}
					var amount = $("<div>",{
						class: 'amount',
						html: amountimages
					});
					var img = $("<div>",{
						class: 'img',
						html: "<img src='/img/album_64.png'/>"
					});
					if(d.albums[i].id == '1'){
						var infos = $("<div>",{
							class: 'infos',
							html: "<div class='title'>"+d.albums[i].name+"</div>"
						});
					}else{
						var infos = $("<div>",{
							class: 'infos',
							html: "<div class='title'>"+d.albums[i].name+"</div><div class='btn edit' rel='editalbum' rev='"+d.albums[i].id+"' title='Editer'></div>"
						});
					}
					$("#global").append(albumbloc);
					$("#album_"+d.albums[i].id).append(amount,img,infos);
				}
				var clear = $("<div>",{ class: 'clear'});
				var btnaddalbum = $("<div>",{class: "btn", rel: "add", rev: "album", title: "Ajouter un album"});
				var imgmenu = $("<div>",{ class: 'img-menu', text: d.albums.length +' albums'});			
				imgmenu.append(btnaddalbum,clear);
				resizeBlocs();
				$("#global").append(clear,imgmenu);
			}
		break;
		
		case "images":
			current.image = 0;
			for(var i = 0; i < jsonarray.albums.length; i++){
				if(jsonarray.albums[i]['id'] == current.album){
					album = jsonarray.albums[i];
				}
			}
			if(album.id === undefined){
				$("#global").append("<div class='noresults'>L'album demandée n'a pas été trouvée...</div>");
			}else{
				image = "";
				if(album.hasOwnProperty('images')){
					pagination.end = Math.round(pagination.start + pagination.step);
					var timestamp = Math.round(+new Date() / 1000);
					if(pagination.end > album.images.length){
						var limit = album.images.length;
					}else{
						var limit = pagination.end;
					}
					console.log("  => Affichage des Images depuis "+pagination.start +" jusqu'à "+ pagination.end);
					for(var i = pagination.start; i < limit; i++){
						/* Test if Title is filled */
						if(album.images[i].title == ""){ var title = "Sans Titre"; }else{ var title = album.images[i].title; }
						/* Test Image orientation */
						if(album.images[i].orientation == "1"){
							/* Vertical */
							if(album.images[i].width < '150'){
								w = album.images[i].width;
							}else{
								w = "150"
							}
						}else{
							/* Horizontal */
							if(album.images[i].width < '200'){
								w = album.images[i].width;
							}else{
								w = "200"
							}
						}
						var imgbloc = $("<div>",{
							id: 'image_'+album.images[i].id,
							class: 'img-bloc'
						});
						var img = $("<div>",{
							class: 'img',
							html: "<img src='/storage/"+album.images[i].userid+"/"+album.images[i].timestamp+".jpg?t="+timestamp+"' width='"+w+"'/>"
						});
						var infos = $("<div>",{
							class: 'infos',
							html: "<div class='title' rel='"+album.images[i].album+"'>"+title+"<br/><em>"+album.images[i].dateadd+"</em></div><div class='btn delete' rel='delete' rev='"+album.images[i].id+"' title='Supprimer'></div>"
						});
						$("#global").append(imgbloc);
						$("#image_"+album.images[i].id).append(img,infos);
					}
					var clear = $("<div>",{ class: 'clear'});
					if(album.images.length >= 1){ var txtimg = "image"; }else{ var txtimg = "images"; }
					var imgmenu = $("<div>",{ class: 'img-menu', text: album.name +': '+album.images.length +' '+txtimg});
					var btnfilter = $("<div>",{class: 'btn', rel: 'filter'});
					var btnshowmenu = $("<div>",{class: 'btn-menu', rel: 'show'});
					var btnshow = $("<div>",{class: 'btn noload', rel: 'show', amount: '0'});
					var btnshow10 = $("<div>",{class: 'btn', rel: 'changeshow', amount: '10'});
					var btnshow20 = $("<div>",{class: 'btn', rel: 'changeshow', amount: '20'});
					var btnshow50 = $("<div>",{class: 'btn', rel: 'changeshow', amount: '50'});
					var imgnext = $("<div>",{class: "btn", rel: "next", html: "Suivant"});
					var imgprev = $("<div>",{class: "btn", rel: "prev", html: "Précédent"});
					var btnbackto = $("<div>",{class: "btn", rel: "backto", title: "Retour aux albums"});
					var boxfilter = $("<div>",{class: "filter-box"});
					btnshow.attr('amount',d.user.step);
					btnshowmenu.append(btnshow50,btnshow20,btnshow10);
					imgmenu.append(btnshow,btnbackto,btnshowmenu,imgnext,imgprev,clear);
					resizeBlocs();
					$("#global").append(clear,imgmenu);
					/* Activer le bouton du nombre d'images */
					$(".btn[amount="+pagination.step+"]").addClass('active');
					/* Desactiver Prev ou Next en fonction */
					if(pagination.start == 0){
						$(".btn[rel='prev']").addClass('disable');
					}
					if(pagination.end >= album.images.length){
						$(".btn[rel='next']").addClass('disable');
					}
				}else{
					var clear = $("<div>",{ class: 'clear'});
					var imgmenu = $("<div>",{ class: 'img-menu', text: album.name +': 0 images'});
					var btnshowmenu = $("<div>",{class: 'btn-menu', rel: 'show'});
					var btnshow = $("<div>",{class: 'btn noload', rel: 'show', amount: '0'});
					var btnshow10 = $("<div>",{class: 'btn', rel: 'changeshow', amount: '10'});
					var btnshow20 = $("<div>",{class: 'btn', rel: 'changeshow', amount: '20'});
					var btnshow50 = $("<div>",{class: 'btn', rel: 'changeshow', amount: '50'});
					var btnbackto = $("<div>",{class: "btn", rel: "backto", title: "Retour aux albums"});
					var boxfilter = $("<div>",{class: "filter-box"});
					btnshow.attr('amount',d.user.step);
					btnshowmenu.append(btnshow50,btnshow20,btnshow10);
					imgmenu.append(btnshow,btnbackto,btnshowmenu,imgnext,imgprev,clear);
					$("#global").append("<div class='noresults'>Tu n'as aucune image dans cet album!</div>").append(clear,imgmenu);
				}
			}
		break;
		
		case "image":
			for(i=0;i< album.images.length;i++){
				if(album.images[i].id == current.image){ 
					image = album.images[i];
				}
			}
			if(image.id === undefined){
				$("#global").append("<div class='noresults'>L'image demandée n'a pas été trouvée...</div>");
			}else{
				var timestamp = Math.round(+new Date() / 1000);
				if(image.title == ""){ var title = ""; }else{ var title = image.title; }
				if(image.orientation == "1"){ classOrientation = "vertical"; }else{ classOrientation = "horizontal"; }
				
				var imgdetail = $("<div>",{ class: "img-detail"});
				var imgpreview = $("<div>",{ class: "img-preview" });
				var img = $("<img>",{ id: "imgpreview", class: classOrientation, src: '/storage/'+image.userid+'/'+image.timestamp+'.jpg?t='+timestamp});
				var imgctrl = $("<div>",{class: "img-ctrl"});
				var imgedit = $("<div>",{class: "img-edit", html: "<table></table>"});
				var imgprev = $("<div>",{class: "img-prev", image: ""});
				var imgnext = $("<div>",{class: "img-next", image: ""});
				
				/* Buttons for Actions */
				var imgactions = $("<div>",{class: "img-actions"});
				var btndelete = $("<div>",{class: "btn withoverlay", rel: "delete", title: "Supprimer l'image"});
				var btnrotater = $("<div>",{class: "btn withoverlay", rel: "rotater", title: "Rotation sur la droite"});
				var btnrotatel = $("<div>",{class: "btn withoverlay", rel: "rotatel", title: "Rotation sur la gauche"});
				var btnbackto = $("<div>",{class: "btn", rel: "backto", title: "Retour à l'album"});
				//var btnresize = $("<div>",{class: "btn", rel: "resize", title: "Redimensionner"});
				var imgid = $("<input>",{id: "imageid", value: image.id, type: "hidden"});
				
				/* Categories */
				var select = $("<select>",{id: "imagealbum"});
				if(d.albums.length > 0){
					for(var i = 0; i < d.albums.length; i++){
						if(d.albums[i].id == album.id){
							var option = $("<option>",{ value: d.albums[i].id, text: d.albums[i].name, selected: 'selected' });
						}else{
							var option = $("<option>",{ value: d.albums[i].id, text: d.albums[i].name });
						}
						select.append(option);
					}
				}
				
				imgactions.append(btndelete,btnrotater,btnrotatel,btnbackto);
				imgctrl.append(imgedit,imgactions,imgid);
				imgpreview.append(img);
				imgdetail.append(imgpreview,imgctrl);
				
				$("#global").append(imgdetail);
				if(smartphone){
					$("table").append("<tr><th>Album</th></tr><tr><td id='selection'></td></tr>");
					$("#selection").append(select);
					$("table").append("<tr><th>Titre</th></tr><tr><td><input type='text' id='imagetitle' value='"+title+"'/></td></tr>");
					$("table").append("<tr><th>Lien pour le Forum</th></tr><tr><td><textarea readonly>[img]http://img.kns7.org/i.php?i="+image.timestamp+"d"+image.userid+"[/img]</textarea></td></tr>");
					$("table").append("<tr><th>Lien direct</th></tr><tr><td><textarea readonly>http://img.kns7.org/i.php?i="+image.timestamp+"d"+image.userid+"</textarea></td></tr>");
				}else{
					$("table").append("<tr><th>Album</th><td id='selection'></td></tr>");
					$("#selection").append(select);
					$("table").append("<tr><th>Titre</th><td><input type='text' id='imagetitle' value='"+title+"'/></td></tr>");
					$("table").append("<tr><th>Lien pour le Forum</th><td><textarea readonly>[img]http://img.kns7.org/i.php?i="+image.timestamp+"d"+image.userid+"[/img]</textarea></td></tr>");
					$("table").append("<tr><th>Lien direct</th><td><textarea readonly>http://img.kns7.org/i.php?i="+image.timestamp+"d"+image.userid+"</textarea></td></tr>");
				}
			}
		break;
		
		case "upload":
			var helpbox = $("<article>",{
				class: "help",
				html: d.help.upload
			});
			var uploadform = $("<div>",{
				class: "upload-form",
				html: "<div id='uploadoutput'></div><form action='inc/upload.php' method='post' enctype='multipart/form-data' id='upload_form'><input type='hidden' id='action' value='imageupload'/><table></table></form>"
			});
			var btns = $("<div>",{
				class: "upload-btns",
				html: "<div class='btn withoverlay' rel='send'>Envoyer</div><div class='btn' rel='cancel' rev='upload'>Annuler</div><div class='clear'></div>"
			});
			var select = $("<select>",{id: "album"});
			if(d.albums.length > 0){
					for(var i = 0; i < d.albums.length; i++){
					if(d.albums[i].id == 1){ 
						var option = $("<option>",{ value: d.albums[i].id, text: d.albums[i].name, selected: 'selected' });
					}else{
						var option = $("<option>",{ value: d.albums[i].id, text: d.albums[i].name });
					}
					select.append(option);
				}
			}
			$("#global").append(helpbox,uploadform,btns);
			if(smartphone){
				$("table").append("<tr><th>Fichier image</th></tr><tr><td><input type='file' id='file' name='file'/></td></tr>");
				$("table").append("<tr><th>Titre</th></tr><tr><td><input type='text' id='title' name='title' value=''/></td></tr>");
				$("table").append("<tr><th>Album</th></tr><tr><td id='selection'><div class='album-icon'></div></td></tr>");
			}else{
				$("table").append("<tr><th>Fichier image</th><td><input type='file' id='file' name='file'/></td></tr>");
				$("table").append("<tr><th>Titre</th><td><input type='text' id='title' name='title' value=''/></td></tr>");
				$("table").append("<tr><th>Album</th><td id='selection'><div class='album-icon'></div></td></tr>");
			}
			$("#selection").append(select);
		break;
		
		case "addalbum":
			var addform = $("<div>",{
				class: "add-form",
				html: "<form id='add_form'><input type='hidden' id='action' value='addalbum'/><table></table></form>"
			});
			var btns = $("<div>",{
				class: "add-btns",
				html: "<div class='btn withoverlay' rel='save' rev='album'>Ajouter</div><div class='btn withoverlay' rel='cancel' rev='albums'>Annuler</div><div class='clear'></div>"
			});
			$("#global").append(addform,btns);
			if(smartphone){
				$("table").append("<tr><th>Titre</th></tr><tr><td><input type='text' id='albumtitle' name='albumtitle' value=''/></td></tr>");
			}else{
				$("table").append("<tr><th>Titre</th><td><input type='text' id='albumtitle' name='albumtitle' value=''/></td></tr>");
			}
		break;
		
		case "editalbum":
			for(var i = 0; i < jsonarray.albums.length; i++){
				if(jsonarray.albums[i]['id'] == current.album){
					album = jsonarray.albums[i];
				}
			}
			if(album.id === undefined){
				$("#global").append("<div class='noresults'>L'album demandée n'a pas été trouvée...</div>");
			}else{
				var addform = $("<div>",{
					class: "add-form",
					html: "<form id='edit_form'><input type='hidden' id='action' value='editalbum'/><input type='hidden' id='albumid' value='"+album.id+"'/><table></table></form>"
				});
				var btns = $("<div>",{
					class: "add-btns",
					html: "<div class='btn withoverlay' rel='save' rev='album'>Enregistrer</div><div class='btn withoverlay' rel='cancel' rev='albums'>Annuler</div><div class='btn withoverlay' rel='deletealbum'>Supprimer</div><div class='clear'></div>"
				});
				$("#global").append(addform,btns);
				if(smartphone){
					$("table").append("<tr><th>Titre</th></tr><tr><td><input type='text' id='albumtitle' name='albumtitle' value='"+album.name+"'/></td></tr>");
				}else{
					$("table").append("<tr><th>Titre</th><td><input type='text' id='albumtitle' name='albumtitle' value='"+album.name+"'/></td></tr>");
				}
			}
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
		
		case "compte":
			var addform = $("<div>",{
				class: "add-form"
			});
			var changepwd_title = $("<div>",{
				class: "bloc-title",
				rel: "changepwd",
				text: "Changer mon mot de passe"
			});
			var changepwd_form = $("<div>",{
				class: "form-bloc",
				rel: "changepwd",
				html: "<table rel='changepwd'></table>"
			});
			var changepwd_btns = $("<div>",{
				class: "add-btns",
				html: "<div class='btn withoverlay' rel='save' rev='changepwd'>Enregistrer</div><div class='clear'></div>"
			});
			changepwd_form.append(changepwd_btns);
			var settings_title = $("<div>",{
				class: "bloc-title",
				rel: "settings",
				text: "Réglages de mon compte"
			});
			var settings_form = $("<div>",{
				class: "form-bloc",
				rel: "settings",
				html: "<table rel='settings'></table>"
			});
			var settings_btns = $("<div>",{
				class: "add-btns",
				html: "<div class='btn withoverlay' rel='save' rev='settings'>Enregistrer</div><div class='clear'></div>"
			});
			settings_form.append(settings_btns);
			var stats_title = $("<div>",{
				class: "bloc-title",
				rel: "stats",
				text: "Statistiques de mon compte"
			});
			var stats_form = $("<div>",{
				class: "form-bloc",
				rel: 'stats',
				html: "<table rel='stats'></table>"
			});
			
			addform.append(stats_title, stats_form);
			if(d.settings.auth == "local"){ addform.append(changepwd_title, changepwd_form); }
			addform.append(settings_title, settings_form);
			$("#global").append(addform);
			
			if(smartphone){
				$("table[rel='changepwd']").append("<tr><th>Mot de passe</th></tr><tr><td><input type='password' id='passwd' name='passwd' value=''/></td></tr>");
				$("table[rel='changepwd']").append("<tr><th>Répéter le mot de passe</th></tr><tr><td><input type='password' id='passwd2' name='passwd2' value=''/></td></tr>");
				
				$("table[rel='settings']").append("<tr><th>Trier les images par</th></tr><tr><td><select id='ordre' name='ordre'><option value='dateadd'>Date</option><option value='title'>Titre</option></select></td></tr>");
				$("table[rel='settings']").append("<tr><th>Tri</th></tr><tr><td><select id='ordre' name='ordre'><option value='ASC'>Croissant</option><option value='DESC'>Décroissant</option></select></td></tr>");
				$("table[rel='settings']").append("<tr><th>Nombre d'images</th></tr><tr><td><select id='step' name='step'><option value='10'>10</option><option value='20'>20</option><option value='50'>50</option></select></td></tr>");
			}else{
				$("table[rel='changepwd']").append("<tr><th>Mot de passe</th><td><input type='password' id='passwd' name='passwd' value=''/></td></tr>");
				$("table[rel='changepwd']").append("<tr><th>Répéter le mot de passe</th><td><input type='password' id='passwd2' name='passwd2' value=''/></td></tr>");
				
				$("table[rel='settings']").append("<tr><th>Trier les images par</th><td><select id='champ' name='champ'><option value='dateadd'>Date</option><option value='title'>Titre</option></select></td></tr>");
				$("table[rel='settings']").append("<tr><th>Tri</th><td><select id='ordre' name='ordre'><option value='ASC'>Croissant</option><option value='DESC'>Décroissant</option></select></td></tr>");
				$("table[rel='settings']").append("<tr><th>Nombre d'images</th><td><select id='step' name='step'><option value='10'>10</option><option value='20'>20</option><option value='50'>50</option></select></td></tr>");				
			}
			if(d.storage.albums > 1){ var nbalbums = " albums" }else{ var nbalbums = " album" }
			if(d.storage.images > 1){ var nbimg = " images" }else{ var nbimg = " image" }
			$("table[rel='stats']").append("<tr><th>"+d.storage.albums+nbalbums+"</th></tr>");
			$("table[rel='stats']").append("<tr><th>"+d.storage.images+nbimg+"</th></tr>");
			if(d.storage.quota == 0){
				$("table[rel='stats']").append("<tr><th>Utilisé "+byteConversion(d.storage.size,"M")+" Mo</th></tr>");
			}else{
				var percent = Math.floor((d.storage.size * 100) / (d.storage.quota * 1024 * 1024));
				$("table[rel='stats']").append("<tr><th>Utilisé "+byteConversion(d.storage.size,"M")+" / "+d.storage.quota+" Mo disponible</th></tr>");
				$("table[rel='stats']").append("<tr><th><div class='placebar'><div class='progress' style='width: "+percent+"%;'></div><span>"+percent+"%</span></div></th></tr>");
			}
		break;
	}
	$(".loader").fadeOut({complete:function(){
		$("#global").show();
		$(".overlay").hide();
		resizeBlocs();
	}});
}

function reloadJson(template){
	if(template === undefined){ template = false; }
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
		if(template != false){
			buildTemplate(template);
		}
	});
}

function byteConversion(bt,scale){
	if(scale === undefined){ scale = "M" }
	switch(scale){
		case "K":
			return Math.floor(bt/1024);
		break;
		
		case "M":
			return Math.floor(bt/1024/1024);
		break;
	}
}

function resizeBlocs(){
	var gwidth = $("#global").width();
	var gheight = $("#global").height();
	var wwidth = $(window).width();
	var wheight = $(window).height();
	console.log("Window Size: "+wwidth+"x"+wheight+"px");
	if(Math.round(wwidth) < 481){
		smartphone = true;
		console.log("Smartphone Mode activated ("+Math.round(wwidth)+")");
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

function uploadError(){
	console.log("Call function *uploadError*");
	$("#uploadoutput").html("Il y a eu une erreur lors de la réception de ton image. Essaie encore une fois!").removeClass().addClass("error").show();
	$(".overlay").hide();
	$(".loader").fadeOut();
}
function uploadSuccess(responseText, statusText, xhr, $form){
	console.log("Call function *uploadSuccess*");
	d = JSON.parse(responseText);
	$.post(rpcfile,{
		action: 'newimage',
		title: $("#title").val(),
		albumid: $("#album").val(),
		timestamp: d.timestamp,
		orientation: d.orientation,
		width: d.width,
		height: d.height
	},function(){
		$("#upload_form").resetForm();
		reloadJson();
		$("#uploadoutput").html("L'image a bien été récupérée! Elle se trouve désormais dans tes images.").removeClass().addClass("success").show();
		$(".overlay").hide();
		$(".loader").fadeOut();
	});
}
function uploadBeforeSubmit(){
	console.log("Call function *uploadBeforeSubmit*");
	$("#uploadoutput").html("").removeClass().hide();
	if(window.File && window.FileReader && window.FileList && window.Blob){
		var fsize = $('#file')[0].files[0].size;
		var ftype = $('#file')[0].files[0].type;
        /* Verifier le type de fichier */
		switch(ftype){
            case 'image/png': 
            case 'image/gif': 
            case 'image/jpeg':
            break;
			
            default:
				$("#uploadoutput").html("Le type de fichier '<b>"+ftype+"</b>' n'est pas autorisé!").addClass('warning').show();
				$(".overlay").hide();
				$(".loader").fadeOut();
				return false;
			break;
		}
       /* Verifier la taille du fichier */
       if(fsize>maxuploadsize){
         $("#uploadoutput").html("L'image est trop grosse!").addClass('warning').show();
		 $(".overlay").hide();
		 $(".loader").fadeOut();
         return false;
       }
	}else{
		$("#uploadoutput").html("Le navigateur devrait être mis à jour, il ne supporte pas les nouvelles version d'envoi de fichier.").addClass('warning').show();
		$(".overlay").hide();
		$(".loader").fadeOut();
		return false;
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
