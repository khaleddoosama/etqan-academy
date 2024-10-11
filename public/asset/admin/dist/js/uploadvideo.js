$(function () {
    var activeUploadRequest = null; // This will hold the current upload request
    // check if there in page input have id = input-id if it exist then make var isupdate = true else make it false
    var isUpdate = document.getElementById('input-id') ? true : false;

    async function uploadVideo() {
        const fileInput = document.getElementById('input-video');
        const file = fileInput.files[0];

        console.log('Starting upload process...');


        const formData = createFormData();


        try {
            if (file) {
                $('#effect').show('blind');
                // scroll to the top of the modal to show the progress bar
                $('.modal.fade.show').animate({ scrollTop: 0 }, 'slow');

                window.scrollTo(
                    {
                        top: 0,
                        behavior: 'smooth'
                    });


                console.log('Uploading file...');
                const preSignedUrlResponse = await postFormData('/admin/upload-video', formData);
                console.log('Pre-signed URL:', preSignedUrlResponse);
                await uploadFileToS3(preSignedUrlResponse.data.url, file);
                console.log('File uploaded successfully.');
                if (isUpdate) {
                    await updateLecture(formData, preSignedUrlResponse.data.filename);
                    console.log('Lecture updated successfully.');
                } else {
                    await storeLecture(formData, preSignedUrlResponse.data.filename);
                    console.log('Lecture stored successfully.');
                }
            } else {
                console.log('No file selected.');
                await updateLecture(formData);
                console.log('Lecture updated successfully.');
            }
        } catch (error) {
            console.error('Error:', error);
            document.querySelector('#status p').innerText = error.message;
        } finally {
            activeUploadRequest = null; // Reset after the upload process completes
        }
    }

    function createFormData() {
        const formData = new FormData();
        formData.append('title', document.getElementById('input-title').value);
        formData.append('section_id', document.getElementById('input-section_id').value);

        if (document.getElementById('input-id')) {
            console.log('found');
            formData.append('id', document.getElementById('input-id').value);
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        formData.append('_token', csrfToken);

        return formData;
    }

    async function postFormData(url, formData) {
        const response = await fetch(url, {
            method: 'POST',
            body: formData,
        });
        const result = await response.json();
        if (!response.ok) {
            toastr.error(result.message);
            throw new Error('Failed to generate pre-signed URL.');
        }

        return result;
    }

    async function uploadFileToS3(uploadUrl, file) {
        const xhr = new XMLHttpRequest();
        xhr.open('PUT', uploadUrl, true);
        xhr.setRequestHeader('Content-Type', file.type);

        activeUploadRequest = xhr; // Assign the active upload request

        return new Promise((resolve, reject) => {
            xhr.upload.addEventListener('progress', updateProgress);
            xhr.onload = () => {
                if (xhr.status === 200) {
                    console.log('File uploaded successfully.');
                    document.querySelector('#status p').innerText = 'Video uploaded successfully.';
                    resolve();
                } else {
                    reject(new Error('Failed to upload video.'));
                }
            };
            xhr.onerror = () => reject(new Error('Network error during file upload.'));
            xhr.send(file);
        });
    }

    function updateProgress(event) {
        const startTime = Date.now();
        if (event.lengthComputable) {
            const percentComplete = Math.round((event.loaded / event.total) * 100);
            const uploadedMB = (event.loaded / 1024 / 1024).toFixed(2);
            const totalMB = (event.total / 1024 / 1024).toFixed(2);
            const speedMbps = ((event.loaded / ((Date.now() - startTime) / 1000)) / 1024 / 1024 * 8).toFixed(2);

            document.querySelector('#progressBar').style.width = `${percentComplete}%`;
            document.querySelector('#progressText').innerText = `${percentComplete}%`;
            document.querySelector('#status p').innerText = `(${uploadedMB}MB of ${totalMB}MB)`;
        }
    }

    async function storeLecture(formData, filepath) {
        formData.append('video_path', filepath);
        formData.append('description', document.getElementById('summernote').value);

        const thumbnailInput = document.getElementById('input-thumbnail');
        if (thumbnailInput.files.length !== 0) {
            formData.append('thumbnail', thumbnailInput.files[0]);
        }

        const attachmentsInput = document.getElementById('input-attachments');
        if (attachmentsInput.files.length !== 0) {
            for (let i = 0; i < attachmentsInput.files.length; i++) {
                formData.append('attachments[]', attachmentsInput.files[i]);
            }
        }


        const response = await fetch('/admin/lectures', {
            method: 'POST',
            body: formData,
        });
        if (!response.ok) throw new Error('Failed to store lecture.');

        const result = await response.json();
        // console.log('Lecture stored:', result);
        document.querySelector('#status p').innerText = 'Lecture stored successfully.';
        setTimeout(() => location.reload(), 1000);
    }

    async function updateLecture(formData, filepath = null) {
        if (filepath) {
            formData.append('video_path', filepath);
        }
        formData.append('description', document.getElementById('summernote').value);

        const thumbnailInput = document.getElementById('input-thumbnail');
        if (thumbnailInput.files.length !== 0) {
            formData.append('thumbnail', thumbnailInput.files[0]);
        }

        const attachmentsInput = document.getElementById('input-attachments');
        if (attachmentsInput.files.length !== 0) {
            for (let i = 0; i < attachmentsInput.files.length; i++) {
                formData.append('attachments[]', attachmentsInput.files[i]);
            }
        }

        // for (const pair of formData.entries()) {
        //     console.log(pair[0] + ', ' + pair[1]);
        // }
        formData.append('_method', 'PUT');
        const response = await fetch('/admin/lectures/' + document.getElementById('input-id').value, {
            method: 'POST',
            body: formData,
        });
        const result = await response.json();
        if (!response.ok) {
            toastr.error(result.message);
            throw new Error('Failed to update lecture.');
        }

        document.querySelector('#status p').innerText = 'Lecture updated successfully.';
        setTimeout(() => location.reload(), 1000);
    }
    // store lecture
    $("#effect").hide();

    $('#form1').validate({
        rules: {
            title: {
                required: true,
            },
            video: {
                required: isUpdate ? false : true,
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
            activeUploadRequest.abort(); // Abort the active request
            activeUploadRequest = null; // Reset the variable
            console.log('Upload canceled');
        }
        $("#effect").hide('blind');
        $('#progressBar').width('0%');
        $('#progressText').html('0%');
        $('#status p').html('');


        // $('#form1').trigger("reset");
        // $('#form1').validate().resetForm();
    });

    window.onbeforeunload = function() {
        if (activeUploadRequest) {
            return 'Are you sure you want to leave this page? Your changes will be lost.';
        }
    };
});
