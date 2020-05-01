function ToggleVisibilityByClass(myClass) 
{
	console.log('ToggleVisibilityByClass');
	var x = document.getElementsByClassName(myClass);
	console.log(x);
	for (var i = 0; i < x.length; i++)
	{
		if(x[i].style.display == "none")
		{
			x[i].style.display = x[i].dataset.display;
		}
		else
		{
			x[i].dataset.display = x[i].style.display;
			x[i].style.display = "none";
		}
	}
}

function ToggleVisibilityByID(myID) 
{
	console.log('ToggleVisibilityByID');
	var x = document.getElementById(myID);
	console.log(x);
	if(x.style.display == "none")
	{
		x.style.display = x.dataset.display;
	}
	else
	{
		x.dataset.display = x.style.display;
		x.style.display = "none";
	}
}

function confirmDelete()
{
    return confirm('Are you sure?');
}