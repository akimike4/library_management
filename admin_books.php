<?php include('db.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Books - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php 
    include('header.php');
    include('sidebar.php'); 
?>

<div class="content">
    <div class="container">
        <h2>Book Inventory</h2>

        <?php
        if (isset($_POST['add_book'])) {
            $code = strtoupper(mysqli_real_escape_string($conn, $_POST['book_code']));
            $title = strtoupper(mysqli_real_escape_string($conn, $_POST['book_title']));
            
            // Check if a row exists where BOTH match exactly
            $check_duplicate = mysqli_query($conn, "SELECT id FROM books WHERE book_code = '$code' AND book_title = '$title'");
            
            if (mysqli_num_rows($check_duplicate) > 0) {
                echo "<script>alert('Error: This exact Book Code and Title combination already exists!');</script>";
            } else {
                $query = "INSERT INTO books (book_code, book_title) VALUES ('$code', '$title')";
                $result = mysqli_query($conn, $query);

                if ($result) {
                    header("Location: admin_books.php");
                } else {
                    echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
                }
            }
        }
        ?>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 15px;">
            <form method="GET" style="flex: 1; border: none; padding: 0; box-shadow: none; margin-bottom: 0;">
                <input type="text" name="search" placeholder="Search title or code..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>" style="width: 70%;" oninput="this.value = this.value.toUpperCase()">
                <button type="submit" class="btn-primary">Search</button>
            </form>

            <form method="POST" style="padding: 15px; border-radius: 12px; display: flex; gap: 10px; align-items: center; margin-bottom: 0; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1);">
                <strong style="font-size: 11px; text-transform: uppercase; color: #ffcc00;">Add Book:</strong>
                
                <input type="text" 
                       name="book_code" 
                       value="00000001" 
                       required 
                       style="width: 120px;" 
                       oninput="this.value = this.value.toUpperCase()">
                
                <input type="text" 
                       name="book_title" 
                       placeholder="Enter Book Title" 
                       required 
                       oninput="this.value = this.value.toUpperCase()">
                
                <button type="submit" name="add_book" class="btn-primary">+</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 20%;">Code</th>
                    <th>Book Title</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
                $filter = $search ? "WHERE book_title LIKE '%$search%' OR book_code LIKE '%$search%'" : "";
                
                $res = mysqli_query($conn, "SELECT * FROM books $filter ORDER BY book_title ASC");
                
                while($row = mysqli_fetch_assoc($res)) {
                    echo "<tr>
                        <td><code>{$row['book_code']}</code></td>
                        <td style='text-transform: uppercase; font-weight: 700;'>{$row['book_title']}</td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>