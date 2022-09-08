<?php
include "database.php";

$page = isset($_GET['page']) ? $_GET['page'] : '';
?>
<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

        <title>BGL Grocery</title>
    </head>
    <body>
        <header>
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <a class="navbar-brand" href="#">BGL Grocery</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                    <div class="navbar-nav">
                    <a class="nav-item nav-link <?=($page=="") ? "active" : ""?>" href="<?=$_SERVER['PHP_SELF']?>">Home</a>
                    <a class="nav-item nav-link <?=($page=="product") ? "active" : ""?>" href="<?=$_SERVER['PHP_SELF']?>?page=product">Product</a>
                    <a class="nav-item nav-link <?=($page=="packoption") ? "active" : ""?>" href="<?=$_SERVER['PHP_SELF']?>?page=packoption">Packaging Option</a>
                    </div>
                </div>
            </nav>
        </header>
        <br/>
        <main>
            <div class="container">
                <?php
                if($page<>''){
                    include "page/$page.php";
                } else {
                ?>
                    <div class="row">
                        <div class="col-sm-4">
                            <h1>Order Testing</h1>
                            <form method="post" action="<?=$_SERVER['PHP_SELF']?>">
                                <?php
                                $db = new database();
                                $result_product = $db->select("product");
                                while($row_product = $result_product->fetch_assoc()){
                                ?>
                                    <div class="form-group row">
                                        <label for="<?=$row['code']?>" class="col-sm-5 col-form-label"><?=$row_product['code']?> (<?=$row_product['name']?>)</label>
                                        <div class="col-sm-7">
                                            <input type="number" class="form-control" id="<?=$row_product['code']?>" name="<?=$row_product['code']?>" value="<?=($_POST) ? $_POST[$row_product['code']]: '0'?>">
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                                <div class="form-group row">
                                    <div class="col-sm-5"></div>    
                                    <div class="col-sm-7">                            
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-sm-8">
                            <h1>Output</h1>
                            <?php
                            function ordercalculation($remaining_order_qty,$bundle_qty,$bundle_price){
                                $divison = floor($remaining_order_qty / $bundle_qty);
                                $remainder = $remaining_order_qty % $bundle_qty;
                                if($divison > 0 && $remainder >= 0){
                                    return $divison * $bundle_price;
                                }
                            }

                            foreach($_POST as $order_product => $order_qty){
                                if($order_qty > 0){
                                    $total_price = 0;
                                    $breakdown = "";
                                    $remaining_order_qty = $order_qty;
                                    $db = new database();
                                    $sql = "SELECT po.code, po.qty, po.price FROM packaging_option po WHERE po.code = '$order_product' 
                                            UNION 
                                            SELECT p.code, p.qty, p.price FROM product p WHERE p.code = '$order_product'
                                            ORDER BY qty DESC";
                                    $result = $db->select_custom($sql);
                                    while($row = $result->fetch_assoc()){
                                        $bundle_qty = $row['qty'];
                                        $bundle_price = $row['price'];
                                        $divison = floor($remaining_order_qty / $bundle_qty);
                                        $remainder = $remaining_order_qty % $bundle_qty;
                                        $total_price += ordercalculation($remaining_order_qty,$bundle_qty,$bundle_price);
                                        $remaining_order_qty = $remainder;
                                        if($divison > 0){
                                            $breakdown .= "<li>$divison ".($divison > 1 ? "packages" : "package")." of $bundle_qty ".($bundle_qty > 1 ? "items" : "item")." ($$bundle_price each)</li>";
                                        }
                                    }
                                    ?>
                                    <ul>
                                        <li>
                                            <?=$order_qty?> <?=$order_product?> for $<?=$total_price?>
                                            <ul>
                                            <?=$breakdown?>
                                            </ul>
                                        </li>
                                    </ul>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </main>
    </body>
</html>