window.app = app = {}
app.urls =
	getArticle: "/webservice/article"
  
getArtile = ->
  $.ajax
    url: app.urls.getArticle+"?hash=456&articleid="+window.article_id
    type: "GET"
    dataType: "json"
    async: false
    success: (data) ->
      console.log data
      window.article = data
      article = data
      return article
      
# getArtiles()

articleApp = angular.module('articleApp', [])

articleApp.controller 'mainCtrl', ($scope) ->
  articleData = getArtile()
  window.article1 = articleData.responseJSON.article
  $scope.article = articleData.responseJSON.article
  $('.article-container .article .content').html articleData.responseJSON.article.content
  return