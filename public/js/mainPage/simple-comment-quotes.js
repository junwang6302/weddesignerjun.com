var request = false;

function interpretRequestQuoteComments(){
	if(request.readyState == 4){
		if(request.status != 200){
			if(typeof console != 'undefined'){
				console.log("Fehler beim Zitieren.\nFehlercode: " + request.status);
				console.log(request);
			}
			else{
				textarea.value = textarea.value + 'Technischer Fehler beim zitieren :( Bitte versuche es erneut.';
			}
		}
		else{
			var content = request.responseText;
			var textarea = document.getElementById('comment');
			textarea.value = textarea.value + content;
		}
	}
	var loader = document.getElementById('simple_comment_quotes_loader');
	if(loader){
		loader.style.display = "none";
	}
}

function quoteComment(quote_ID){
	if(window.XMLHttpRequest){
		request = new XMLHttpRequest();
	}
	else if(window.ActiveXObject){
		request = new ActiveXObject('Microsoft.XMLHTTP');
	}
	if(request){
		var url = quoteCommentsSiteurl+"/wp-content/plugins/simple-comment-quotes/get-quote.php";
		var loader = document.getElementById('simple_comment_quotes_loader');
		if(loader){
			loader.style.display = "inline";
		}
		request.open('get', url + '?id=' + quote_ID, true);
		request.send();
		request.onreadystatechange = interpretRequestQuoteComments;
		return true;
	}
}
