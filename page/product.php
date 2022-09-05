<?php
$subpage = isset($_GET['subpage']) ? $_GET['subpage'] : '';
$action = isset($_GET['action']) ? $_GET['action'] : '';
$code = isset($_GET['code']) ? $_GET['code'] : '';

if ($action=="add") {
    $code = $_POST['code'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $db = new database();
    $para = array(
        "code" => "$code",
        "name" => "$name",
        "price" => $price
    );
    $query = $db->insert("product",$para);
    header("Location: http://localhost/bgl?page=product");
    exit;
} else if($action=="update") {
    $code = $_POST['code'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $db = new database();
    $para = array(
        "name" => "$name",
        "price" => $price
    );
    $query = $db->update("product",$para,"code = '$code'");
    header("Location: http://localhost/bgl?page=product");
    exit;
} else if($action=="delete") {
    $db = new database();
    $db->delete("product","code = '$code'");
    header("Location: http://localhost/bgl?page=product");
    exit;
}

if($subpage=="add"){
?>
    <h1>Add Product</h1>
    <form method="post" action="<?=$_SERVER['PHP_SELF']?>?page=product&action=add">
        <div class="form-group">
            <label for="code">Code</label>
            <input type="text" class="form-control" id="code" name="code" maxlength="2" required="required">
        </div>
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" required="required">
        </div>
        <div class="form-group">
            <label for="price">Price</label>
            <input type="number" class="form-control" id="price" name="price" step="any" required="required">
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
<?php
} else if($subpage=="update"){
    $db = new database();
    $result = $db->select("product","*","code = '$code'");
    $row = $result->fetch_assoc();
?>
    <h1>Update Product</h1>
    <form method="post" action="<?=$_SERVER['PHP_SELF']?>?page=product&action=update">
        <input type="hidden" name="code" value="<?=$row['code']?>"/>
        <div class="form-group">
            <label for="code">Code</label>
            <input type="text" class="form-control" required="required" readonly="readonly" value="<?=$code?>">
        </div>
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" required="required" value=<?=$row['name']?>>
        </div>
        <div class="form-group">
            <label for="price">Price</label>
            <input type="number" class="form-control" id="price" name="price" step="any" required="required" value="<?=$row['price']?>">
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Update</button>
        </div>
    </form>
<?php
} else {
?>
    <h1>Product</h1>
    <a href="<?=$_SERVER['PHP_SELF']?>?page=product&subpage=add">Add</a>
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Price</th>
                <th width="20%"></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $db = new database();
            $result = $db->select("product");
            while($row = $result->fetch_assoc()){
            ?>
                <tr>
                    <td><?=$row['code']?></td>
                    <td><?=$row['name']?></td>
                    <td>$<?=$row['price']?></td>
                    <td>
                        <a href="<?=$_SERVER['PHP_SELF']?>?page=product&code=<?=$row['code']?>&subpage=update">Update</a>
                        <a href="<?=$_SERVER['PHP_SELF']?>?page=product&code=<?=$row['code']?>&action=delete" onclick="return confirm('Are you sure to delete <?=$row['name']?>');" >Delete</a>
                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
<?php
}
?>