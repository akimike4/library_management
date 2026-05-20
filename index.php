<?php include('db.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Library System - Dashboard</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


</head>
<body>

<?php 
    include('header.php');
    include('sidebar.php');
?>

<div class="content">
    <div style="width: 100%; max-width: 1300px;"> 
        
        <div class="stats-grid">
            <?php
            $total_books_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM books");
            $total_books = mysqli_fetch_assoc($total_books_res)['total'];

            $borrowed_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM borrowings WHERE status = 'Borrowed'");
            $total_borrowed = mysqli_fetch_assoc($borrowed_res)['total'];

            $available_books = $total_books - $total_borrowed;
            ?>

            <div class="stat-card">
                <span class="stat-label">Total Books</span>
                <span class="stat-value"><?php echo $total_books; ?></span>
            </div>
            <div class="stat-card">
                <span class="stat-label">Currently Borrowed</span>
                <span class="stat-value" style="color: #ffa000;"><?php echo $total_borrowed; ?></span>
            </div>
            <div class="stat-card">
                <span class="stat-label">Available Books</span>
                <span class="stat-value" style="color: #2e7d32;"><?php echo $available_books; ?></span>
            </div>
            <div class="stat-card">
                <span class="stat-label">Not Yet Returned</span>
                <span class="stat-value" style="color: #ff6b6b;"><?php echo $total_borrowed; ?></span>
            </div>
        </div>

        <div class="container">
            <h2>Active Borrowing Records</h2>

            <?php
            if (isset($_POST['add_borrowing'])) {
                $name = mysqli_real_escape_string($conn, $_POST['name']);
                $role = $_POST['role'];
                $book_id = $_POST['book_id'];

                $check_status = mysqli_query($conn, "SELECT status FROM borrowings WHERE book_id = '$book_id' AND status = 'Borrowed'");
                
                if (mysqli_num_rows($check_status) > 0) {
                    echo "<script>alert('Error: This book is currently unavailable.');</script>";
                } else {
                    mysqli_query($conn, "INSERT INTO borrowings (name, role, book_id, status) VALUES ('$name', '$role', '$book_id', 'Borrowed')");
                    header("Location: index.php");
                }
            }

            if (isset($_GET['return_id'])) {
                $id = $_GET['return_id'];
                $now = date('Y-m-d H:i:s');
                mysqli_query($conn, "UPDATE borrowings SET status='Returned', return_date='$now' WHERE id=$id");
                header("Location: index.php");
            }
            ?>

            <form method="POST" style="margin-bottom: 30px; display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">
                <input type="text" name="name" placeholder="Borrower Name" required oninput="this.value = this.value.toUpperCase()">
                
                <select name="role" style="max-width: 150px;">
                    <option value="Student">Student</option>
                    <option value="Staff">Staff</option>
                </select>
                
                <select name="book_id" class="searchable-book" required>
                    <option value="">Search Book Title...</option>
                    <?php
                    $books_query = "SELECT b.id, b.book_title, 
                                   (SELECT status FROM borrowings WHERE book_id = b.id AND status = 'Borrowed' LIMIT 1) as current_status 
                                   FROM books b ORDER BY b.book_title ASC";
                    $books = mysqli_query($conn, $books_query);
                    
                    while($b = mysqli_fetch_assoc($books)) {
                        $is_borrowed = ($b['current_status'] == 'Borrowed');
                        $disabled = $is_borrowed ? "disabled" : "";
                        $label = $is_borrowed ? " [ALREADY BORROWED]" : "";
                        
                        echo "<option value='{$b['id']}' $disabled>{$b['book_title']}$label</option>";
                    }
                    ?>
                </select>

                <button type="submit" name="add_borrowing" class="btn-primary">Log Borrowing</button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th style="width: 20%;">Borrower</th>
                        <th style="width: 10%;">Code</th>
                        <th style="width: 30%;">Book Title</th>
                        <th style="width: 15%;">Borrowed</th>
                        <th style="width: 15%;">Returned</th>
                        <th style="width: 10%;">Status</th>
                        <th style="width: 10%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT borrowings.*, books.book_code, books.book_title 
                            FROM borrowings 
                            JOIN books ON borrowings.book_id = books.id 
                            ORDER BY borrow_date DESC";
                    $res = mysqli_query($conn, $sql);
                    while($row = mysqli_fetch_assoc($res)) {
                        $statusClass = ($row['status'] == 'Borrowed') ? 'borrowed' : 'returned';
                        $borrowedDate = date('d M Y', strtotime($row['borrow_date']));
                        $returnedDate = ($row['return_date']) ? date('d M Y', strtotime($row['return_date'])) : "---";

                        echo "<tr>
                            <td><b>{$row['name']}</b> ({$row['role']})</td>
                            <td><code>{$row['book_code']}</code></td>
                            <td>{$row['book_title']}</td>
                            <td>$borrowedDate</td>
                            <td>$returnedDate</td>
                            <td><span class='status-badge $statusClass'>{$row['status']}</span></td>
                            <td>";
                        if($row['status'] == 'Borrowed') {
                            echo "<a href='index.php?return_id={$row['id']}'>Return</a>";
                        }
                        echo "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.searchable-book').select2({
        placeholder: "Type book title...",
        allowClear: true
    });
});
</script>

<script>
$(document).ready(function() {
    $('.searchable-book').select2({
        placeholder: "SEARCH BOOK TITLE...",
        allowClear: true, 
        width: 'resolve'
    });
});
</script>

</body>
</html>