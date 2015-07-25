window.app = app = {}
app.urls =
	saveArticle: "/webservice/article"
saveArticle = ->
	textEditor.post()
  # console.log $('#input').val()
  # console.log 'user hash: '+window.userHash
	$.ajax
		url: app.urls.saveArticle
		type: 'POST'
		data: {
		  hash: window.userHash
		  content: $('#input').val()
			subject: $('#subject-input').val()
			tags: $('#tag-input').val()
		}
		dataType: 'json'
		async: false
		success: (data) ->
      # console.log data
      # console.log 'successfully update'
			alert "Your article has already saved successfully!"
			error: (data) ->
				# console.log data
				alert "Hey! There is something not up to date plz contact junwang6302@gmail.com."
  	return null

$(".save-btn").on 'click', (e) ->
	saveArticle()
	