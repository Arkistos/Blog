

var buttonState = true;
var angle = 0;

document.getElementById("image").onclick = function rotateButton(){
	var button = document.getElementById("image");
	var i = 0;
	if(buttonState == true){
		angle += 5;
		button.style.transform = "rotate("+ angle + "deg)";
		if(angle < 90){
			requestAnimationFrame(rotateButton);
		}
		else{
			buttonState = false;
		}
	}
	else
	{
		angle -= 5;
		button.style.transform = "rotate("+ angle + "deg)";
		if(angle >0){
			requestAnimationFrame(rotateButton);
		}
		else{
			buttonState = true;
		}
	}
}


/***Newsletter***/
var $form = $('#getNewsletter');
var $mail;
var $comment;

$form.on('submit', function(e){
	e.preventDefault();
	$mail = $('#inputEmail').val();
	$mail = 'http://127.0.0.1:8000/setmail/'.concat($mail);
	axios.get($mail).then(function(response){
		$comment = response.data.message;
		$('#comment').text($comment);
	})
});


/***changement de Nom utilisateur***/
$('#editName').click(function() {
	var $name = prompt('Entrez votre nouveau nom d\'utilisateur');
	if($name != null & $name !=0)
	{
		var $route = 'http://127.0.0.1:8000/user/changeName/' + $name;
		axios.get($route).then(function(response){
			if(response.data.code === 200){
				$('#userName').text($name);
			}
		});
	}
});

/***changement de Mail***/
$('#editMail').click(function() {
	var $mail = prompt('Entrez votre nouvelle adresse email');
	var testMail = /[\w.-]+@[\w-]+\.\w{2,6}/;
	if($mail != null & $mail !=0 & testMail.test($mail) )
	{
		var $route = 'http://127.0.0.1:8000/user/changeMail/' + $mail;
		axios.get($route).then(function(response){
			if(response.data.code === 200){
				$('#userMail').text($mail);
			}
		});
	}
	else
	{
		alert('mot de passe invalide');
	}
});

/***changement de Mot de Passe***/
$('#editPassword').click(function() {
	var $mdp = prompt('Entrez votre nouveau mot de passe');
	
	if($mdp != null & $mdp !=0)
	{
		var $route = 'http://127.0.0.1:8000/user/changePassword/' + $mdp;
		axios.get($route).then(function(response){
			alert('Votre mot de passe à été mis à jour');
		});
	}
});

/***catégorie lors de l'écriture d'un post***/
if(document.getElementById("add_cat")){
	document.getElementById("add_cat").onclick = function add()
	{
		var $category = prompt("Enter the new category : ");
		if($category != null & $category != ""){
			var $route = 'http://127.0.0.1:8000/category/add/'.concat($category);
			axios.get($route).then(function(response){
				var $rp = response.data.message;
				$nbr = parseInt(response.data.message);
				$('#form_Category').append('<div id="form_Category"><div class="form-check"><input type="checkbox" id="form_Category_'+$nbr+'" name="form[Category][]" class="form-check-input" value="'+$nbr+'" /><label class="form-check-label" for="form_Category_0">' + $category + '</label></div><div class="form-check">');
			})
		}
	}
}

/***Affichage du nom de la photo uploader dans Add***/
var imgInput = document.getElementById('form_Image');
if (imgInput) {
	imgInput.onchange = function(){
		var nameFile =  imgInput.files[0].name.toUpperCase();
		if(imgInput.files[0].size>1048576){
			alert('Ce fichier et trop volumineux');
			imgInput.value = "";
		}
		else if(!/.PNG/.test(nameFile) & !/.JPG/.test(nameFile) & !/.JPEG/.test(nameFile)){
			alert('mauvaise extension');
			imgInput.value = "";
		}
		else{
			$('#imgName').text(imgInput.files[0].name);
		}
	}
}


/***changement de profile pic***/
var fileInput = document.getElementById('fileInput');

fileInput.onchange = function() {
	var formData = new FormData();

	formData.append('photo', fileInput.files[0], fileInput.files[0].name);

	var xhr = new XMLHttpRequest();
	xhr.open('POST', 'http://127.0.0.1:8000/user/setPhoto', true);
	xhr.onload = function(){
		if (xhr.status === 200) {
			var img = document.getElementById('profilePic');
			img.src = '/images/'+ fileInput.files[0].name;
		}
	};
	xhr.send(formData);
};
