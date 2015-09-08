<?php

require_once("config.php");

class Entry {

    private $mysqli;

    // connect to db
    public function __construct() {
        $this->mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if($this->mysqli->connect_errno) {
            die("Error connection to DB..." . $this->mysqli->connect_error);
        }
    }

    public function __destruct() {
        $this->mysqli->close();
    }

    // add new entry
    public function add_entry($name, $url, $text) {
        $this->mysqli->real_escape_string($name);
        $this->mysqli->real_escape_string($url);
        $this->mysqli->real_escape_string($text);

        $result = $this->mysqli->query("INSERT INTO entrys (name, url, text) VALUES ('{$name}', '{$url}', '{$text}')");

        if($result) {
            return true;
        } else {
            return false;
        }
    }

    // get all pages
    private function get_pages() {
        $limit_entry = 10;
        $result = $this->mysqli->query("SELECT COUNT(*) FROM entrys");
        $total_rows = $result->fetch_row();
        $total_pages = ceil($total_rows[0] / $limit_entry);

        return $result = array(
            "total_pages" => $total_pages,
            "limit_entry" => $limit_entry
        );
    }

    // draw pagination
    public function pagination($page) {
        $limit_entry = $this->get_pages()["limit_entry"];
        $total_pages = $this->get_pages()["total_pages"];

        $menu = "";

        if($total_pages > 0 && $total_pages != 1) {
            $menu .= "<ul class='pagination'>";
            $count = 1;

            if($page == 1) {
                $menu .= "<li class='disabled'><a href=\"#\" aria-label=\"Previous\"><span aria-hidden=\"true\">&laquo;</span></a></li>";
            } elseif($page > 1) {
                $prev = $page - 1;
                $menu .= "<li><a href=\"?page={$prev}\" aria-label=\"Previous\"><span aria-hidden=\"true\">&laquo;</span></a></li>";
            }

            while($total_pages >= $count) {
                if($page == $count) {
                    $menu .= '<li class="active"><a href="?page=' . $count . '">' . $count . '</a></li>';
                } else {
                    $menu .= '<li><a href="?page=' . $count . '">' . $count . '</a></li>';
                }
                $count++;
            }

            if($page == $total_pages) {
                $menu .= "<li class='disabled'><a href=\"#\" aria-label=\"Next\"><span aria-hidden=\"true\">&raquo;</span></a></li>";
            } elseif($page < $total_pages) {
                $next = $page + 1;
                $menu .= "<li><a href=\"?page={$next}\" aria-label=\"Next\"><span aria-hidden=\"true\">&raquo;</span></a></li>";
            }

            $menu .= "</ul>";
        }

        return $menu;
    }

    // delete entry
    public function delete_entry($id) {
        $id = (int) $id;

        $result = $this->mysqli->query("DELETE FROM entrys WHERE id = {$id} LIMIT 1");

        if($result) {
            return true;
        } else {
            return false;
        }
    }

    // update entry
    public function update_entry($id, $name, $url, $text) {
        $this->mysqli->real_escape_string($name);
        $this->mysqli->real_escape_string($url);
        $this->mysqli->real_escape_string($text);

        $result = $this->mysqli->query("UPDATE entrys SET name = '{$name}', url = '{$url}', text = '{$text}' WHERE id = {$id} LIMIT 1");

        if($result) {
            return true;
        } else {
            return false;
        }
    }

    // draw all entry
    public function print_entry($result) {
        $output = "";

        if($result) {
            echo "<tbody>";
            while ($row = $result->fetch_assoc()) {
                $text = preg_replace( "#\r?\n#", "<br />", $row["text"]);
                $output .= "<tr>";
                $output .= "<td>{$row['id']}</td>";
                $output .= "<td>{$row['name']}</td>";
                $output .= "<td><a href='//{$row['url']}' target='_blank'>{$row['url']}</a></td>";
                $output .= "<td>{$text}</td>";
                // is admin
                if($this->is_admin()) {
                    $output .= "<td class='text-right'><a title='Редактировать запись' data-toggle=\"modal\" data-target=\"#edit\" class='btn btn-primary edit-link' href='#' data-action=\"edit\" data-id=\"{$row['id']}\"><span class='glyphicon glyphicon-edit'></span></a> ";
                    $output .= "<a title='Удалить запись' class='btn btn-danger remove-link' data-toggle=\"modal\" data-target=\"#remove\" data-action='delete' data-id='{$row["id"]}' href='#'><span class='glyphicon glyphicon-remove'></span></a></td>";
                }
                $output .= "</tr>";
            }
            echo $output;
            echo "</tbody>";
        } else {
            echo $output;
        }
    }

    // get data entry
    public function get_entry($id) {
        $id = (int) $id;
        $result = $this->mysqli->query("SELECT * FROM entrys WHERE id = {$id}");

        return $result;
    }

    // ... search ...
    public function search($search) {
        $this->mysqli->escape_string($search);

        $limit_entry = $this->get_pages()["limit_entry"];

        $result = $this->mysqli->query("SELECT * FROM entrys WHERE name LIKE '%{$search}%' OR url LIKE '%{$search}%' LIMIT {$limit_entry}");

        if($result) {
            if($result->num_rows > 0) {
                $this->print_entry($result);
            } else {
                $search_not_found = "";
                $search_not_found .= "<h1 class='alert alert-info text-center'>";
                $search_not_found .= "По запросу \"{$search}\" ничего не найдено...";
                $search_not_found .= "</h1>";

                echo $search_not_found;
            }
        } else {
            die("Ошибка запроса в базу!" . "<br />" . $this->mysqli->connect_error);
        }
    }

    // get all entry, with and without order and sort
    public function get_result($page, $sort, $order) {
        $limit_entry = $this->get_pages()["limit_entry"];
        $total_pages = $this->get_pages()["total_pages"];

        $page_position = (($page - 1) * $limit_entry);

        if(($sort == "name" || $sort == "url") && $order == "up") {
            $order = "ASC";
        } elseif(($sort == "name" || $sort == "url") && $order == "down") {
            $order = "DESC";
        } else {
            $sort = "id";
            $order = "ASC";
        }

        $results = $this->mysqli->query("SELECT * FROM entrys ORDER BY {$sort} {$order} LIMIT {$page_position}, {$limit_entry}");

        if($results) {
            $this->print_entry($results);
        } else {
            echo $this->mysqli->error;
        }
    }

    // check is admin
    public function is_admin() {
        if(isset($_SESSION["auth"])) {
            if($_SESSION["auth"] == 1) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function login($login, $pass) {
        $result = $this->mysqli->query("SELECT * FROM users WHERE username = '{$login}'");

        if($result) {
            $row = $result->fetch_assoc();
            if(password_verify($pass, $row["hashed_password"])) {
                session_start();

                $_SESSION["auth"] = 1;
                // setcookie("username", $login, time() + (84600 * 30));
            } else {
                return "error";
            }
        } else {
            return "error";
        }
    }
}
