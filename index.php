<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<?php
require "./db-connection.php";

function fetchAll($DB)
{
    $uri = "SELECT id, box, description, quantity, value, conditio, gender, material, filename, created, updated FROM packing order by box, filename asc";
    $d = $DB->query($uri);
    //$DB->closeConnection();
    return $d;
}

function updateRecords($DB)
{

}

function grepSubmitAction($post)
{
    $postKeys = array_keys($_POST);
    $pos = -1;
    $submitStr = "";
    for ($i = 0; $i < count($postKeys); $i++) {
        //echo $i . " " . $postKeys[$i] . " " . strpos($postKeys[$i], "submit") . "<br>";
        $pos = (strpos($postKeys[$i], "submit") === false) ? -1 : strpos($postKeys[$i], "submit");
        // echo $postKeys[$i] . " " . $i . " " . $pos . "<br>";
        if ($pos != -1) {
            $submitStr = $postKeys[$i];
            break;
        }
    }
    return $submitStr;
}

?>
<!-- https://en.rakko.tools/tools/36/ -->

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Packing List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
        integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="styles.css" rel="stylesheet">
</head>

<body>
    <?php
// $result = $DB->query("SELECT * FROM packing" );
// echo count($result)

// Load current records
$data = fetchAll($DB);

if (isset($_POST["showGrid"])) {
    $_SESSION["showGrid"] = $_POST["showGrid"];
}

if (isset($_POST["showNew"])) {
    $_SESSION["showNew"] = $_POST["showNew"];
}

echo "Session->ShowGrid " . $_SESSION["showGrid"] . "; <BR>Session->ShowNew " . $_SESSION["showNew"] . "<br>";
// echo implode(" ", $_POST);
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // $_SESSION["showGrid"] = isset($_POST["showGrid"]) ? $_POST["showGrid"] : $_SESSION["showGrid"];
    $_SESSION["showNew"] = isset($_POST["showNew"]) ? $_POST["showNew"] : $_SESSION["showNew"];

    //  echo "showNew: " . isset($_POST["showNew"]) . " , " . isset($_POST["action"]);

    foreach ($_POST as $name => $val) {
        echo htmlspecialchars($name . ': ' . $val) . "<br/>";
    }

    $submitAction = grepSubmitAction($_POST);

    if ($submitAction != "") {
        // echo "submitAction: " . $submitAction;
        $submitArray = explode("-", $submitAction);
        $action = "new"; // default value of $action

        if ($submitArray[1] == "new") {
            $action = "insert";
        } else if ($submitArray[1] == "save") {
            $action = "update";
            $id = $submitArray[2];
        } else if ($submitArray[1] == "remove") {
            $action = "delete";
            $id = $submitArray[2];
        }

        if ($_SESSION["showNew"] == "show" && ($action == "insert")) {

            // $action = $_POST["action"];
            echo "action: " . $action;
            $box = str_replace("-", "", trim($_POST["box-new"]));
            // $_POST["box"] = $box;

            $description = trim($_POST["description-new"]);
            $quantity = trim($_POST["quantity-new"]);
            $value = trim($_POST["value-new"]);
            $condition = str_replace("-", "", trim($_POST["condition-new"]));
            $gender = str_replace("-", "", trim($_POST["gender-new"]));
            $material = str_replace("-", "", trim($_POST["material-new"]));
            $filename = trim($_POST["filename-new"]);

            // echo $_POST;

            $id = $_POST["id"];
            $uri = "";
            $date = date('d-m-y h:i:s');
            echo $date;
            // echo "action:" . $action;
            // INSERT a new row
            // if ($action == "insert") {
            $id = "";
            // echo $uri;

            // if (!$box) {
            //     echo "<script> document.getElementById('box-new').focus()  </script>";
            // }

            if (($box) && ($description) && ($quantity) && ($value) && ($condition) && ($gender) && ($material)) {
                $result = $DB->query("INSERT INTO packing (box, description, quantity, value, conditio, gender, material, filename) VALUES (?,?,?,?,?,?,?,?)",
                    array($box, $description, $quantity, $value, $condition, $gender, $material, $filename));
                if ($result > 0) {
                    echo "<script> alert('Added " . $result . " record successfully!') </script>";
                    $data = fetchAll($DB);
                }
                $DB->closeConnection();
            }
            // }

        } else {
            if ($_SESSION["showGrid"] == "show" && ($action == "update")) {
                $box = trim($_POST["box-" . $id]);
                $description = trim($_POST["description-" . $id]);
                $quantity = trim($_POST["quantity-" . $id]);
                $value = trim($_POST["value-" . $id]);
                $condition = trim($_POST["condition-" . $id]);
                $gender = trim($_POST["gender-" . $id]);
                $material = trim($_POST["material-" . $id]);
                $filename = trim($_POST["filename-" . $id]);

                if (($box) && ($description) && ($quantity) && ($value) && ($condition) && ($gender) && ($material)) {
                    $result = $DB->query("UPDATE packing SET box = ?, description = ?, quantity = ?, value = ?, conditio = ?, gender = ?, material = ?, filename = ?
                        WHERE id = ?", array($box, $description, $quantity, $value, $condition, $gender, $material, $filename, $id));
                    if ($result > 0) {
                        echo "<script> alert('Modified " . $result . " record successfully! [" . $id . "]') </script>";
                        $data = fetchAll($DB);
                    }
                    $DB->closeConnection();
                }
            } else if ($_SESSION["showGrid"] == "show" && ($action == "delete")) {
                if ($id != "") {
                    $result = $DB->query("DELETE FROM packing WHERE id = ?", array($id));
                    if ($result > 0) {
                        echo "<script> alert('Removed " . $result . " record successfully! [" . $id . "]') </script>";
                    }
                    $DB->closeConnection();
                }
            }
        }
    }
}

