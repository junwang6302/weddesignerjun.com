window.app = app = {}
app.urls =
	getArticles: "/webservice/getarticles"
  
getArtiles = ->
  $.ajax
    url: app.urls.getArticles+"?hash=456"
    type: "GET"
    dataType: "json"
    async: false
    success: (data) ->
      console.log data
      window.articles = data
      articles = data
      return articles
      
# getArtiles()

articleListApp = angular.module('articleListApp', [])

articleListApp.controller 'mainCtrl', ($scope) ->
  articles = getArtiles()
  window.articles1 = articles.responseJSON.articles
  $scope.articles = articles.responseJSON.articles
  return