
document.querySelectorAll('.lecture-list').forEach(list => {
    new Sortable(list, {
        group: 'shared-lectures',
        animation: 150,
        onEnd: function (evt) {
            const lectureId = evt.item.dataset.id;
            const newSectionId = evt.to.dataset.sectionId;

            const newOrder = Array.from(evt.to.children).map(el => el.dataset.id);

            $.ajax({
                url: window.Laravel.routes.reassignAndSortLecture,
                method: 'POST',
                data: {
                    _token: window.Laravel.csrfToken,
                    lecture_id: lectureId,
                    new_section_id: newSectionId,
                    new_order: newOrder
                },
                success: function (res) {
                    toastr.success('Lecture moved and reordered');
                },
                error: function (err) {
                    console.error(err);
                    toastr.error('Error occurred');
                }
            });
        }
    });
});



document.querySelectorAll('.section-list').forEach(list => {
    new Sortable(list, {
        group: 'shared-sections',
        animation: 150,
        fallbackOnBody: true,
        swapThreshold: 0.65,
        onEnd: function (evt) {
            const sectionId = evt.item.dataset.id;
            const newParentId = evt.to.dataset.parentId;

            const newOrder = Array.from(evt.to.children).map(el => el.dataset.id);

            $.ajax({
                url: window.Laravel.routes.reassignAndSortSection,
                method: 'POST',
                data: {
                    _token: window.Laravel.csrfToken,
                    section_id: sectionId,
                    new_parent_id: newParentId,
                    new_order: newOrder
                },
                success: function (res) {
                    toastr.success('Section moved and reordered');
                },
                error: function (err) {
                    console.error(err);
                    toastr.error('Error occurred');
                }
            });
        }
    });
});


$(document).ready(function () {
    $('#collapseAll').hide();
    $('#expandAll').click(function () {
        $('.collapse').toggle('show');
        $('#collapseAll').show();
        $('#expandAll').hide();
    });
    $('#collapseAll').click(function () {
        $('.collapse').toggle('show');
        $('#expandAll').show();
        $('#collapseAll').hide();
    });


    $('.collapse').on('hide.bs.collapse', function (e) {
        if (e.target === this) {
            $(this).closest('.card').find('.toggle-icon').first().css('transform', 'rotate(0deg)');
        }
    });

    $('.collapse').on('show.bs.collapse', function (e) {
        if (e.target === this) {
            $(this).closest('.card').find('.toggle-icon').first().css('transform', 'rotate(180deg)');
        }
    });

});




// when upload attachments show the attachments preview
$('#input-attachments').change(function () {
    console.log('attachments');
    $('#showAttachments').show('blind');

    // remove old preview
    $('#showAttachments').empty();

    // get the files
    var files = this.files;

    for (var i = 0; i < files.length; i++) {
        var file = files[i];
        var reader = new FileReader();
        reader.onload = function (e) {
            var preview = document.createElement('div');
            preview.classList.add('col-md-6');
            preview.classList.add('col-12');
            preview.classList.add('my-3');
            let type = e.target.result.split(':')[1].split('/')[0];

            if (type == 'image') {
                preview.innerHTML = '<img class="img-thumbnail img-fluid" src="' + e.target.result +
                    '"/>';
            } else if (type == 'video') {
                preview.innerHTML = '<video style="width: 100%; height: 100%;" controls src="' + e
                    .target.result + '" >';
            } else if (type == 'audio') {
                preview.innerHTML =
                    '<audio style="width: 100%; height: 100%;" controls class="audio"  src="' + e.target
                        .result + '" >';
            } else if (type == 'application') {
                preview.innerHTML = '<iframe style="width: 100%; height: 100%;" src="' + e.target
                    .result + '" >' + '</iframe>';
            } else {
                console.log(e.target);
                preview.innerHTML = '<a class="text-primary" href="' + e.target.result +
                    '" target="_blank">View attachment</a>';
            }
            $('#showAttachments').append(preview);
        }
        reader.readAsDataURL(file);
    }

});

// show video from youtube
$('#input-video').change(function () {
    var videoId = $(this).val();
    var videoUrl = 'https://www.youtube.com/embed/' + videoId + '?enablejsapi=1';
    $('#showVideo').show('blind');
    $('#showVideo').attr('src', videoUrl);
});



