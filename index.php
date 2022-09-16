<!DOCTYPE html>
<html lang="en">
<?php
    require("./db-connection.php");

    function fetchAll($DB) {
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

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $box = trim($_POST["box"]);
        $description = trim($_POST["description"]);
        $quantity = trim($_POST["quantity"]);
        $value = trim($_POST["value"]);
        $condition = trim($_POST["condition"]);
        $gender = trim($_POST["gender"]);
        $material = trim($_POST["material"]);
        $filename = trim($_POST["filename"]);

        // echo $_POST;
        // echo implode(" ", $_POST);
        foreach ($_POST as $name => $val){
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
            echo $uri;
            $result = $DB->query("INSERT INTO packing (box, description, quantity, value, conditio, gender, material, filename) VALUES (?,?,?,?,?,?,?,?)", 
                         array($box, $description, $quantity, $value, $condition, $gender, $material, $filename));            
            if ($result > 0) {
                // echo "<script> alert('Insert " . $result .  " record(s) successfully!') </script>";
                $data = fetchAll($DB);
            }
            $DB->closeConnection();
        } 
    }


?>

    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <table border="1" padding="2">
            <tr>
                <th>ID</th>
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
            <tr>
                <td>
                    <input name="id" class="form-control input-sm" type="hidden" value="">{}
                    <input name="action" class="form-control input-sm" type="hidden" value="new" />
                </td>
                <td>
                    <select name="box" class="form-control imput-sm" required>
                        <option>  -  </option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </td>
                <td><input name="description" class="form-control input-sm" type="text" maxlength="40" length="40"
                        value="" placeholder="Item with Brand, Model..." required /></td>
                <td><input name="quantity" size="4" class="form-control input-sm" type="number" step="1" required />
                </td>
                <td><input name="value" size="6" class="form-control input-sm" type="number" step="100" required />
                </td>
                <td>
                    <select name="condition" class="form-control input-sm" required>
                        <option> - </option>
                        <option value="New">New</option>
                        <option value="Used">Used</option>
                    </select>
                </td>
                <td>
                    <select name="gender" class="form-control input-sm" required>
                        <option> - </option>
                        <option value="children">children</option>
                        <option value="female">female</option>
                        <option value="male">female</option>
                        <option value="other">female</option>
                    </select>
                </td>
                <td>
                    <select name="material" class="form-control input-sm" required>
                        <option> - </option>
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
                <td><input name="filename" class="form-control input-sm" type="text" maxlength="30" length="30" value=""
                        placeholder="filename..." /></td>
                <td>-</td>
                <td>-</td>
                <td><a href="#" onclick="event.preventDefault();this.closest('form').submit()"><i
                            class="fa-regular fa-floppy-disk"> </i></a>
                    <!-- <i class="fa-regular fa-file"></i> -->
                    <!-- | <i class="fa-solid fa-pen-to-square"></i>
                    | <i class="fa-solid fa-trash"></i> -->
                </td>
            </tr>
            <?php
        // echo var_dump($result);
        for ($i=0; $i<count($data); $i++) {
?>
            <tr>
                <td><?=$data[$i]["id"]?></td>
                <td><?=$data[$i]["box"]?></td>
                <td><?=$data[$i]["description"]?></td>
                <td><?=$data[$i]["quantity"]?></td>
                <td><?=$data[$i]["value"]?></td>
                <td><?=$data[$i]["conditio"]?></td>
                <td><?=$data[$i]["gender"]?></td>
                <td><?=$data[$i]["material"]?></td>
                <td><?=$data[$i]["filename"]?></td>
                <td><?=$data[$i]["created"]?></td>
                <td><?=$data[$i]["updated"]?></td>
                <td>

                    <!-- <i class="fa-regular fa-file"></i> -->
                    | <i class="fa-solid fa-pen-to-square"></i>
                    | <i class="fa-solid fa-trash"></i>
                </td>
            </tr>
<?php            
        }       

?>
        </table>
    </form>
</body>

</html>