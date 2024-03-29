document.addEventListener("DOMContentLoaded", function () {
	var mediaUploadButtons = document.querySelectorAll(".js-media-upload");

	mediaUploadButtons.forEach(function (button) {
		button.addEventListener("click", function (e) {
			e.preventDefault();

			var field = button.getAttribute("data-field");
			var mediaUploader = (wp.media.frames.file_frame = wp.media({
				title: "Choose Media",
				multiple: false,
			}));

			mediaUploader.on("select", function () {
				var attachment = mediaUploader
					.state()
					.get("selection")
					.first()
					.toJSON();
				document.getElementById(field).value = attachment.url;
			});

			mediaUploader.open();
		});
  });
});


