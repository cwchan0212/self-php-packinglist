<?php
    session_start();
    require "./db-connection.php";

    function fetchAll($DB) {
        $uri = "SELECT id, box, description, quantity, value, conditio, gender, material, filename, created, updated FROM packing order by box, filename asc";
        $d = $DB->query($uri);
        $DB->closeConnection();
        return $d;
    }

    function fetchOne($DB, $box) {
        $uri = "SELECT id, box, description, quantity, value, conditio, gender, material, filename, created, updated FROM packing WHERE box = ? order by box, filename asc";
        $d = $DB->query($uri, array($box));
        $DB->closeConnection();
        return $d;
    }

    function insertOne($DB, $array) {
        $uri = "INSERT INTO packing (box, description, quantity, value, conditio, gender, material, filename, ip) VALUES (?,?,?,?,?,?,?,?,?)";
        $res = $DB->query($uri, $array);
        // $_SESSION["lastID"] = $DB->lastInsertId();
        // imageUpload();
        return $res;
    }
    
    function updateOne($DB, $array) {
        $uri = "UPDATE packing SET box = ?, description = ?, quantity = ?, value = ?, conditio = ?, gender = ?, material = ?, filename = ? WHERE id = ?";
        $res = $DB->query($uri, $array);               
        return $res;
    }

    function deleteOne($DB, $array) {
        $uri = "DELETE FROM packing WHERE id = ?";
        $res = $DB->query($uri, $array);
        return $res;
    }    

    function grepSubmitAction($post) {
        $postKeys = array_keys($_POST);
        $pos = -1;
        $submitStr = "";
        for ($i = 0; $i < count($postKeys); $i++) {
            $pos = strpos($postKeys[$i], "submit") === false ? -1 : strpos($postKeys[$i], "submit");
            if ($pos != -1) {
                $submitStr = $postKeys[$i];
                break;
            }
        }
        return $submitStr;
    }

    function getIPAddr() {
        $ip = "";
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    function imageUpload() {
        $target_dir = "images/";
        $target_file = $target_dir . basename($_FILES["filename-new"]["name"]);
        if (move_uploaded_file($_FILES["filename-new"]["tmp_name"], $target_file)) {
            echo "<script>alert('The file ". htmlspecialchars( basename( $_FILES["filename-new"]["name"])). " has been uploaded.')</script>";
        } else {
            echo "<script>alert(Sorry, there was an error uploading your file.')</script>";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>PHP::Packing List</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
            integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link href="styles.css" rel="stylesheet">
    </head>

    <body>
        <div class="logo">
            <img src="https://www.sevenseasworldwide.com/media/4203/25thlogo400x124.png" width="300" />
        </div>
        <div class="banner">
            <img src="https://www.sevenseasworldwide.com/media/3523/hongkong800.png?anchor=center&mode=crop&width=60&height=60&rnd=132690178250000000" width="40">
            　<i class="fa-solid fa-angle-left fa-2xl"></i><i class="fa-solid fa-question fa-2xl"></i><i class="fa-solid fa-equals fa-2xl"></i>　<i class="fa-solid fa-arrow-left fa-2xl"></i>　<i class="fa-solid fa-minus fa-2xl"></i>　<h3 class="display-4"> Packing List </h3>　<i class="fa-solid fa-minus fa-2xl"></i>　<i class="fa-solid fa-arrow-right fa-2xl"></i>　<i class="fa-solid fa-question fa-2xl"></i><i class="fa-solid fa-angle-right fa-2xl"></i>　  
            <img src="https://www.sevenseasworldwide.com/media/3536/uk800.png?anchor=center&mode=crop&width=60&height=60&rnd=132690180520000000" width="40">
        </div>
<?php
    if (isset($_POST["showGrid"])) {
        $_SESSION["showGrid"] = $_POST["showGrid"];        
    } else {
        $_SESSION["showGrid"] = "show";
    }

    if (isset($_POST["showNew"])) {
        $_SESSION["showNew"] = $_POST["showNew"];
    } else {
        $_SESSION["showNew"] = "show";
    }

    // echo "Session->ShowGrid " . $_SESSION["showGrid"] . "; <BR>Session->ShowNew " .  $_SESSION["showNew"] . "<br>";

    if (isset($_POST["showBox"])) {
        if ($_SESSION["showGrid"] == "show") {
            $_SESSION["showBox"] = (int)trim($_POST["showBox"]);
        } else {
            $_SESSION["showBox"] = -1;
        }
    } else {
        $_SESSION["showBox"] = -1;
    }

    if ($_SESSION["showGrid"] == "hide") {
        $_SESSION["showBox"] = -1;
    }


    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $submitAction = grepSubmitAction($_POST);
        
        if (isset($_FILES["filename-new"])) {
            // imageUpload($_FILES["filename-new"]);    // not implemented
        }  

        // echo "files: " . var_dump($_FILES);

        if ($submitAction != "") {
            // echo "submitAction: " . $submitAction . "<BR>";
            $submitArray = explode("-", $submitAction);
            $action = "new"; // default value of $action

            if ($submitArray[1] == "new") {
                $action = "insert";
            } elseif ($submitArray[1] == "save") {
                $action = "update";
                $id = $submitArray[2];
            } elseif ($submitArray[1] == "remove") {
                $action = "delete";
                $id = $submitArray[2];
            }

            // foreach ($_POST as $name => $val) {
            //     echo htmlspecialchars($name . ": " . $val) . "<br/>";
            // }

            if ($_SESSION["showNew"] == "show" && $action == "insert") {
                // echo "action: " . $action;
                $box = trim($_POST["box-new"]);

                $description = trim($_POST["description-new"]);
                $quantity = trim($_POST["quantity-new"]);
                $value = trim($_POST["value-new"]);
                $condition = trim($_POST["condition-new"]);
                $gender = trim($_POST["gender-new"]);
                $material =  trim($_POST["material-new"]);
                // $filename = $_POST["filename-new"];
                $filename = $_FILES["filename-new"]["name"];
                $ip = getIPAddr();

                $uri = "";
                date_default_timezone_set("Asia/Hong_Kong");
                $date = date("d-m-y h:i:s");
                // echo $date;
                $id = "";

                if ($box && $description && $quantity && $value && $condition && $gender &&  $material ) {
                    $result = insertOne($DB, array($box, $description, $quantity, $value, $condition, $gender, $material, $filename, $ip));
                    if ($result > 0) {
                        $_SESSION["lastId"] = $DB->lastInsertId();
                        $_SESSION["showBox"] = $box;
                        echo "<script> alert('Added " . $result . " record successfully!') </script>";
                    }                    
                }
                // }
            } else {
                if ($_SESSION["showGrid"] == "show" && $action == "update") {
                    $box = trim($_POST["box-" . $id]);
                    $description = trim($_POST["description-" . $id]);
                    $quantity = trim($_POST["quantity-" . $id]);
                    $value = trim($_POST["value-" . $id]);
                    $condition = trim($_POST["condition-" . $id]);
                    $gender = trim($_POST["gender-" . $id]);
                    $material = trim($_POST["material-" . $id]);
                    $filename = trim($_POST["filename-" . $id]);

                    if ($box && $description && $quantity && $value && $condition && $gender &&  $material ) {
                        $result = updateOne($DB,array($box, $description, $quantity, $value, $condition, $gender, $material, $filename, $id));
                        $_SESSION["updateId"] = $id;
                        $_SESSION["showBox"] = $box;
                        if ($result > 0) {
                            echo "<script> alert('Modified " . $result . " record successfully! [" . $id . "]') </script>";
                            // $data = fetchOne($DB, (int)$_SESSION["showBox"]);
                        }                        
                    }
                } elseif (
                    $_SESSION["showGrid"] == "show" &&  $action == "delete" ) {
                    if ($id != "") {
                        $result = deleteOne($DB, array($id));
                        if ($result > 0) {
                            echo "<script> alert('Removed " . $result .  " record successfully! [" . $id . "]') </script>";
                        }
                    }
                }                
            }
        }
    }
    
    // echo $_SESSION["showGrid"] . "<br>";
    // echo "BOX: " . $_SESSION["showBox"] . "<br>";

    $_SESSION["lastId"] = isset($_SESSION["lastId"]) ? $_SESSION["lastId"] : -1;
    $_SESSION["updateId"] = isset($_SESSION["updateId"]) ? $_SESSION["updateId"] : -1;

    if ($_SESSION["lastId"] != -1) {
        $_SESSION["updateId"] = $_SESSION["lastId"];
    }

    if ($_SESSION["updateId"] != -1) {
        $_SESSION["lastId"] = $_SESSION["updateId"];
    }

    // echo $_SESSION["lastId"] . "<BR>";

?>
        
        <table class="packtable">
            <form name="pack-config" method="post" action="<?=$_SERVER["PHP_SELF"]?>" enctype="multipart/form-data"> 
                <tr>
                    <td>
                        <button class="btn btn<?=$outline = (($_SESSION["showGrid"] =="show") &&  ($_SESSION["showBox"] == -1)) ? "" : "-outline" ?>-secondary" name="showGrid" value="<?=$_SESSION["showGrid"] == "hide" ? "show" : "hide"?>" formnovalidate>
                            　<i class="<?=$_SESSION["showGrid"] == "hide" ? "fa-regular fa-eye-slash" : "fa-regular fa-eye"?>">　</i>
                        </button>
<?php
    for ($i=0; $i<15; $i++) {                      
?>
                        <button name="showBox" type="submit" class="btn btn<?=$outline = ((int)$_SESSION["showBox"] === $i+1) ? "" : "-outline" ?>-secondary" value="<?=$i+1?>" formnovalidate>　<?=$i+1?>　</button>
<?php
    }
?>
                    </td>
                    <td>
                        <button class="btn btn<?=$outline = (($_SESSION["showNew"] =="show") &&  ($_SESSION["showNew"] == -1)) ? "" : "-outline" ?>-secondary" name="showNew" value="<?=$_SESSION["showNew"] == "hide" ? "show" : "hide"?>" formnovalidate>
                            　<i class="<?=$_SESSION["showNew"] == "hide" ? "fas fa-th" : "fas fa-border-none"?>">　</i>
                        </button>
                    </td>
                </tr>
                <form>
        </table>

        <table class="packtable">
            <tr>
                <th>No.</th>
                <th>Box</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Value</th>
                <th>Condition</th>
                <th>Gender</th>
                <th>Material</th>
                <th colspan="2">Filename <small>*(not implemented)</small></th>
                <th>Created on</th>
                <th>Updated on</th>
                <th>Action</th>
            </tr>

<?php
    if ($_SESSION["showNew"] == "show") {
?>
            <form name="pack-new" method="post" action="<?=$_SERVER["PHP_SELF"]?>" enctype="multipart/form-data">  
                    
            <tr>
                <td>
                    <i class="fa-solid fa-plus"></i>
                    <input name="id" class="form-control form-control-sm" type="hidden" value="">
                </td>
                <td>
                    <select name="box-new" class="form-control form-control-sm" required>
                        <option></option>
<?php  
    for ($i = 0; $i < 15; $i++) {
?>
                        <option value="<?=$i + 1?>" <?=$selected = ((int)($_SESSION["showBox"]) === $i + 1) ? "selected" : "" ?>><?=$i + 1?></option>
<?php   
    }   
?>
                    </select>
                </td>
                <td><input name="description-new" class="form-control form-control-sm" type="text" maxlength="30" length="30"  value="" placeholder="Item with Brand, Model..." required /></td>
                <td><input name="quantity-new" size="4" min="0" class="form-control form-control-sm" type="number" step="1" required />
                </td>
                <td><input name="value-new" size="6" min="0" class="form-control form-control-sm" type="number" step="10" required />
                </td>
                <td>
                    <select name="condition-new" class="form-control form-control-sm" required>
                        <option></option>
                        <option value="New">New</option>
                        <option value="Used">Used</option>
                    </select>
                </td>
                <td>
                    <select name="gender-new" class="form-control form-control-sm" required>
                        <option></option>
                        <option value="Children">Children</option>
                        <option value="Female">Female</option>
                        <option value="Male">Male</option>
                        <option value="Other">Other</option>
                    </select>
                </td>
                <td>
                    <select name="material-new" class="form-control form-control-sm" required>
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
                <td>
                    <input name="filename-new" class="form-control form-control-sm" id="formFileSm" type="file">
                    
                </td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>
                    <button type="submit" class="btn btn-outline-secondary" name="submit-new" value="new" class="btn" type="submit">
                        　<i class="fa-solid fa-pen-to-square">　</i>
                    </button>
                </td>
            </tr>
            </form>
<?php
    }
?>
            <form name="pack-old" method="post" action="<?=$_SERVER["PHP_SELF"]?>" enctype="multipart/form-data"> 
<?php
    
    if ($_SESSION["showGrid"] == "show") {
        // echo "box: " . $_SESSION["showBox"] . "<br>";
        if ($_SESSION["showBox"] !== -1) {
            $data = fetchOne($DB, (int)$_SESSION["showBox"]);
        } else {
            $data = fetchAll($DB);
        }
        
        // echo count($data) . "<br>";
        // $lastId = lastInsertOne($DB);
        // echo "lastid"  . $_SESSION["lastId"] . " ";
        for ($i = 0; $i < count($data); $i++) {
?>
            <tr<?= $last = ((int)($_SESSION["lastId"]) === (int)$data[$i]["id"]) ? " class=\"newly\"" : ""  ?>>
                <td><?=$i + 1?></td>
                <td>
                    <select name="box-<?=$data[$i]["id"]?>" class="form-control form-control-sm" required>
                        <option></option>
                        <?php for ($j = 0; $j < 15; $j++) {?>
                        <option value="<?=$j + 1?>" <?=$selected = ($data[$i]["box"] == $j + 1) ? "selected" : "" ?>>
                            <?=$j + 1?></option>
                        <?php }?>
                    </select>
                </td>
                <td>
                    <input name="description-<?=$data[$i]["id"]?>" class="form-control form-control-sm" type="text" maxlength="40" length="40" value="<?=$data[$i]["description"]?>" required />
                </td>
                <td>
                    <input name="quantity-<?=$data[$i]["id"]?>" size="4" min="0" value="<?=$data[$i]["quantity"]?>" class="form-control form-control-sm" type="number" step="1" required />
                </td>
                <td>
                    <input name="value-<?=$data[$i]["id"]?>" size="6" min="0" value="<?=$data[$i]["value"]?>"class="form-control form-control-sm" type="number" step="10" required />
                </td>
                <td>
                    <select name="condition-<?=$data[$i]["id"]?>" class="form-control form-control-sm" required>
                        <option></option>
                        <option value="New" <?=$selected = ($data[$i]["conditio"] == "New") ? "selected" : "" ?>>New</option>
                        <option value="Used" <?=$selected = ($data[$i]["conditio"] == "Used") ? "selected" : "" ?>>Used</option>
                    </select>
                </td>
                <td>
                    <select name="gender-<?=$data[$i]["id"]?>" class="form-control form-control-sm" required>
                        <option></option>
                        <option value="Children" <?=$selected = ($data[$i]["gender"] == "Children") ? "selected" : "" ?>>Children</option>
                        <option value="Female" <?=$selected = ($data[$i]["gender"] == "Female") ? "selected" : "" ?>>Female</option>
                        <option value="Male" <?=$selected = ($data[$i]["gender"] == "Male") ? "selected" : "" ?>>Male</option>
                        <option value="Other" <?=$selected = ($data[$i]["gender"] == "Other") ? "selected" : "" ?>>Other</option>
                    </select>
                </td>
                <td>
                    <select name="material-<?=$data[$i]["id"]?>" class="form-control form-control-sm" required>
                        <option></option>
                        <option value="Cotton" <?=$selected = ($data[$i]["material"] == "Cotton") ? "selected" : "" ?>>Cotton</option>
                        <option value="Crystal" <?=$selected = ($data[$i]["material"] == "Crystal") ? "selected" : "" ?>>Crystal </option>
                        <option value="Glass" <?=$selected = ($data[$i]["material"] == "Glass") ? "selected" : "" ?>> Glass</option>
                        <option value="Leather" <?=$selected = ($data[$i]["material"] == "Leather") ? "selected" : "" ?>>Leather</option>
                        <option value="Linen" <?=$selected = ($data[$i]["material"] == "Linen") ? "selected" : "" ?>>Linen</option>
                        <option value="Metal" <?=$selected = ($data[$i]["material"] == "Metal") ? "selected" : "" ?>>Metal</option>
                        <option value="Nylon" <?=$selected = ($data[$i]["material"] == "Nylon") ? "selected" : "" ?>>Nylon</option>
                        <option value="Other" <?=$selected = ($data[$i]["material"] == "Other") ? "selected" : "" ?>>Other</option>
                        <option value="Paper" <?=$selected = ($data[$i]["material"] == "Paper") ? "selected" : "" ?>>Paper</option>
                        <option value="Plastic" <?=$selected = ($data[$i]["material"] == "Plastic") ? "selected" : "" ?>>Plastic</option>
                        <option value="Porcelain" <?=$selected = ($data[$i]["material"] == "Porcelain") ? "selected" : "" ?>>Porcelain</option>
                        <option value="Silk" <?=$selected = ($data[$i]["material"] == "Silk") ? "selected" : "" ?>>Silk</option>
                        <option value="Vinyl" <?=$selected = ($data[$i]["material"] == "Vinyl") ? "selected" : "" ?>>Vinyl</option>
                        <option value="Wood" <?=$selected = ($data[$i]["material"] == "Wood") ? "selected" : "" ?>>Wood</option>
                        <option value="Wool" <?=$selected = ($data[$i]["material"] == "Wool") ? "selected" : "" ?>>Wool</option>
                    </select>
                </td>
                <td>
                    <input name="filename-<?=$data[$i]["id"]?>" class="form-control form-control-sm" type="text" maxlength="30" length="30" value="<?=$data[$i]["filename"]?>" />
                </td>
                <td>
<?php
        $pattern = "/\b(\.jpg|\.JPG|\.png|\.PNG|\.gif|\.GIF)\b/";       // http://phpwfun.atwebpages.com/packing/images/1.jpg
            if (preg_match($pattern, $data[$i]["filename"])) {
?>
                    <button class="btn" onClick="window.open('./images/<?=$data[$i]["box"]?>/<?=$data[$i]["filename"]?>')">
                        <i class="fa-regular fa-image"></i></button>
<?php       } else {?>
  <i class="fa-regular fa-image-slash"></i>
<?php       } ?>
                </td>
                <td>
                    <font class="datetime"><?=$data[$i]["created"]?></font>
                </td>
                <td>
                    <font class="datetime">
                        <?=$data[$i]["updated"] == "0000-00-00 00:00:00" ? "-" : $data[$i]["updated"]?></font>
                </td>
                <td>
                    <!-- fa-regular fa-floppy-disk -->
                    <!-- <i class="fa-regular fa-file"></i> -->
                    <button type="submit" class="btn btn-outline-secondary" name="submit-save-<?=$data[$i]["id"]?>" value="<?=$data[$i]["id"]?>" class="btn" type="submit">　<i class="fa-regular fa-floppy-disk">　</i></button>
                    <button type="submit" class="btn btn-outline-secondary" name="submit-remove-<?=$data[$i]["id"]?>" value="<?=$data[$i]["id"]?>" class="btn">　<i class="fa-solid fa-trash"></i>　</button>
                </td>

            </tr>
<?php          
            
        }
    }
?>          </form>
        </table>

    </body>

</html>
<?php
    // session_unset();
    // session_destroy();  
    $DB->closeConnection();
?>