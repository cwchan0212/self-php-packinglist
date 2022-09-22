<?php
    session_start();
    require "./db-connection.php";

    function fetchAll($DB) {
        // SELECT box, DESCRIPTION, sum(quantity) AS Q, SUM(VALUE) AS s FROM packing group by description ORDER BY box, DESCRIPTION asc
        $uri = "SELECT box, description, SUM(quantity) AS quantity, SUM(value) as value, conditio, gender, material FROM packing GROUP BY box, description, gender, material ORDER BY box, description, conditio, gender, material, filename asc";
        $d = $DB->query($uri);
        $DB->closeConnection();
        return $d;
    }

    function fetchOne($DB, $box) {
        $uri = "SELECT box, description, SUM(quantity) AS quantity, SUM(value) as value, conditio, gender, material FROM packing WHERE box = ? GROUP BY box, description, gender, material ORDER BY box, description, conditio, gender, material, filename asc";
        $d = $DB->query($uri, array($box));
        $DB->closeConnection();
        return $d;
    }
    // https://stackoverflow.com/questions/16470527/warning-input-variables-exceeded-1000
    // Warning: Input variables exceeded 1000
    // $totalResults = json_decode($_POST['totalResults']);
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>PHP::Summary</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link href="styles.css" rel="stylesheet">
    </head>

    <body>
        <div class="logo">
            <img src="https://www.sevenseasworldwide.com/media/4203/25thlogo400x124.png" width="300" />
        </div>
        <div class="banner">
            <img src="https://www.sevenseasworldwide.com/media/3523/hongkong800.png?anchor=center&mode=crop&width=60&height=60&rnd=132690178250000000" width="40">
            　<i class="fa-solid fa-angle-left fa-2xl"></i><i class="fa-solid fa-question fa-2xl"></i><i class="fa-solid fa-equals fa-2xl"></i>　<i class="fa-solid fa-arrow-left fa-2xl"></i>　<i class="fa-solid fa-minus fa-2xl"></i>　<h3 class="display-4"> Summary </h3>　<i class="fa-solid fa-minus fa-2xl"></i>　<i class="fa-solid fa-arrow-right fa-2xl"></i>　<i class="fa-solid fa-question fa-2xl"></i><i class="fa-solid fa-angle-right fa-2xl"></i>　  
            <img src="https://www.sevenseasworldwide.com/media/3536/uk800.png?anchor=center&mode=crop&width=60&height=60&rnd=132690180520000000" width="40">
        </div>
<?php
    if (isset($_POST["showGrid"])) {
        $_SESSION["showGrid"] = $_POST["showGrid"];  
    } else {
        $_SESSION["showGrid"] = "show";
    }

    if (isset($_POST["showBox"])) {
        if ($_SESSION["showGrid"] == "show") {
            $_SESSION["showBox"] = (int)trim($_POST["showBox"]);
        } else {
            // echo "SSa: " . $_SESSION["showBox"] . "<BR>";
            $_SESSION["showBox"] = -1;
        }
    } else {
        // echo "SSb: " . $_SESSION["showBox"] . "<BR>";
        if (isset($_SESSION["showBox"])) {
            // do nothing
        } else {
            $_SESSION["showBox"] = -1;
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {



    }

?>
        <table class="packtable">
            <form name="pack-config" method="post" action="<?=$_SERVER["PHP_SELF"]?>" enctype="multipart/form-data"> 
                <tr?>
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
                </tr>
            </form>
        </table>
        <div class="tableFixHead">
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Box</th>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Value</th>
                        <th>Condition</th>
                        <th>Gender</th>
                        <th>Material</th>
                    </tr>
                 </thead>
                <tbody>
<?php
    if ($_SESSION["showGrid"] == "show") {

        if ($_SESSION["showBox"] !== -1) {
            $data = fetchOne($DB, (int)$_SESSION["showBox"]);
        } else {
            $data = fetchAll($DB);
        }

        $jsondata = json_encode($data);
        $array = json_decode( $jsondata, true);
        $i = 1;
        foreach ($array as $key => $jsons) { 
?>

                    <tr>
                        <td><?=$i++?></td>
                        <td><?=$jsons["box"] ?></td>
                        <td><?=$jsons["description"] ?></td>
                        <td><?=$jsons["quantity"] ?></td>
                        <td><?=$jsons["value"] ?></td>
                        <td><?=$jsons["conditio"] ?></td>
                        <td><?=$jsons["gender"] ?></td>
                        <td><?=$jsons["material"] ?></td>
                    </tr>
<?php
        }
    }
?>
                </tbody>
            </table>
        </div>
    </body>
</html>
<?php
    // session_unset();
    // session_destroy();
    $DB->closeConnection();
?>