?>
    <table>
        <form name="pack-config" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <tr>
                <td>
                    <button class="btn" name="showGrid" value="<?=($_SESSION["showGrid"] == "hide") ? "show" : "hide"?>"
                        formnovalidate>
                        <i
                            class="<?=($_SESSION["showGrid"] == "hide") ? "fa-regular fa-eye-slash" : "fa-regular fa-eye"?>"></i>
                    </button>
                </td>
                <td>
                    <button class="btn" name="showNew" value="<?=($_SESSION["showNew"] == "hide") ? "show" : "hide"?>"
                        formnovalidate>
                        <i
                            class="<?=($_SESSION["showNew"] == "hide") ? "fas fa-th" : "fas fa-border-none"?>"></i>
                    </button>
                </td>
            </tr>
            <form>
    </table>

    <table border="1" padding="1">
        <tr>
            <th>No.</th>
            <th>Box</th>
            <th>Description</th>
            <th>Quantity</th>
            <th>Value</th>
            <th>Condition</th>
            <th>Gender</th>
            <th>Material</th>
            <th colspan="2">Filename</th>
            <th>Created on</th>
            <th>Updated on</th>
            <th>Action</th>
        </tr>
<?php
if ($_SESSION["showNew"] == "show") {
    ?>
        <form name="pack-new" value="new" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <tr>
                <td>
                <i class="fa-solid fa-plus"></i>
                    <input name="id" class="form-control input-sm" type="hidden" value="">
                </td>
                <td>
                    <select name="box-new" class="form-control imput-sm" required>
                        <option></option>
<?for ($i = 0; $i < 15; $i++) {?>
                        <option value="<?=$i + 1?>"><?=$i + 1?></option>
<?}?>
                    </select>
                </td>
                <td><input name="description-new" class="form-control input-sm" type="text" maxlength="30" length="30"
                        value="" placeholder="Item with Brand, Model..." required /></td>
                <td><input name="quantity-new" size="4" min="0" class="form-control input-sm" type="number" step="1"
                        required />
                </td>
                <td><input name="value-new" size="6" min="0" class="form-control input-sm" type="number" step="100"
                        required />
                </td>
                <td>
                    <select name="condition-new" class="form-control input-sm" required>
                        <option></option>
                        <option value="New">New</option>
                        <option value="Used">Used</option>
                    </select>
                </td>
                <td>
                    <select name="gender-new" class="form-control input-sm" required>
                        <option></option>
                        <option value="Children">Children</option>
                        <option value="Female">Female</option>
                        <option value="Male">Male</option>
                        <option value="Other">Other</option>
                    </select>
                </td>
                <td>
                    <select name="material-new" class="form-control input-sm" required>
                        <option></option>
                        <option value="Cotton">Cotton</option>
                        <option value="Crystal">Crystal</option>
                        <option value="Glass">Glass</option>
                        <option value="Leather">Leather</option>
                        <option value="Linen">Linen</option>
                        <option value="Metal">Metal</option>
                        <option value="Nylon">Nylon</option>
                        <option value="Other">Other</option>
                        <option value="Paper">Paper</option>
                        <option value="Plastic">Plastic</option>
                        <option value="Porcelain">Porcelain</option>
                        <option value="Silk">Silk</option>
                        <option value="Vinyl">Vinyl</option>
                        <option value="Wood">Wood</option>
                        <option value="Wool">Wool</option>
                    </select>
                </td>
                <td><input name="filename-new" class="form-control input-sm" type="text" maxlength="30" length="30"
                        value="" placeholder="filename..." /></td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>
                    <!-- <a href="#" onclick="event.preventDefault();this.closest('form').submit()"> -->
                    <button name="submit-new" value="new" class="btn"><i class="fa-solid fa-pen-to-square">
                        </i></button>
                    <!-- </a> -->
                    <!-- <i class="fa-regular fa-file"></i> -->
                    <!-- | <i class="fa-solid fa-pen-to-square"></i>
                    | <i class="fa-solid fa-trash"></i> -->
                </td>
            </tr>
        </form>
<?php
}
// echo var_dump($result);
if ($_SESSION["showGrid"] == "show") {
    for ($i = 0; $i < count($data); $i++) {
        ?>
        <form name="pack-old" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <tr>
                <td>
                    <?=$i+1?>
                </td>
                <input name="id-<?=$data[$i]["id"]?>" class="form-control input-sm" type="hidden" value="">
                <!-- <input name="action" class="form-control input-sm" type="hidden" value="edit" /> -->
                <!-- <td><?=$data[$i]["id"]?></td> -->
                <td>
                    <select name="box-<?=$data[$i]["id"]?>" class="form-control imput-sm" required>
                        <option></option>
                        <option value="1" <?if ($data[$i]["box"] == 1) {echo "selected";}?>>1</option>
                        <option value="2" <?if ($data[$i]["box"] == 2) {echo "selected";}?>>2</option>
                        <option value="3" <?if ($data[$i]["box"] == 3) {echo "selected";}?>>3</option>
                        <option value="4" <?if ($data[$i]["box"] == 4) {echo "selected";}?>>4</option>
                        <option value="5" <?if ($data[$i]["box"] == 5) {echo "selected";}?>>5</option>
                    </select>
                </td>
                <td>
                    <input name="description-<?=$data[$i]["id"]?>" class="form-control input-sm" type="text"
                        maxlength="40" length="40" value="<?=$data[$i]["description"]?>" required />
                </td>
                <td>
                    <input name="quantity-<?=$data[$i]["id"]?>" size="4" min="0" value="<?=$data[$i]["quantity"]?>"
                        class="form-control input-sm" type="number" step="1" required />
                </td>
                <td>
                    <input name="value-<?=$data[$i]["id"]?>" size="6" min="0" value="<?=$data[$i]["value"]?>"
                        class="form-control input-sm" type="number" step="100" required />
                </td>
                <td>
                    <select name="condition-<?=$data[$i]["id"]?>" class="form-control input-sm" required>
                        <option></option>
                        <option value="New" <?if ($data[$i]["conditio"] == "New") {echo "selected";}?>>New</option>
                        <option value="Used" <?if ($data[$i]["conditio"] == "Used") {echo "selected";}?>>Used
                        </option>
                    </select>
                </td>
                <td>
                    <select name="gender-<?=$data[$i]["id"]?>" class="form-control input-sm" required>
                        <option></option>
                        <option value="Children" <?if ($data[$i]["gender"] == "Children") {echo "selected";}?>
                            >Children
                        </option>
                        <option value="Female" <?if ($data[$i]["gender"] == "Female") {echo "selected";}?>>Female
                        </option>
                        <option value="Male" <?if ($data[$i]["gender"] == "Male") {echo "selected";}?>>Male</option>
                        <option value="Other" <?if ($data[$i]["gender"] == "Other") {echo "selected";}?>>Other
                        </option>
                    </select>
                </td>
                <td>
                    <select name="material-<?=$data[$i]["id"]?>" class="form-control input-sm" required>
                        <option></option>
                        <option value="Cotton" <?if ($data[$i]["material"] == "Cotton") {echo "selected";}?>>Cotton
                        </option>
                        <option value="Crystal" <?if ($data[$i]["material"] == "Crystal") {echo "selected";}?>
                            >Crystal
                        </option>
                        <option value="Glass" <?if ($data[$i]["material"] == "Glass") {echo "selected";}?>>Glass
                        </option>
                        <option value="Leather" <?if ($data[$i]["material"] == "Leather") {echo "selected";}?>
                            >Leather
                        </option>
                        <option value="Linen" <?if ($data[$i]["material"] == "Linen") {echo "selected";}?>>Linen
                        </option>
                        <option value="Metal" <?if ($data[$i]["material"] == "Metal") {echo "selected";}?>>Metal
                        </option>
                        <option value="Nylon" <?if ($data[$i]["material"] == "Nylon") {echo "selected";}?>>Nylon
                        </option>
                        <option value="Other" <?if ($data[$i]["material"] == "Other") {echo "selected";}?>>Other
                        </option>
                        <option value="Paper" <?if ($data[$i]["material"] == "Paper") {echo "selected";}?>>Paper
                        </option>
                        <option value="Plastic" <?if ($data[$i]["material"] == "Plastic") {echo "selected";}?>
                            >Plastic
                        </option>
                        <option value="Porcelain" <?if ($data[$i]["material"] == "Porcelain") {echo "selected";}?>
                            >Porcelain</option>
                        <option value="Silk" <?if ($data[$i]["material"] == "Silk") {echo "selected";}?>>Silk
                        </option>
                        <option value="Vinyl" <?if ($data[$i]["material"] == "Vinyl") {echo "selected";}?>>Vinyl
                        </option>
                        <option value="Wood" <?if ($data[$i]["material"] == "Wood") {echo "selected";}?>>Wood
                        </option>
                        <option value="Wool" <?if ($data[$i]["material"] == "Wool") {echo "selected";}?>>Wool
                        </option>
                    </select>
                </td>
                <td>
                    <input name="filename-<?=$data[$i]["id"]?>" class="form-control input-sm" type="text" maxlength="30"
                        length="30" value="<?=$data[$i]["filename"]?>" />
                        <!-- <button class="btn"><i class="fa-regular fa-image"></i></button> -->
                </td>
                <td>
<?
        $pattern = "/\b(\.jpg|\.JPG|\.png|\.PNG|\.gif|\.GIF)\b/";
        // http://phpwfun.atwebpages.com/packing/images/1.jpg
        if (preg_match($pattern, $data[$i]["filename"])) {
            ?>
                <button class="btn" onClick="window.open('./images/<?=$data[$i]["box"]?>/<?=$data[$i]["filename"]?>')"><i class="fa-regular fa-image"></i></button>
<?} else {?>
                <i class="fa-regular fa-image-slash"></i>
<?}?>
                </td>
                <td>
                    <font class="datetime"><?=$data[$i]["created"]?></font>
                </td>
                <td>
                    <font class="datetime"><?=($data[$i]["updated"] == "0000-00-00 00:00:00") ? "-" : $data[$i]["updated"]?></font>
                </td>
                <td>
                    <!-- fa-regular fa-floppy-disk -->
                    <!-- <i class="fa-regular fa-file"></i> -->
                    <button name="submit-save-<?=$data[$i]["id"]?>" value="<?=$data[$i]["id"]?>" class="btn"><i
                            class="fa-regular fa-floppy-disk"></i></button>
                    <button name="submit-remove-<?=$data[$i]["id"]?>" value="<?=$data[$i]["id"]?>" class="btn"><i
                            class="fa-solid fa-trash"></i></button>
                </td>
            </tr>
        </form>
<?php
}
}
?>
    </table>

</body>

</html>