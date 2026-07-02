document.addEventListener("DOMContentLoaded", function () {
  var mediaUploadButtons = document.querySelectorAll(".js-media-upload");

  mediaUploadButtons.forEach(function (button) {
    button.addEventListener("click", function (e) {
      e.preventDefault();

      var field = button.getAttribute("data-field");
      var input = document.getElementById(field);
      var previewWrap = document.getElementById(field + "-preview-wrap");
      var preview = document.getElementById(field + "-preview");

      var mediaUploader = (wp.media.frames.file_frame = wp.media({
        title: "Choose Media",
        multiple: false,
      }));

      mediaUploader.on("select", function () {
        var attachment = mediaUploader.state().get("selection").first().toJSON();

        input.value = attachment.url;

        if (preview && previewWrap) {
          previewWrap.classList.add("is-visible");
          previewWrap.classList.add("is-loading");

          // Une fois l'image chargée, on masque le loader
          preview.onload = function () {
            previewWrap.classList.remove("is-loading");
          };

          preview.onerror = function () {
            previewWrap.classList.remove("is-loading");
            previewWrap.classList.remove("is-visible");
          };

          preview.src = attachment.url;
        }
      });

      mediaUploader.open();
    });
  });
});
