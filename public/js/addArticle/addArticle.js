// Generated by CoffeeScript 1.7.1
(function() {
  var app, saveArticle;

  window.app = app = {};

  app.urls = {
    saveArticle: "/webservice/article"
  };

  saveArticle = function() {
    textEditor.post();
    return $.ajax({
      url: app.urls.saveArticle,
      type: 'POST',
      data: {
        hash: window.userHash,
        content: $('#input').val(),
        subject: $('#subject-input').val()
      },
      dataType: 'json',
      async: false,
      success: function(data) {
        alert("Your article has already saved successfully!");
        ({
          error: function(data) {
            return alert("Hey! There is something not up to date plz contact junwang6302@gmail.com.");
          }
        });
        return null;
      }
    });
  };

  $(".save-btn").on('click', function(e) {
    return saveArticle();
  });

}).call(this);
