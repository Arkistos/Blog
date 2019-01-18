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
