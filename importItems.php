<?php
include "connection.php";
include "auth.php";
start_secure_session();
require_admin();

// Initialize message variables
$import_status = '';
$import_message = '';

if (isset($_POST["import"])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        header("Location: viewItems.php?import_status=error&import_message=" . urlencode("Invalid request token. Please refresh and try again."));
        exit;
    }

    // Check if file was uploaded
    if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
        $error_msg = "No file uploaded or upload error.";
        if (isset($_FILES['csv_file'])) {
            switch ($_FILES['csv_file']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $error_msg = "File is too large.";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $error_msg = "File was only partially uploaded.";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $error_msg = "No file was uploaded.";
                    break;
            }
        }
        header("Location: viewItems.php?import_status=error&import_message=" . urlencode($error_msg));
        exit;
    }

    // Validate file type
    $file_type = $_FILES['csv_file']['type'];
    $file_ext = strtolower(pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION));
    
    $allowed_exts = ['csv', 'txt'];
    $allowed_types = ['text/csv', 'text/plain', 'application/vnd.ms-excel', 'application/csv'];
    
    if (!in_array($file_ext, $allowed_exts) && !in_array($file_type, $allowed_types)) {
        header("Location: viewItems.php?import_status=error&import_message=" . urlencode("Invalid file type. Please upload a CSV file."));
        exit;
    }

    // Open and parse CSV file
    $csv_file = fopen($_FILES['csv_file']['tmp_name'], 'r');
    if (!$csv_file) {
        header("Location: viewItems.php?import_status=error&import_message=" . urlencode("Could not open uploaded file."));
        exit;
    }

    // Detect and skip BOM if present
    $bom = fread($csv_file, 3);
    if ($bom !== "\xEF\xBB\xBF") {
        rewind($csv_file);
    }

    // Read header row
    $headers = fgetcsv($csv_file);
    if (!$headers) {
        fclose($csv_file);
        header("Location: viewItems.php?import_status=error&import_message=" . urlencode("Could not read CSV headers."));
        exit;
    }

    // Normalize headers (lowercase, trim)
    $headers = array_map(function($h) {
        return strtolower(trim($h));
    }, $headers);

    // Map column positions
    $col_map = [
        'id' => array_search('id', $headers),
        'name' => array_search('name', $headers),
        'category' => array_search('category', $headers),
        'price lbp' => array_search('price lbp', $headers),
        'price usd' => array_search('price usd', $headers),
        'ingredients' => array_search('ingredients', $headers),
        'image path' => array_search('image path', $headers)
    ];

    // Validate required columns
    if ($col_map['name'] === false || $col_map['category'] === false) {
        fclose($csv_file);
        header("Location: viewItems.php?import_status=error&import_message=" . urlencode("CSV must contain 'Name' and 'Category' columns."));
        exit;
    }

    // Process rows
    $updated_count = 0;
    $inserted_count = 0;
    $error_count = 0;
    $error_details = [];
    $row_num = 1; // Start at 1 since header is row 0

    while (($row = fgetcsv($csv_file)) !== false) {
        $row_num++;

        // Skip empty rows
        if (empty($row) || (count($row) === 1 && trim($row[0]) === '')) {
            continue;
        }

        // Extract values
        $id = ($col_map['id'] !== false && isset($row[$col_map['id']])) ? trim($row[$col_map['id']]) : '';
        $name = ($col_map['name'] !== false && isset($row[$col_map['name']])) ? trim($row[$col_map['name']]) : '';
        $category = ($col_map['category'] !== false && isset($row[$col_map['category']])) ? trim($row[$col_map['category']]) : '';
        $price_lbp = ($col_map['price lbp'] !== false && isset($row[$col_map['price lbp']])) ? trim($row[$col_map['price lbp']]) : '';
        $price_usd = ($col_map['price usd'] !== false && isset($row[$col_map['price usd']])) ? trim($row[$col_map['price usd']]) : '';
        $ingredients = ($col_map['ingredients'] !== false && isset($row[$col_map['ingredients']])) ? trim($row[$col_map['ingredients']]) : '';
        $image_path = ($col_map['image path'] !== false && isset($row[$col_map['image path']])) ? trim($row[$col_map['image path']]) : '';

        // Validate required fields
        if (empty($name)) {
            $error_count++;
            $error_details[] = "Row $row_num: Item name is required";
            continue;
        }

        if (empty($category)) {
            $error_count++;
            $error_details[] = "Row $row_num: Category is required";
            continue;
        }

        // Validate prices are numeric
        if ($price_lbp !== '' && (!is_numeric($price_lbp) || $price_lbp < 0)) {
            $error_count++;
            $error_details[] = "Row $row_num: LBP price must be a positive number";
            continue;
        }

        if ($price_usd !== '' && (!is_numeric($price_usd) || $price_usd < 0)) {
            $error_count++;
            $error_details[] = "Row $row_num: USD price must be a positive number";
            continue;
        }

        // Validate category exists
        $cat_stmt = $conn->prepare("SELECT COUNT(*) FROM categories WHERE cat_name = ?");
        $cat_stmt->bind_param("s", $category);
        $cat_stmt->execute();
        $cat_stmt->bind_result($cat_exists);
        $cat_stmt->fetch();
        $cat_stmt->close();

        if (!$cat_exists) {
            $error_count++;
            $error_details[] = "Row $row_num: Category '$category' does not exist";
            continue;
        }

        // Convert prices to proper types
        $price_lbp = ($price_lbp === '') ? 0 : (int)$price_lbp;
        $price_usd = ($price_usd === '') ? 0 : (float)$price_usd;

        // Check if item exists by ID
        $item_exists = false;
        if (!empty($id) && is_numeric($id)) {
            $check_stmt = $conn->prepare("SELECT COUNT(*) FROM items WHERE item_id = ?");
            $check_stmt->bind_param("i", $id);
            $check_stmt->execute();
            $check_stmt->bind_result($item_exists);
            $check_stmt->fetch();
            $check_stmt->close();
        }

        if ($item_exists) {
            // Update existing item
            $update_stmt = $conn->prepare("UPDATE items SET item_name = ?, item_category = ?, item_pricelbp = ?, item_priceusd = ?, Ingredients = ?, item_pic = ? WHERE item_id = ?");
            $update_stmt->bind_param("ssidsis", $name, $category, $price_lbp, $price_usd, $ingredients, $image_path, $id);
            
            if ($update_stmt->execute()) {
                $updated_count++;
            } else {
                $error_count++;
                $error_details[] = "Row $row_num: Update failed - " . $update_stmt->error;
            }
            $update_stmt->close();
        } else {
            // Insert new item
            $insert_stmt = $conn->prepare("INSERT INTO items (item_name, item_category, item_pricelbp, item_priceusd, Ingredients, item_pic) VALUES (?, ?, ?, ?, ?, ?)");
            $insert_stmt->bind_param("ssidsi", $name, $category, $price_lbp, $price_usd, $ingredients, $image_path);
            
            if ($insert_stmt->execute()) {
                $inserted_count++;
            } else {
                $error_count++;
                $error_details[] = "Row $row_num: Insert failed - " . $insert_stmt->error;
            }
            $insert_stmt->close();
        }
    }

    fclose($csv_file);

    // Build success message
    $success_parts = [];
    if ($inserted_count > 0) {
        $success_parts[] = "$inserted_count item(s) added";
    }
    if ($updated_count > 0) {
        $success_parts[] = "$updated_count item(s) updated";
    }

    $final_message = '';
    if (!empty($success_parts)) {
        $final_message = implode(', ', $success_parts);
        if ($error_count > 0) {
            $final_message .= ". $error_count error(s) occurred.";
        }
        header("Location: viewItems.php?import_status=success&import_message=" . urlencode($final_message));
    } else {
        if ($error_count > 0) {
            $final_message = "Import completed with $error_count error(s).";
            // Store first few error details in session if needed
            $_SESSION['import_errors'] = array_slice($error_details, 0, 5);
        } else {
            $final_message = "No items were imported.";
        }
        header("Location: viewItems.php?import_status=error&import_message=" . urlencode($final_message));
    }
    exit;
}

// If accessed directly without POST, redirect back
header("Location: viewItems.php");
exit;
?>
