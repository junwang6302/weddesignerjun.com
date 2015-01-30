window.app = app = {}
app.urls =
	saveArticle: "/webservice/article"
saveArticle = ->
	textEditor.post()
	console.log $('#input').val()
	console.log 'user hash: '+window.userHash
	$.ajax
		url: app.urls.saveArticle
		type: 'POST'
		data: {
		  hash: window.userHash
		  content: $('#input').val()
			subject: 'front end test'
		}
		dataType: 'json'
		async: true
	success: (data) =>
		console.log data
	error: (data) =>
		console.log data
  return null

$(".save-btn").on 'click', (e) ->
	saveArticle()
	