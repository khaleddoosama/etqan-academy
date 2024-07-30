$(function () {
    var activeUploadRequest = null; // This will hold the current upload request

    async function uploadVideo() {
        const fileInput = document.getElementById('input-video');
        const file = fileInput.files[0];

        console.log('Starting upload process...');

        // Generate pre-signed URL
        const formData = new FormData();
        // formData.append('video', file);
        formData.append('title', $('#input-title').val());
        formData.append('section_id', $('#input-section_id').val());
        if ($('#input-thumbnail').prop('files').length !== 0) {
            formData.append('thumbnail', $('#input-thumbnail').prop('files')[0]);
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // csrf
        formData.append('_token', csrfToken);
        // activeUploadRequest = $.ajax({
        //     url: '/admin/upload-video',
        //     type: 'POST',
        //     data: formData,
        //     contentType: false,
        //     processData: false,
        //     success: function (response) {
        //         const uploadUrl = response.data.url;
        //         const filepath = response.data.filename;
        //         console.log('response:', response);
                // Upload video to S3 using the pre-signed URL
                activeUploadRequest = $.ajax({
                    url: "https://etqan-bucket.s3.eu-north-1.amazonaws.com/uploads/tkn-mal/s1-khtm/videos/1806029637787209.mp4?X-Amz-Content-Sha256=UNSIGNED-PAYLOAD&X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=AKIA47CR26RHQCY3RN4G%2F20240730%2Feu-north-1%2Fs3%2Faws4_request&X-Amz-Date=20240730T182800Z&X-Amz-SignedHeaders=host&X-Amz-Expires=1200&X-Amz-Signature=e78f952834ab5e479210aea7f78b5c3274e06f659c2ec7ad073b68411c071d55",
                    type: 'PUT',
                    data: file,
                    processData: false,
                    contentType: file.type,
                    xhr: function () {
                        const xhr = $.ajaxSettings.xhr();
                        $('#effect').show('blind');
                        var startTime = Date.now();
                        if (xhr.upload) {
                            xhr.upload.addEventListener("progress", function (
                                evt) {
                                // console.log(evt);
                                if (evt.lengthComputable) {
                                    var percentComplete = evt.loaded /
                                        evt.total;
                                    percentComplete = parseInt(
                                        percentComplete *
                                        100);
                                    var uploadedMB = (evt.loaded /
                                        1024 / 1024)
                                        .toFixed(
                                            2); // Convert bytes to MB
                                    var totalMB = (evt.total / 1024 /
                                        1024).toFixed(
                                            2); // Convert bytes to MB
                                    var elapsedTime = (Date.now() -
                                        startTime) /
                                        1000; // Calculate elapsed time in seconds
                                    var speedMbps = ((evt.loaded /
                                        elapsedTime) /
                                        1024 / 1024 * 8).toFixed(
                                            2); // Speed in Mbps
                                    $('#progressBar').width(
                                        percentComplete + '%');
                                    $('#progressText').html(
                                        percentComplete + '%'
                                    )
                                    $('#status p').html(
                                        `(${uploadedMB}MB of ${totalMB}MB)`
                                    );

                                }
                            }, false);
                            return xhr;
                        }


                    },
                    success: function (response) {
                        // Handle success
                        console.log('Success:', response);
                        $('#status p').html("Video uploaded successfully.");
                        // store lecture
                        // form data without video
                        formData.delete('video');
                        // put video name
                        formData.append('video_path', "asd.mp4");

                        $.ajax({
                            url: "/admin/lectures",
                            type: 'POST',
                            data: formData,
                            processData: false, // Prevent jQuery from automatically transforming the data into a query string
                            contentType: false, // Tell jQuery not to set any content type header
                            success: function (response) {
                                console.log('Lecture stored:', response);
                                $('#status p').html("Lecture stored successfully.");
                                // reload page
                                // wait 1 second
                                setTimeout(function () {
                                    location.reload();
                                }, 1000);
                            },
                            error: function (xhr, status, error) {
                                console.log('Upload error:', error);
                                console.log('XHR:', xhr);
                                console.log('Status:', status);
                                console.error('Failed to store lecture.');
                                $('#status p').html("Error storing lecture.");
                            }
                        });
                    },
                    error: function (xhr, status, error) {
                        console.log('Upload error:', error);
                        console.log('XHR:', xhr);
                        console.log('Status:', status);
                        console.error('Failed to upload video.');
                        $('#status p').html("Error uploading video.");
                    }
                });
            // },
            // error: function (xhr, status, error) {
            //     console.log(error);
            //     console.log(xhr);
            //     console.log(status);
            //     console.error('Failed to generate pre-signed URL.');
            //     $('#status p').html("Error generating pre-signed URL.");
            // }
        // });

    }
    // store lecture
    $("#effect").hide();

    $('#form1').validate({
        rules: {
            title: {
                required: true,
            },
            video: {
                required: true,
                accept: "video/*"
            }
        },
        messages: {
            title: {
                required: "{{ __('validation.required', ['attribute' => __('attributes.title')]) }}"
            },
            video: {
                required: "{{ __('validation.required', ['attribute' => __('attributes.video')]) }}",
                accept: "{{ __('validation.accept', ['attribute' => __('attributes.video')]) }}"
            }
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            error.css('padding', '0 7.5px');
            element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        }, // when everything is ok, send ajax request
        submitHandler: function (form) {
            console.log(11);
            uploadVideo();

        }

    });

    // cancel upload
    $('#cancelUpload').click(function () {
        if (activeUploadRequest) {
            console.log(activeUploadRequest);
            activeUploadRequest.abort(); // Abort the active request
            activeUploadRequest = null; // Reset the variable
            console.log('Upload canceled');
        }
        $("#effect").hide('blind');
        $('#progressBar').width('0%');
        $('#progressText').html('0%');
        $('#status p').html('');


        $('#form1').trigger("reset");
        $('#form1').validate().resetForm();
    });
});

