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

// echo implode(" ", $_POST);
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $_SESSION["showGrid"] = $_POST["showGrid"];


    $box = str_replace("-", "", trim($_POST["box-new"]));
    // $_POST["box"] = $box;

    $description = trim($_POST["description-new"]);
    $quantity = trim($_POST["quantity-new"]);
    $value = trim($_POST["value-new"]);

    $condition = str_replace("-", "", trim($_POST["condition-new"]));
    // $_POST["condition"] = $condition;

    $gender = str_replace("-", "", trim($_POST["gender-new"]));
    // $_POST["gender"] = $gender;

    $material = str_replace("-", "", trim($_POST["material-new"]));
    // $_POST["material"] = $material;

    $filename = trim($_POST["filename-new"]);

    // echo $_POST;

    foreach ($_POST as $name => $val) {
        echo htmlspecialchars($name . ': ' . $val) . "<br/>";
    }

    $action = $_POST["action"];
    $id = $_POST["id"];
    $uri = "";
    $date = date('d-m-y h:i:s');
    echo $date;

    // INSERT a new row
    if ($action == "new") {
        $id = "";
        // echo $uri;

        // if (!$box) {
        //     echo "<script> document.getElementById('box-new').focus()  </script>";
        // }

        if (($box) && ($description) && ($quantity) && ($value) && ($condition) && ($gender) && (material)) {
            $result = $DB->query("INSERT INTO packing (box, description, quantity, value, conditio, gender, material, filename) VALUES (?,?,?,?,?,?,?,?)",
                array($box, $description, $quantity, $value, $condition, $gender, $material, $filename));
            if ($result > 0) {
                // echo "<script> alert('Insert " . $result .  " record(s) successfully!') </script>";
                $data = fetchAll($DB);
            }
            $DB->closeConnection();
        }
    }
}

