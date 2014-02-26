function popitup(url) {
	newwindow=window.open(url,'Preview','scrollbars=yes,width=800');
	if (window.focus) {newwindow.focus()}
	return false;
}