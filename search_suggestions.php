
<?php
include 'db_connect.php';

$q = isset($_GET['q']) ? $_GET['q'] : '';


$sql = "SELECT id, name FROM products WHERE name LIKE ? LIMIT 5";
$stmt = $conn->prepare($sql);
$searchTerm = "%".$q."%";
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<ul style='list-style:none; margin:0; padding:0;'>";
    while($row = $result->fetch_assoc()) {
        echo "<li><a href='product_detail.php?id=".$row['id']."'>".$row['name']."</a></li>";
    }
    echo "</ul>";
} else {
    echo "<p>Nenhum resultado encontrado</p>";
}
?>