$('#form1').validate({
    rules: {
        title: {
            required: true,
        },
        video: {
            required: true,
        }
    },
    messages: {
        title: {
            required: window.Laravel.messages.requiredTitle
        },
        video: {
            required: window.Laravel.messages.requiredVideo
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
    },


});



$(function () {
    $('#accordion-lectures').sortable({
        update: function (event, ui) {
            var lectureOrder = [];
            $('#accordion-lectures .card-lecture').each(function (index) {
                lectureOrder.push($(this).data('id'));
            });

            $.ajax({
                url: window.Laravel.routes.lecturesUpdateOrder,
                method: 'Post',
                data: {
                    lectures: lectureOrder,
                    _token: window.Laravel.csrfToken
                },
                success: function (response) {
                    // toastr.success
                    toastr.success('Order updated successfully');
                    //btnn rewrite the order
                    $('#accordion-lectures .card-lecture').each(function (index) {
                        $(this).find('.btnn').text(
                            `${window.Laravel.messages.video} #${index + 1}: ${$(this).find('.btnn').text().split(':')[1]}`
                        );
                    });
                },
                error: function (xhr, status, error) {
                    console.log(error);
                    console.log(xhr);
                }
            });
        }
    });

});



$(document).ready(function () {
    $('#getVideoModal').on('shown.bs.modal', function () {
        $('#input-get-course').select2({
            dropdownParent: $('#getVideoModal')
        });
        $('#input-get-section').select2({
            dropdownParent: $('#getVideoModal')
        });
        $('#input-get-lecture').select2({
            dropdownParent: $('#getVideoModal')
        });
    });
});



$('#input-get-course').change(function () {
    var course_id = $(this).val();
    if (course_id) {
        $.ajax({

            url: window.Laravel.routes.getSectionsByCourse.replace(':course_id', course_id),
            type: 'Get',
            success: function (data) {
                $('#div-get-setion').show('blind');
                $options =
                    `<option selected disabled>${window.Laravel.messages.choose}</option>`;
                data.data.forEach(element => {
                    $options +=
                        `<option value="${element.id}">${element.title}</option>`;
                });
                $('#input-get-section').html($options);
            }
        });
    } else {
        $('#div-get-setion').hide('blind');
        $('#div-get-lecture').hide('blind');
    }
});

$('#input-get-section').change(function () {
    var section_id = $(this).val();
    if (section_id) {
        $.ajax({
            url: window.Laravel.routes.getLecturesBySection.replace(':section_id', section_id),
            type: 'Get',
            success: function (data) {
                $('#div-get-lecture').show('blind');
                $options =
                    `<option selected disabled>${window.Laravel.messages.choose}</option>`;
                data.data.forEach(element => {
                    $options +=
                        `<option value="${element.id}">${element.title}</option>`;
                });
                $('#input-get-lecture').html($options);
            }
        });
    } else {
        $('#div-get-lecture').hide('blind');
    }
});



$('#getSectionModal-input-get-course').change(function () {
    var course_id = $(this).val();

    if (course_id) {
        // Show loader and hide the section dropdown until data arrives
        $('#getSectionModal-loader').show();
        $('#getSectionModal-div-get-setion').hide();

        $.ajax({
            url: window.Laravel.routes.getSectionsByCourse.replace(':course_id', course_id),
            type: 'GET',
            success: function (data) {
                $('#getSectionModal-loader').hide();
                $('#getSectionModal-div-get-setion').show('blind');

                let $options = `<option selected disabled>${window.Laravel.messages.choose}</option>`;
                data.data.forEach(element => {
                    $options += `<option value="${element.id}">${element.title}</option>`;
                });
                $('#getSectionModal-input-get-section').html($options);
            },
            error: function (xhr, status, error) {
                console.log(xhr);
                console.log(status);
                console.log(error);
                $('#getSectionModal-loader').hide();
                toastr.error('Error occurred'.error);
            }
        });
    } else {
        $('#getSectionModal-div-get-setion').hide('blind');
    }
});

// selectAllLectures
$('#selectAllLectures').click(function () {
    if ($(this).is(':checked')) {
        $('.lecture-checkbox').prop('checked', true);
    } else {
        $('.lecture-checkbox').prop('checked', false);
    }
});

$('#selectAllSections').click(function () {
    if ($(this).is(':checked')) {
        $('.section-checkbox').prop('checked', true);
    } else {
        $('.section-checkbox').prop('checked', false);
    }
});

document.getElementById('delete-selected').addEventListener('click', function () {
    const lectureIds = Array.from(document.querySelectorAll('.lecture-checkbox:checked')).map(cb => cb.value);
    const sectionIds = Array.from(document.querySelectorAll('.section-checkbox:checked')).map(cb => cb.value);

    if (lectureIds.length === 0 && sectionIds.length === 0) {
        return toastr.warning('من فضلك اختر محاضرات أو أقسام للحذف');
    }

    // show confirmation dialog
    Swal.fire({
        title: 'تاكيد الحذف',
        text: 'هل تريد حذف المحاضرات والأقسام المحددة؟',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'حذف',
        cancelButtonText: 'الغاء',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            if (sectionIds.length > 0) {
                $.ajax({
                    url: window.Laravel.routes.sectionsBulkDelete,
                    method: 'POST',
                    data: {
                        _token: window.Laravel.csrfToken,
                        ids: sectionIds,
                    },
                    success: function (res) {
                        toastr.success('تم الحذف بنجاح');
                    },
                    error: function () {
                        toastr.error('حدث خطأ أثناء الحذف');
                    }
                });
            }

            if (lectureIds.length > 0) {
                $.ajax({
                    url: window.Laravel.routes.lecturesBulkDelete,
                    method: 'POST',
                    data: {
                        _token: window.Laravel.csrfToken,
                        ids: lectureIds,
                    },
                    success: function (res) {
                        toastr.success('تم الحذف بنجاح');
                    },
                    error: function () {
                        toastr.error('حدث خطأ أثناء الحذف');
                    }
                });
            }
            window.location.reload();
        }
    })


});
