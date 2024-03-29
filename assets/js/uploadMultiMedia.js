document
	.querySelectorAll(".form-row.row-upload-multi")
	.forEach(function (parent) {
		parent.addEventListener("click", function (e) {
			if (e.target.classList.contains("button-upload-multi")) {
				e.preventDefault();
				var type = e.target.getAttribute('data-type') ? e.target.getAttribute('data-type').split(',') : '';
				// Create the media frame
				var multiFileFrame = (wp.media.frames.Gallery = wp.media({
					multiple: "add",
					library: {
						type: type,
					},
				}));

				// When the media frame is opened
				multiFileFrame.on("open", function () {
					multiFileFrameOpen(parent, multiFileFrame);
				});

				// When media is selected
				multiFileFrame.on("select", function () {
					multiFileFrameSelect(parent, multiFileFrame);
				});

				// Open the media frame
				multiFileFrame.open();
			}
		});
	});

function multiFileFrameOpen(parent, multiFileFrame) {
	var images = parent.querySelector(".upload-input", parent).value.split(",");
	var selection = multiFileFrame.state().get("selection");

	images.forEach(function (id) {
		var attachment = wp.media.attachment(id);
		attachment.fetch();
		selection.add(attachment ? [attachment] : []);
	});
}

function multiFileFrameSelect(parent, multiFileFrame) {
	var gallery = parent.querySelector(".gallery-container");
	var library = multiFileFrame.state().get("selection");
	var imageURLs = [];
	var imageIDs = [];
	var imageURL;
	var outputHTML;
	var joinedIDs;

	gallery.innerHTML = "";

	library.map(function (image) {
		if (!image.id) {
			return;
		}
		image = image.toJSON();
		imageURLs.push(image.url);
		imageIDs.push(image.id);
		console.log(image);
		if (image.type == 'image') {
			imageURL = image.sizes.thumbnail.url;
			outputHTML =
				'<li class="selected-item">' +
				'<img data-pic-id="' +
				image.id +
				'" src="' +
				imageURL +
				'" />' +
				'<a class="option-btn button-delete dashicons dashicons-trash" data-tooltip="Delete" href="#"></a>' +
				"</li>";
		} else {
			imageURL = image.url;
			outputHTML =
				'<li class="selected-item">' +
				'<div class="img">' +
				'<img data-pic-id="' +
				image.id +
				'" src="/wp-includes/images/media/document.png" class="icon" draggable="false" alt="">' +
				"</div>" +
				"<div class='filename'>" +
				image.filename +
				"</div>" +
				'<a class="option-btn button-delete dashicons dashicons-trash" data-tooltip="Delete" href="#"></a>' +
				"</li>";
		}


		gallery.insertAdjacentHTML("beforeend", outputHTML);
	});

	joinedIDs = imageIDs.join(",").replace(/^,*/, "");
	if (joinedIDs.length !== 0) {
		parent.classList.remove("empty");
	}

	fillInput(parent, joinedIDs);
}

/**
 * Click | Remove single
 */
document.addEventListener("click", function (e) {
	if (e.target.classList.contains("button-delete")) {
		e.preventDefault();

		var parent = e.target.closest(".form-row.row-upload-multi");
		var selectedImage = e.target.closest(".selected-item");

		if (selectedImage) {
			selectedImage.remove();

			var joinedIDs = findAllIDs(parent);

			if (joinedIDs === "") {
				parent.classList.add("empty");
			}

			fillInput(parent, joinedIDs);
		}
	}
});

/**
 * Click | Remove all
 */
document.addEventListener("click", function (e) {
	if (e.target.classList.contains("button-delete-all")) {
		e.preventDefault();

		var parent = e.target.closest(".form-row.row-upload-multi");

		parent.classList.add("empty");

		document.querySelector(".upload-input", parent).value = "";
		document.querySelector(".gallery-container", parent).innerHTML = "";
	}
});

/**
 * Helper method. Find all IDs of added images.
 * @method findAllIDs
 * @return {String}		joined ids separated by `,`
 */
function findAllIDs(parent) {
	var imageIDs = [];
	var images = parent.querySelectorAll(".gallery-container img");

	images.forEach(function (img) {
		var id = img.getAttribute("data-pic-id");
		imageIDs.push(id);
	});

	return imageIDs.join(",");
}

function fillInput(parent, joinedIDs) {
	parent.querySelector(".upload-input").value = joinedIDs;
}
