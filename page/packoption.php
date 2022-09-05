<?php
$subpage = isset($_GET['subpage']) ? $_GET['subpage'] : '';
$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';

if ($action=="add") {
    $code = $_POST['code'];
    $qty = $_POST['qty'];
    $price = $_POST['price'];
    $db = new database();
    $para = array(
        "code" => "$code",
        "qty" => "$qty",
        "price" => $price
    );
    $query = $db->insert("packaging_option",$para);
    header("Location: http://localhost/bgl?page=packoption");
    exit;
} else if($action=="update") {
    $id = $_POST['id'];
    $qty = $_POST['qty'];
    $price = $_POST['price'];
    $db = new database();
    $para = array(
        "qty" => "$qty",
        "price" => $price
    );
    $query = $db->update("packaging_option",$para,"id = $id");
    header("Location: http://localhost/bgl?page=packoption");
    exit;
} else if($action=="delete") {
    $db = new database();
    $db->delete("packaging_option", "id = $id");
    header("Location: http://localhost/bgl?page=packoption");
    exit;
}

if($subpage=="add"){
?>
    <h1>Add Packaging Option</h1>
    <form method="post" action="<?=$_SERVER['PHP_SELF']?>?page=packoption&action=add">
        <div class="form-group">
            <label for="code">Code</label>
            <select class="form-control" id="code" name="code" required="required">
                <?php
                
                $db = new database();
                $result_product = $db->select("product");
                while($row_product = $result_product->fetch_assoc()){
                ?>
                    <option value="<?=$row_product['code']?>"><?=$row_product['code']?> (<?=$row_product['name']?>)</option?>
                <?php
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="qty">Qty</label>
            <input type="number" class="form-control" id="qty" name="qty" required="required">
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
    $result = $db->select("packaging_option","*","id = '$id'");
    $row = $result->fetch_assoc();
?>
    <h1>Update Packaging Option</h1>
    <form method="post" action="<?=$_SERVER['PHP_SELF']?>?page=packoption&action=update">
        <input type="hidden" name="id" value="<?=$row['id']?>"/>
        <div class="form-group">
            <label for="code">Code</label>
            <input type="text" class="form-control" required="required" readonly="readonly" value="<?=$row['code']?>">
        </div>
        <div class="form-group">
            <label for="qty">Qty</label>
            <input type="number" class="form-control" id="qty" name="qty" required="required" value=<?=$row['qty']?>>
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
    <h1>Packaging Option</h1>
    <a href="<?=$_SERVER['PHP_SELF']?>?page=packoption&subpage=add">Add</a>
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th>Code</th>
                <th>Packaging Options</th>
                <th width="20%"></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $db = new database();
            $result = $db->select("packaging_option","*","","code, qty ASC");
            $codex = "";
            while($row = $result->fetch_assoc()){
            ?>
                <tr>
                    <td><?=($codex<>$row['code']) ? $row['code'] : ''?></td>
                    <td><?=$row['qty']?> for $<?=$row['price']?></td>
                    <td>
                        <a href="<?=$_SERVER['PHP_SELF']?>?page=packoption&id=<?=$row['id']?>&subpage=update">Update</a>
                        <a href="<?=$_SERVER['PHP_SELF']?>?page=packoption&id=<?=$row['id']?>&action=delete" onclick="return confirm('Are you sure to delete');" >Delete</a>
                    </td>
                </tr>
            <?php
                $codex = $row['code'];
            }
            ?>
        </tbody>
    </table>
<?php
}
?>