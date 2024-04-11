function initializeDropzone(formSelector, uploadUrl, sectionId, method = "POST") {

    var previewNode = document.querySelector(formSelector + " #template");
    previewNode.id = "";
    var previewTemplate = previewNode.parentNode.innerHTML;
    previewNode.parentNode.removeChild(previewNode);

    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    var dropzone = new Dropzone(formSelector, { // Specify the form selector here
        url: uploadUrl,

        maxFiles: 2,
        thumbnailWidth: 80,
        thumbnailHeight: 80,
        parallelUploads: 20,
        previewTemplate: previewTemplate,
        autoQueue: false,
        previewsContainer: formSelector + " #previews",
        clickable: formSelector + " .fileinput-button",
        acceptedFiles: 'video/*',
        headers: {
            "X-CSRF-TOKEN": csrfToken
        },
        paramName: "video",

    });

    dropzone.on("error", function (file, response) {
        // Optionally display an error message based on the response
        if (typeof response === 'string' || response instanceof String) {
            // Plain string error
            Toast.fire({
                icon: 'error',
                title: response
            })
        } else {
            // Laravel validation response (JSON)
            if (response.errors && Object.keys(response.errors).length > 0) {
                Toast.fire({
                    icon: 'error',
                    title: Object.values(response.errors).join('\n')
                })
            }
        }
    });

    dropzone.on("success", function (file, response) {
        console.log(response);
        Toast.fire({
            icon: 'success',
            title: response.message
        })
    });

    dropzone.on("addedfile", function (file) {
        // Hookup the start button
        file.previewElement.querySelector(".start").onclick = function () {
            dropzone.enqueueFile(file)
        }
    })

    dropzone.on("totaluploadprogress", function (progress) {
        document.querySelector("#total-progress .progress-bar").style.width = progress + "%"
    })

    dropzone.on("sending", function (file, xhr, formData) {
        var title = $(formSelector + ' #input-title').val();
        if (!title) {

            dropzone.removeFile(file); // Remove the file from Dropzone queue
            return; // Stop the function execution
        }
        formData.append("_method", method); // Spoofing PUT method

        formData.append("section_id", sectionId);
        formData.append("title", title);

        // Show the total progress bar when upload starts
        document.querySelector("#total-progress").style.opacity = "1"
        // And disable the start button
        file.previewElement.querySelector(".start").setAttribute("disabled", "disabled")
    });

    // Hide the total progress bar when nothing's uploading anymore
    dropzone.on("queuecomplete", function (progress) {
        document.querySelector("#total-progress").style.opacity = "0"
    })

    // Setup the buttons for all transfers
    document.querySelector("#actions .start").onclick = function () {
        dropzone.enqueueFiles(dropzone.getFilesWithStatus(Dropzone.ADDED))
    }
    document.querySelector("#actions .cancel").onclick = function () {
        dropzone.removeAllFiles(true)
    }

    return dropzone;
}


// DropzoneJS Demo Code Start
// Dropzone.autoDiscover = false

// Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
// var previewNode = document.querySelector("#template")
// previewNode.id = ""
// var previewTemplate = previewNode.parentNode.innerHTML
// previewNode.parentNode.removeChild(previewNode)

// var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
//     url: "{{ route('admin.lectures.store') }}", // Set the url
//     maxFiles: 1,
//     thumbnailWidth: 80,
//     thumbnailHeight: 80,
//     parallelUploads: 20,
//     previewTemplate: previewTemplate,
//     autoQueue: false, // Make sure the files aren't queued until manually added
//     previewsContainer: "#previews", // Define the container to display the previews
//     clickable: ".fileinput-button", // Define the element that should be used as click trigger to select files.
//     acceptedFiles: 'video/*', // Accept only video files
//     headers: {
//         "X-CSRF-TOKEN": csrfToken // Include CSRF token header
//     },
//     paramName: "video", // The name that will be used to transfer the file
//     // autoProcessQueue: false, // Prevent automatic upload to manually start it later

// })

// myDropzone.on("error", function (file, response) {
//     // Optionally display an error message based on the response
//     if (typeof response === 'string' || response instanceof String) {
//         // Plain string error
//         Toast.fire({
//             icon: 'error',
//             title: response
//         })
//     } else {
//         // Laravel validation response (JSON)
//         if (response.errors && Object.keys(response.errors).length > 0) {
//             Toast.fire({
//                 icon: 'error',
//                 title: Object.values(response.errors).join('\n')
//             })
//         }
//     }
// });

// on success
// myDropzone.on("success", function (file, response) {
//     Toast.fire({
//         icon: 'success',
//         title: response.message
//     })
// });


// myDropzone.on("addedfile", function (file) {
//     // Hookup the start button
//     file.previewElement.querySelector(".start").onclick = function () {
//         myDropzone.enqueueFile(file)
//     }
// })

// Update the total progress bar
// myDropzone.on("totaluploadprogress", function (progress) {
//     document.querySelector("#total-progress .progress-bar").style.width = progress + "%"
// })


// myDropzone.on("sending", function (file, xhr, formData) {
//     var title = $('#form1 #input-title').val();
//     if (!title) {

//         myDropzone.removeFile(file); // Remove the file from Dropzone queue
//         return; // Stop the function execution
//     }
//     formData.append("section_id", "{{ $section-> id}}");
//     formData.append("title", title);

//     // Show the total progress bar when upload starts
//     document.querySelector("#total-progress").style.opacity = "1"
//     // And disable the start button
//     file.previewElement.querySelector(".start").setAttribute("disabled", "disabled")
// });

// // Hide the total progress bar when nothing's uploading anymore
// myDropzone.on("queuecomplete", function (progress) {
//     document.querySelector("#total-progress").style.opacity = "0"
// })

// // Setup the buttons for all transfers
// // The "add files" button doesn't need to be setup because the config
// // `clickable` has already been specified.
// document.querySelector("#actions .start").onclick = function () {
//     myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED))
// }
// document.querySelector("#actions .cancel").onclick = function () {
//     myDropzone.removeAllFiles(true)
// }
// DropzoneJS Demo Code End
