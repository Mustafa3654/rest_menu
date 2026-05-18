<?php
include "../includes/connection.php";
include "../includes/auth.php";
start_secure_session();
require_admin();

// -------------------------
// Read filters
// -------------------------
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$csrfToken = ensure_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Categories</title>
    <link rel="stylesheet" href="../style/tailwind.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body class="bg-[#F7F5EA] font-poppins min-h-screen pt-16 flex justify-center">
    <div class="bg-white p-8 rounded-2xl border border-[#CBB58B] shadow-lg w-[550px] mx-4">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-[#42522B] text-center text-2xl font-bold m-0">View Categories</h1>
            <a href="dashboard" class="bg-[#42522B] text-white px-5 py-2.5 rounded font-bold no-underline transition-all duration-300 hover:bg-[#2b3a1d] hover:-translate-y-0.5 hover:shadow-md">BACK</a>
        </div>
        
        <div class="bg-[#F7F5EA] border border-[rgba(203,181,139,0.4)] rounded-lg p-5 flex gap-5 items-center flex-wrap mb-5">
            <form method="GET" class="flex-1 min-w-[200px]">
                <input type="text" name="search" placeholder="Search categories by name..." value="<?php echo htmlspecialchars($search); ?>" class="w-full p-2.5 border border-[#CBB58B] rounded text-sm bg-white">
            </form>
        </div>

        <?php
        $sql = "SELECT * FROM categories WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($search)) {
            $sql .= " AND cat_name LIKE ?";
            $params[] = "%$search%";
            $types .= "s";
        }

        $sql .= " ORDER BY cat_name";

        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo '<div class="flex flex-col gap-4">';
            echo '<div class="grid grid-cols-[1.5fr_1fr_1fr] items-center text-center p-4 rounded-lg bg-[#42522B] text-white font-bold border-none">';
            echo '<span>Category Name</span>';
            echo '<span>Edit</span>';
            echo '<span>Delete</span>';
            echo '</div>';

            while($row = $result->fetch_assoc()) {
                echo "<div class='grid grid-cols-[1.5fr_1fr_1fr] items-center text-center p-4 rounded-lg bg-[#F7F5EA] border border-[rgba(203,181,139,0.3)] font-medium text-[#2B2B2A]'>
                        <span>".htmlspecialchars($row["cat_name"])."</span>
                        <span><a href='editCategory?category=" . urlencode($row["cat_name"]) . "' class='text-[#42522B] text-lg transition-colors hover:text-[#2b3a1d]'><i class='fas fa-pen'></i></a></span>
                        <span>
                            <form method='POST' action='deleteCategory' style='display:inline;' onsubmit='return confirm(\"Are you sure?\");'>
                                " . csrf_input() . "
                                <input type='hidden' name='id' value='" . (int)$row["cat_id"] . "'>
                                <button type='submit' style='background:none;border:none;padding:0;cursor:pointer;' class='text-[#e74c3c] text-lg transition-colors hover:text-[#c0392b]'><i class='fas fa-trash'></i></button>
                            </form>
                        </span>
                      </div>";
            }
            echo '</div>';
        } else {
            echo "<div class='alert-custom alert-custom-info text-center p-5'>No categories found.</div>";
        }
        $stmt->close();
        ?>
    </div>
</body>
</html>
