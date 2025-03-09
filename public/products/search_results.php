<?php
require_once "../../includes/database/config.php"; 

if (isset($_GET['query']) && !empty($_GET['query'])) {
    $searchQuery = htmlspecialchars($_GET['query']); 

    $sql = "
    SELECT p.id, p.name, p.description, p.price, p.image, c.name AS category_name 
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE (p.name LIKE :searchTerm OR p.description LIKE :searchTerm OR c.name LIKE :searchTerm)
    AND p.is_active = 1"; 

    $stmt = $pdo->prepare($sql); 
    $searchTerm = "%$searchQuery%";
    $stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR); 

    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) > 0) {
        header("Location: shop.php?query=" . urlencode($searchQuery));
        exit; 
    } else {
        header("Location: shop.php?query=" . urlencode($searchQuery) . "&no_results=1");
        exit; 
    }

} else {
    header("Location: shop.php?error=empty_query");
    exit; 
}
?>
