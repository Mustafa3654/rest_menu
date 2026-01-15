<?php 
include "connection.php";
session_start();
include "header.php";
if($_SESSION["isAdmin"] != true){
    echo '<script>window.location.assign("index")</script>';
}
?>

<?php
    $item = $_GET["item"] ?? null;

    if (isset($_POST["submit"])) {

        $id = $_POST["id"];
        $name = $_POST['categories'];

      
        $sql = "UPDATE `items` SET `item_category`='$name' WHERE item_id = '$id'";
      
        $result = mysqli_query($conn, $sql);
      
        if ($result) {
          
        } else {
          echo "Failed: " . mysqli_error($conn);
        }
      }

    if($item != null){
        $sql = "SELECT * FROM items WHERE (item_name='$item')";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
    }
    
?>

<div class="container">
    <h2>Edit Item</h2>
    <form method="POST" action="editItemCategory.php">

        <input type="hidden" name="id" value="<?php echo $row["item_id"] ?>">

        <select class="form-control" name="categories" id="categories">
                <?php 
                    $sql = "SELECT * FROM categories";
                    $result = mysqli_query($conn, $sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                        ?>
                        <option value="<?php echo $row["cat_name"] ?>"><?php echo $row["cat_name"] ?></option>
                <?php
                        }
                    }
                        
                ?>
        </select><br>

        <input class="btn btn-primary" type="submit" value="Update" name="submit">
    </form>
</div>

    <?php
    // Close database connection
    mysqli_close($conn);
    ?>

<?php include "footer.php" ?>