?>
    <table>
        <form name="pack-config" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <tr>
                <td>
            
                    <button name="showGrid" value="<?= ($_SESSION["showGrid"] == "hide") ? "show" : "hide" ?>"><i class="<?= ($_SESSION["showGrid"] == "hide") ? "fa-regular fa-eye-slash" : "fa-regular fa-eye" ?>"></i></button>
                    <!-- <button name="showGrid" value="show"><i class="fa-regular fa-eye"></i></button> -->
                </td>
                <td>
                    <button><i class="fas fa-th"></i></button>
                    <button><i class="fas fa-border-none"></i></button>
                </td>
            </tr>
        <form>

    </table>

    <table border="1" padding="1">
        <tr>
            <!-- <th>ID</th> -->
            <th>Box</th>
            <th>Description</th>
            <th>Quantity</th>
            <th>Value</th>
            <th>Condition</th>
            <th>Gender</th>
            <th>Material</th>
            <th>Filename</th>
            <th>Created on</th>
            <th>Updated on</th>
            <th>Action</th>
        </tr>
        <form name="pack-new" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <tr>

                <!-- <td> -->
                <input name="id" class="form-control input-sm" type="hidden" value="">
                <input name="action" class="form-control input-sm" type="hidden" value="new" />
                <!-- </td> -->
                <td>
                    <select name="box-new" class="form-control imput-sm" required>
                        <option></option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </td>
                <td><input name="description-new" class="form-control input-sm" type="text" maxlength="40" length="30"
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
// echo var_dump($result);
for ($i = 0; $i < count($data); $i++) {
    ?>

        <form name="pack-old" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <tr>
                <input name="id-<?=$data[$i]["id"]?>" class="form-control input-sm" type="hidden" value="">
                <!-- <td><?=$data[$i]["id"]?></td> -->
                <td>
                    <select name="box-<?=$data[$i]["id"]?>" class="form-control imput-sm" required>
                        <option></option>
                        <option value="1" <?if ($data[$i]["box"]==1) { echo "selected" ; } ?>>1</option>
                        <option value="2" <?if ($data[$i]["box"]==2) { echo "selected" ; } ?>>2</option>
                        <option value="3" <?if ($data[$i]["box"]==3) { echo "selected" ; } ?>>3</option>
                        <option value="4" <?if ($data[$i]["box"]==4) { echo "selected" ; } ?>>4</option>
                        <option value="5" <?if ($data[$i]["box"]==5) { echo "selected" ; } ?>>5</option>
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
                    <input name="value-<?=$data[$i]["id"]?>" size="6" min="0" value="<?=$data[$i]["quantity"]?>"
                        class="form-control input-sm" type="number" step="100" required />
                </td>
                <td>
                    <select name="condition-<?=$data[$i]["id"]?>" class="form-control input-sm" required>
                        <option></option>
                        <option value="New" <?if ($data[$i]["conditio"]=="New" ) { echo "selected" ; } ?>>New</option>
                        <option value="Used" <?if ($data[$i]["conditio"]=="Used" ) { echo "selected" ; } ?>>Used
                        </option>
                    </select>
                </td>
                <td>
                    <select name="gender-<?=$data[$i]["id"]?>" class="form-control input-sm" required>
                        <option></option>
                        <option value="Children" <?if ($data[$i]["gender"]=="Children" ) { echo "selected" ; } ?>
                            >Children
                        </option>
                        <option value="Female" <?if ($data[$i]["gender"]=="Female" ) { echo "selected" ; } ?>>Female
                        </option>
                        <option value="Male" <?if ($data[$i]["gender"]=="Male" ) { echo "selected" ; } ?>>Male</option>
                        <option value="Other" <?if ($data[$i]["gender"]=="Other" ) { echo "selected" ; } ?>>Other
                        </option>
                    </select>
                </td>
                <td>
                    <select name="material-<?=$data[$i]["id"]?>" class="form-control input-sm" required>
                        <option></option>
                        <option value="Cotton" <?if ($data[$i]["material"]=="Cotton" ) { echo "selected" ; } ?>>Cotton
                        </option>
                        <option value="Crystal" <?if ($data[$i]["material"]=="Crystal" ) { echo "selected" ; } ?>
                            >Crystal
                        </option>
                        <option value="Glass" <?if ($data[$i]["material"]=="Glass" ) { echo "selected" ; } ?>>Glass
                        </option>
                        <option value="Leather" <?if ($data[$i]["material"]=="Leather" ) { echo "selected" ; } ?>
                            >Leather
                        </option>
                        <option value="Linen" <?if ($data[$i]["material"]=="Linen" ) { echo "selected" ; } ?>>Linen
                        </option>
                        <option value="Metal" <?if ($data[$i]["material"]=="Metal" ) { echo "selected" ; } ?>>Metal
                        </option>
                        <option value="Nylon" <?if ($data[$i]["material"]=="Nylon" ) { echo "selected" ; } ?>>Nylon
                        </option>
                        <option value="Other" <?if ($data[$i]["material"]=="Other" ) { echo "selected" ; } ?>>Other
                        </option>
                        <option value="Paper" <?if ($data[$i]["material"]=="Paper" ) { echo "selected" ; } ?>>Paper
                        </option>
                        <option value="Plastic" <?if ($data[$i]["material"]=="Plastic" ) { echo "selected" ; } ?>
                            >Plastic
                        </option>
                        <option value="Porcelain" <?if ($data[$i]["material"]=="Porcelain" ) { echo "selected" ; } ?>
                            >Porcelain</option>
                        <option value="Silk" <?if ($data[$i]["material"]=="Silk" ) { echo "selected" ; } ?>>Silk
                        </option>
                        <option value="Vinyl" <?if ($data[$i]["material"]=="Vinyl" ) { echo "selected" ; } ?>>Vinyl
                        </option>
                        <option value="Wood" <?if ($data[$i]["material"]=="Wood" ) { echo "selected" ; } ?>>Wood
                        </option>
                        <option value="Wool" <?if ($data[$i]["material"]=="Wool" ) { echo "selected" ; } ?>>Wool
                        </option>
                    </select>
                </td>
                <td>
                    <input name="filename-<?=$data[$i]["id"]?>" class="form-control input-sm" type="text" maxlength="30"
                        length="30" value="<?=$data[$i]["filename"]?>" />
                </td>
                </td>
                <td>
                    <font class="datetime"><?=$data[$i]["created"]?></font>
                </td>
                <td>
                    <font class="datetime"><?=$data[$i]["updated"]?></font>
                </td>
                <td>
                    <!-- fa-regular fa-floppy-disk -->
                    <!-- <i class="fa-regular fa-file"></i> -->
                    <button name="save-<?=$data[$i]["id"]?>" value="-<?=$data[$i]["id"]?>" class="btn"><i
                            class="fa-regular fa-floppy-disk"></i></button>
                    <button name="delete-<?=$data[$i]["id"]?>" value="-<?=$data[$i]["id"]?>" val class="btn"><i
                            class="fa-solid fa-trash"></i></button>
                </td>
            </tr>
        </form>
        <?php
}

?>
    </table>

</body>

</html>