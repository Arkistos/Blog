var buttonState = true;
var angle = 0;

document.getElementById("image").onclick = function rotateButton(){
var button = document.getElementById("image");
var i = 0;
	if(buttonState == true)
	{
		angle += 5;
		button.style.transform = "rotate("+ angle + "deg)";
		if(angle < 90)
		{
			requestAnimationFrame(rotateButton);
		}
		else
		{
			buttonState = false;
		}
	}
	else
	{
		angle -= 5;
		button.style.transform = "rotate("+ angle + "deg)";
		if(angle >0)
		{
			requestAnimationFrame(rotateButton);
		}
		else
		{
			buttonState = true;
		}
	}
}

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


//alert($choices.text());
document.getElementById("add_cat").onclick = function add()
{
	var $category = prompt("Enter the new category : ");
	if($category != null & $category != "")
	{
		var $route = 'http://127.0.0.1:8000/category/add/'.concat($category);
		axios.get($route).then(function(response){
			var $rp = response.data.message;
			
			$nbr = parseInt(response.data.message);
			$('#form_Category').append('<div id="form_Category"><div class="form-check"><input type="checkbox" id="form_Category_'+$nbr+'" name="form[Category][]" class="form-check-input" value="'+$nbr+'" /><label class="form-check-label" for="form_Category_0">' + $category + '</label></div><div class="form-check">');
		})
	}
}
	
