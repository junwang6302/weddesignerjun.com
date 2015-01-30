// Generated by CoffeeScript 1.7.1
(function() {
  var app, saveArticle;

  window.app = app = {};

  app.urls = {
    saveArticle: "/webservice/article"
  };

  saveArticle = function() {
    textEditor.post();
    console.log($('#input').val());
    console.log('user hash: ' + window.userHash);
    $.ajax({
      url: app.urls.saveArticle,
      type: 'POST',
      data: {
        hash: window.userHash,
        content: $('#input').val(),
        subject: 'front end test'
      },
      dataType: 'json',
      async: true
    });
    return {
      success: (function(_this) {
        return function(data) {
          return console.log(data);
        };
      })(this),
      error: (function(_this) {
        return function(data) {
          console.log(data);
          return null;
        };
      })(this)
    };
  };

  $(".save-btn").on('click', function(e) {
    return saveArticle();
  });

}).call(this);
