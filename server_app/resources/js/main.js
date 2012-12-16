function toggleDisplay(id) {
	if (document.getElementById(id+'_sub').className == 'visible') {
		document.getElementById(id+'_sub').className = 'invisible';
		document.getElementById(id+'_link').style.backgroundColor = '';
		document.getElementById(id+'_link').style.borderBottomColor = '#333';
	}
	else if (document.getElementById(id+'_sub').className == 'invisible') {
		document.getElementById(id+'_sub').className = 'visible';
		document.getElementById(id+'_link').style.backgroundColor = '#222';
		document.getElementById(id+'_link').style.borderRadius = '3px 3px 0 0';
		document.getElementById(id+'_link').style.borderBottomColor = '#222';
	}
}

document.getElementById('login').onmouseover = function() {
	toggleDisplay('login');
}
document.getElementById('login').onmouseout = function() {
	toggleDisplay('login');
}