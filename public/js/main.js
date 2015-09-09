// settings
var animateTime = 2000;

var entry = {};

var searchFrom = $('#search_form');

// update entry
entry.update = function(id, name, url, text) {
    $.ajax({
        url: "get.php",
        type: "POST",
        data: {
            action: "update",
            id: id,
            name: name,
            url: url,
            text: text
        },
        success: function(data) {
            var message = "";
            var modalEdit = $('#edit_modal');
            var editClose = $('#edit');

            editClose.on('hide.bs.modal', function() {
                location.reload();
            });

            if(data === 'success') {
                message = "<h4 id='success_edit' class='alert alert-success text-center'>Запись успешно обновлена!</h4>";

                modalEdit.append(message);

                $('#success_edit').animate({
                    opacity: 0
                }, animateTime);
                setTimeout(function() {
                    $('#success_edit').remove();
                }, animateTime);
            } else {
                message = "<h4 class='alert alert-danger text-center' id='error_edit'>Ошибка обновления!</h4>";

                modalEdit.append(message);

                $('#error_edit').animate({
                    opacity: 0
                }, animateTime);
                setTimeout(function() {
                    $('#error_edit').remove();
                }, animateTime);
            }
        }
    });
};

// add new entry
entry.add = function(name, url, text) {
    $.ajax({
        url: "get.php",
        type: "POST",
        data: {
            name: name,
            url: url,
            text: text,
            action: "add"
        },
        success: function(data) {
            var message,
                addName = $('#add_name'),
                addUrl = $('#add_url'),
                addText = $('#add_text'),
                addModalWindow = $('#add_modal');

            if(data === 'success') {
                var loadingAdd = $('#loading_add');
                var afterLoadingAdd = $('#after-loading-add');

                loadingAdd.show();
                afterLoadingAdd.hide();

                // clear add form
                addName.val('');
                addUrl.val('');
                addText.val('');

                // hide loading window
                setTimeout(function() {
                    loadingAdd.hide();
                    afterLoadingAdd.show();

                    message = "<h4 id='success_add' class='alert alert-success text-center'>Запись успешно добавлена!</h4>";
                    addModalWindow.append(message);

                    $('#success_add').animate({
                        opacity: 0
                    }, animateTime);

                    setTimeout(function() {
                        $('#success_add').remove();
                    }, animateTime);
                }, 1000);
            } else {
                message = "<h4 id='error_add' class='alert alert-danger text-center'>Ошибка добавления записи!</h4>";

                addModalWindow.append(message);

                $('#error_add').animate({
                    opacity: 0
                }, animateTime);
                setTimeout(function() {
                    $('#error_add').remove();
                }, animateTime);
            }

            $('#add').on('hide.bs.modal', function() {
                location.reload();
            });
        }
    });
};

// search in database
entry.search = function(search) {
    $.ajax({
        url: "get.php",
        type: "GET",
        cache: false,
        data: {
            action: "search",
            text: search
        },
        success: function(data) {
            if(data !== 'error') {
                console.log(data);
            }
        },
        error: function() {
            console.log('search error...');
        }
    });
};

// get data for update entry
$('.edit-link').click(function() {
    var action = $(this).data('action');
    var id = $(this).data('id');

    $('#edit-form').submit(function(e) {
        e.preventDefault();

        var getName = $('#edit_name').val(),
            getUrl = $('#edit_url').val(),
            getText = $('#edit_text').val();

        if(getName !== "" && getUrl !== "" && getText !== "") {
            entry.update(id, getName, getUrl, getText);
        }
    });

    $.ajax({
        url: "get.php",
        type: "POST",
        data: {
            action: action,
            id: id
        },
        success: function(data) {
            $('#loading').hide();
            $('#after-loading').show();

            var entry = JSON.parse(data);

            var name = $("#edit_name");
            var url = $('#edit_url');
            var text = $('#edit_text');

            name.val(entry.name);
            url.val(entry.url);
            text.val(entry.text);
        }
    });

    var getEditModal = $('#edit');

    // clear edit form
    getEditModal.on('hide.bs.modal', function() {
        $('#edit_name').val('');
        $('#edit_url').val('');
        $('#edit_text').val('');
    });

    getEditModal.on('hidden.bs.modal', function() {
        $('#after-loading').hide();
        $('#loading').show();
    });
});

// add new entry
$('#add-entry').submit(function(e) {
    e.preventDefault();

    var name = $('#add-entry').serializeArray();

    var getName = $.trim(name[0].value),
        getUrl = $.trim(name[1].value),
        getText = $.trim(name[2].value);

    if(getName !== "" && getUrl !== "" && getText !== "") {
        entry.add(getName, getUrl, getText);
    }
});

// delete entry
$('.remove-link').click(function() {
    var action = $(this).data('action');
    var id = $(this).data('id');

    $('#remove').on('shown.bs.modal', function() {
        var answer = $('#yes');

        answer.click(function() {
            $.ajax({
                url: "get.php",
                type: "POST",
                data: {
                    action: action,
                    id: id
                },
                success: function(data) {
                    if(data === 'success') {
                        console.log(data);
                        $('#remove').modal('hide');
                        location.reload();
                    }
                }
            });
        });
    });
});

// login form
$('#login_form').submit(function(e) {
    e.preventDefault();

    var username = $("#username").val(),
        password = $("#password").val(),
        username = $.trim(username),
        password = $.trim(password);

    if(username !== "" && password !== "") {
        var errorMessage = $('#login_error_message');
        var message = '<h4 class="alert alert-danger text-center error_login">Доступ закрыт!</h4>';

        $.ajax({
            url: "get.php",
            type: "POST",
            data: {
                action: "auth",
                login: username,
                password: password
            },
            success: function(data) {
                if(data === "error") {
                    errorMessage.show();

                    errorMessage.append(message);

                    var errorMsg = $('.error_login');

                    errorMsg.animate({
                        opacity: 0
                    }, animateTime);

                    setTimeout(function() {
                        errorMessage.hide();
                        errorMsg.remove();
                    }, animateTime);
                } else {
                    location.reload();
                }
            }
        });
    }
});
