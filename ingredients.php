<?php
include "connection.php";
include "header.php";

if(isset($_GET["item"])){
    $item = $_GET["item"];
}
else{
    echo '<script>window.location.assign("index")</script>';
}

$sql = "SELECT * FROM items WHERE item_id = '$item'";
$result = $conn->query($sql);
?>

<div class="container">
    <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                
                echo "<h4>Ingredients of: <br> &emsp;". $row["item_name"] ." </h4>";
                $ingredients = $row["Ingredients"];
                $ing = explode(", ", $ingredients);
                $listItems = "";
                foreach ($ing as $item) {
                    $listItems .= "<li class='list-group-item'>" . htmlspecialchars($item) . "</li>\n";
                }
                $unorderedList = "<ul class='list-group list-group-flush'>\n" . $listItems . "</ul>";
                echo $unorderedList;
                ?>
                <div class="col-3">
                                    <img src="<?= htmlspecialchars($row['item_pic']); ?>" alt="" style="width: 100%; height: auto; object-fit: cover; border-radius: 8px;">
                                </div>
                                <?php
            }
        }
        else{
            echo "No Ingredients Found!";
        }
    ?>
</div>

<?php
include "footer.php"
?>