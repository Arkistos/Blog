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
