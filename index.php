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
                                            <input type="number" class="form-control" id="<?=$row_product['code']?>" name="<?=$row_product['code']?>" min="0" value="<?=($_POST) ? $_POST[$row_product['code']]: '0'?>">
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
                            foreach($_POST as $order_product => $order_qty){
                                $minimum_pack = array();
                                if($order_qty > 0){
                                    $package = array();
                                    $db = new database();
                                    $sql = "SELECT po.code, po.qty, po.price FROM packaging_option po WHERE po.code = '$order_product' 
                                            UNION 
                                            SELECT p.code, p.qty, p.price FROM product p WHERE p.code = '$order_product'
                                            ORDER BY qty DESC";
                                    $result = $db->select_custom($sql);
                                    while($row = $result->fetch_assoc()){
                                        $breakdown = "";
                                        $total_price = 0;
                                        $total_package = 0;
                                        $remaining_order_qty = $order_qty;
                                        $bundle_qty = $row['qty'];
                                        $bundle_price = $row['price'];

                                        $divison = floor($remaining_order_qty / $bundle_qty);
                                        if($divison > 0){
                                            $total_price += $divison * $bundle_price;
                                            $breakdown .= "<li>$divison ".($divison > 1 ? "packages" : "package")." of $bundle_qty ".($bundle_qty > 1 ? "items" : "item")." ($$bundle_price each)</li>";
                                            $total_package += $divison;
                                        }
                                        
                                        $remaining_order_qty = $remaining_order_qty % $bundle_qty;

                                        // end the loop when there is no more qty remaining

                                        if($remaining_order_qty > 0){
                                            $sql_sub = "SELECT po.code, po.qty, po.price FROM packaging_option po WHERE po.code = '$order_product' AND po.qty < $bundle_qty
                                                UNION 
                                                SELECT p.code, p.qty, p.price FROM product p WHERE p.code = '$order_product'
                                                ORDER BY qty DESC";
                                            $result_sub = $db->select_custom($sql_sub);
                                            while($row_sub = $result_sub->fetch_assoc()){
                                                $bundle_qty_sub = $row_sub['qty'];
                                                $bundle_price_sub = $row_sub['price'];
                                                $divison_sub = floor($remaining_order_qty / $bundle_qty_sub);
                                                if($divison_sub > 0){
                                                    $total_price += $divison_sub * $bundle_price_sub;
                                                    $breakdown .= "<li>$divison_sub ".($divison_sub > 1 ? "packages" : "package")." of $bundle_qty_sub ".($bundle_qty_sub > 1 ? "items" : "item")." ($$bundle_price_sub each)</li>";
                                                    $total_package += $divison_sub;
                                                }
                                                $remaining_order_qty = $remaining_order_qty % $bundle_qty_sub;
                                                // end the loop when there is no more qty remaining
                                                if($remaining_order_qty == 0){
                                                    break;
                                                }
                                            }
                                        }

                                        $minimum_pack[] = $total_package;
                                        $package[$total_package]['total_price'] = $total_price;
                                        $package[$total_package]['breakdown'] = $breakdown;


                                        /*

                                        intial qty = 80
                                        70 -> intial qty 10 --> 1pack
                                            20 -> initial qty 10 -> 0pack
                                            1 -> intial qty 0 -> 10pack
                                        
                                        total 11packs
                                        
                                        intial qty = 80
                                        20 -> intial qty = 0, 4pack
                                            1 break loop
                                        total 4packs

                                        intial qty = 80
                                        1
                                        total 80pack

                                        4
                                        11
                                        80

                                        return array[0]
                                    

                                        To solve the breakdown, firstly the pack options need to be sorted in descending order.
                                        
                                        Order 1: Cheese with 10 qty
                                        
                                        Pack Opt|  Divison  | Remainder
                                        -------------------------------
                                            5   |     2     |   0

                                        Order 2: Ham with 14 qty
                                        
                                        Pack Opt|  Divison  | Remainder
                                        -------------------------------
                                            8   |     1     |   6
                                            5   |     1     |   1
                                            2   |     0     |   1   ---> doesnt count as it has least than 2
                                            1   |     1     |   0  

                                        Order 3: Soy Sauce with 3 qty
                                        
                                        Pack Opt|  Divison  | Remainder
                                        -------------------------------
                                            1   |     3     |   0
                                        

                                        Order 4: Product 1 with 80 qty
                                        intial qty = 80
                                        Pack Opt|  Divison  | Remainder
                                        -------------------------------
                                            70   |     1     |   10 --> initial qty = 10
                                            20   |     0     |   10 --> initial qty = 10
                                            1   |     10     |   0 --> initial qty = 0
                                            

                                        initial qty = 80
                                        Pack Opt|  Divison  | Remainder
                                        -------------------------------
                                            70   |     1     |   10 --> fail, initial qty 80
                                            20   |     4     |   0 --> success, initial qty = 0
                                            1   |     10     |   0

                                        initial qty = 80
                                        Pack Opt|  Divison  | Remainder
                                        -------------------------------
                                            70   |     1     |   10 --> fail, initial qty 80
                                            19   |     4     |   4 --> fail, initial qty = 80
                                            1   |     80     |   0 --> success
                                        

                                        8
                                            */
                                    }
                                    
                                    // sorting the number of package in ascending order 
                                    sort($minimum_pack);        
                                    
                                    ?>
                                    <ul>
                                        <li>
                                            <?=$order_qty?> <?=$order_product?> for $<?=$package[$minimum_pack[0]]['total_price'];?>
                                            <ul>
                                            <?=$package[$minimum_pack[0]]['breakdown'];?>
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