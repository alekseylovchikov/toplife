<?php

require("../includes/start.php");

require_once("../includes/entry.class.php");

$entry = new Entry();

?>

<!-- HEADER -->
<?php include("../includes/templates/header.php"); ?>

    <!-- BEGIN BODY -->
    <article class="container">
        <div id="search" class="search">
            <form id="search_form" action="index.php" method="get">
                <div class="row">
                    <div class="col-md-8">
                        <label>Поиск</label>
                        <p><input class="form-control" name="search" type="search" placeholder="Поиск" autocomplete="off" /></p>
                        <button type="submit" class="btn btn-primary">Найти</button>
                    </div>
                    <div id="buttons" class="text-right">
                        <div class="col-md-4">
                            <?php

                            if($entry->is_admin()) {
                                echo "<a class=\"btn btn-success\" data-toggle=\"modal\" data-target=\"#add\" href=\"#add\">Добавить</a>";
                            }

                            ?>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <table id="after_table_loading" class="table table-striped">
            <?php

            // get order for sort
            if(isset($_GET["order"])) {
                $get_order = ($_GET["order"] == "up") ? "down" : "up";
            } else {
                $get_order = "up";
            }

            // generate link for sort
            $link = "order={$get_order}";

            ?>
            <thead>
                <tr>
                    <th class="col-md-2"><a id="reset-sort" href="?" title="Сбросить сортировку">ID</a></th>
                    <th class="col-md-2"><a href="?sort=name&<?=$link?>" title="Сортировать по имени проекта"><span class="glyphicon glyphicon-chevron-up"></span><span class="glyphicon glyphicon-chevron-down"></span></a> Название проекта</th>
                    <th class="col-md-2"><a href="?sort=url&<?=$link?>" title="Сортировать по URL"><span class="glyphicon glyphicon-chevron-up"></span><span class="glyphicon glyphicon-chevron-down"></span></a> URL</th>
                    <th>Доступы</th>
                    <?php

                    // check - is admin
                    if($entry->is_admin()) {
                        echo "<th class=\"text-right\">Управление</th>";
                    }

                    ?>
                </tr>
            </thead>
            <?php

            // get page number
            if(isset($_GET["page"]) && !empty($_GET["page"])) {
                $page_number = filter_var($_GET["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH);

                if(!is_numeric($page_number)) {
                    die("Неверный номер страницы!");
                }

                if($page_number < 1) {
                    $page_number = 1;
                }
            } else {
                $page_number = 1;
            }

            // if search do this
            if(isset($_GET["search"]) && !empty($_GET["search"])) {
                $search = $_GET["search"];
                $search = trim($search);
                $search = strip_tags($search);

                $entry->search($search);
            } else {
                if(isset($_GET["sort"]) && isset($_GET["order"]) && ($_GET["sort"] == "name" || $_GET["sort"] == "url") && ($_GET["order"] == "up" || $_GET["order"] == "down")) {
                    $sort = $_GET["sort"];
                    $order = $_GET["order"];

                    $entry->get_result($page_number, $sort, $order);
                } else {
                    $entry->get_result($page_number, "", "");
                }
            }

            ?>
        </table>

        <!-- begin pagination -->
        <div class="text-center">
            <nav>
                <ul class="pagination pagination-sm">
                    <?php

                    if(empty($_GET["search"])) {
                        echo $entry->pagination($page_number);
                    }

                    ?>
                </ul>
            </nav>
        </div>
        <!-- end pagination -->

        <!-- BEGIN MODAL WINDOWS -->
        <!-- add modal -->
        <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Добавить доступ</h4>
                    </div>
                    <!-- for messages -->
                    <div id="add_modal"></div>
                    <div id="errors_add"></div>
                    <!-- end for messages -->
                    <form id="add-entry">
                        <div class="modal-body" id="loading_add" style="display: none;">
                            <h4 class="alert alert-info text-center">Загрузка...</h4>
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                                    <span class="sr-only">100% Complete</span>
                                </div>
                            </div>
                        </div>
                        <div id="after-loading-add" class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Название</label>
                                    <input id="add_name" data-validation="length alphanumeric" data-validation-length="min1" class="form-control" name="name" type="text" placeholder="Название" />
                                </div>
                                <div class="col-md-6">
                                    <label>URL</label>
                                    <input id="add_url" class="form-control" data-validation="url length" data-validation-length="min1" name="url" type="text" placeholder="URL" />
                                </div>
                                <div class="col-md-12">
                                    <label>Доступы</label>
                                    <textarea id="add_text" style="max-width: 570px; min-width: 570px; min-height: 100px;" name="text" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="submit" class="btn btn-success">Добавить</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- edit modal -->
        <div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Редактировать доступ</h4>
                    </div>

                    <!-- for messages -->
                    <div id="edit_modal"></div>
                    <!-- end for messages -->

                    <form id="edit-form">
                        <div class="modal-body" id="loading">
                            <h4 class="alert alert-info text-center">Загрузка...</h4>
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                                    <span class="sr-only">100% Complete</span>
                                </div>
                            </div>
                        </div>
                        <div id="after-loading" class="modal-body" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="edit_name">Название</label>
                                    <input id="edit_name" class="form-control" type="text" placeholder="Название проекта" />
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_url">URL</label>
                                    <input id="edit_url" class="form-control" type="text" placeholder="URL" />
                                </div>
                                <div class="col-md-12">
                                    <label for="edit_text">Доступы</label>
                                    <textarea id="edit_text" style="max-width: 570px; min-width: 570px; min-height: 100px;" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" id="update" class="btn btn-success">Обновить</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- remove modal -->
        <div class="modal fade" id="remove" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Удаление записи</h4>
                    </div>
                    <div class="modal-body">
                        <h4 class="alert alert-danger">Вы уверены?</h4>
                    </div>
                    <div id="answer" class="modal-footer">
                        <button type="button" id="yes" class="btn btn-success">Да</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Нет</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- login modal -->
        <div class="modal fade" id="login" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Войти</h4>
                    </div>
                    <div class="modal-body">
                        <?php if($entry->is_admin()) { ?>
                            Hello, username!
                        <?php } else { ?>
                            <!-- begin for errors login -->
                            <div id="login_error_message" style="display: none;">
                                <h4 class="alert alert-danger text-center">Доступ закрыт!</h4>
                            </div>
                            <!-- end for errors login -->
                            <form id="login_form" style="width: 300px; margin: auto;" action="index.php" method="post">
                                <p><input class="form-control" type="text" name="username" id="username" placeholder="Логин" /></p>
                                <p><input class="form-control" type="password" name="password" id="password" placeholder="Пароль" /></p>
                                <p class="text-center"><input class="btn btn-success" type="submit" value="Войти" /></p>
                            </form>
                        <?php } ?>
                    </div>
                    <div id="answer" class="modal-footer">
                        <button type="button" id="no" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- END MODAL WINDOWS -->
    </article>

    <script src="js/main.js"></script>
    <!-- END BODY -->

<!-- FOOTER -->
<?php include("../includes/templates/footer.php"); ?>
