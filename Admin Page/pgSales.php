<?php
@include '../conn/config.php';

// Default query to fetch all sales data
$query = "SELECT salesID, memberID, date, amount FROM tblsales";

// Check if month and year filters are set
if (isset($_GET['month']) && isset($_GET['year'])) {
    $month = $_GET['month'];
    $year = $_GET['year'];

    // Modify the query to filter by selected month and year
    if ($month != 0) {
        $query .= " WHERE MONTH(date) = $month";
        if ($year != 0) {
            $query .= " AND YEAR(date) = $year";
        }
    } else if ($year != 0) {
        $query .= " WHERE YEAR(date) = $year";
    }
}

$result = mysqli_query($conn, $query);

// Array to store the unique years
$years = array();

// Fetch each row and store the years in the array
$yquery = "SELECT DISTINCT YEAR(date) AS year FROM tblsales";
$yresult = mysqli_query($conn, $yquery);
while ($row = mysqli_fetch_assoc($yresult)) {
    $years[] = $row['year'];
}

// Free the result set
mysqli_free_result($yresult);

$salesData = array();
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $salesData[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="icon" type="image/png" href="../WebImages/logo.jpg">
    <link rel="stylesheet" href="../Styles/styleSales.css">
    <title>Sales</title>
</head>

<body>
    <nav>
        <div class="icon"><i class='fas fa-dumbbell'></i></div>
        <div class="logo">VFlex</div>
    </nav>

    <div class="main">
        <div class="header">
            <div class="back">
                <a href="AdminPage.php"><i class="fas fa-arrow-left"></i> Back</a>
            </div>

            <div class="filter">
                <h3 style="margin-right: 10px;">FILTER:</h3>
                <label for="month">Month:</label>
                <select id="month">
                    <option value="0" <?php if (isset($_GET['month']) && $_GET['month'] == 0) echo ' selected'; ?>>ALL</option>
                    <option value="1" <?php if (isset($_GET['month']) && $_GET['month'] == 1) echo ' selected'; ?>>January</option>
                    <option value="2" <?php if (isset($_GET['month']) && $_GET['month'] == 2) echo ' selected'; ?>>February</option>
                    <option value="3" <?php if (isset($_GET['month']) && $_GET['month'] == 3) echo ' selected'; ?>>March</option>
                    <option value="4" <?php if (isset($_GET['month']) && $_GET['month'] == 4) echo ' selected'; ?>>April</option>
                    <option value="5" <?php if (isset($_GET['month']) && $_GET['month'] == 5) echo ' selected'; ?>>May</option>
                    <option value="6" <?php if (isset($_GET['month']) && $_GET['month'] == 6) echo ' selected'; ?>>June</option>
                    <option value="7" <?php if (isset($_GET['month']) && $_GET['month'] == 7) echo ' selected'; ?>>July</option>
                    <option value="8" <?php if (isset($_GET['month']) && $_GET['month'] == 8) echo ' selected'; ?>>August</option>
                    <option value="9" <?php if (isset($_GET['month']) && $_GET['month'] == 9) echo ' selected'; ?>>September</option>
                    <option value="10" <?php if (isset($_GET['month']) && $_GET['month'] == 10) echo ' selected'; ?>>October</option>
                    <option value="11" <?php if (isset($_GET['month']) && $_GET['month'] == 11) echo ' selected'; ?>>November</option>
                    <option value="12" <?php if (isset($_GET['month']) && $_GET['month'] == 12) echo ' selected'; ?>>December</option>
                </select>

                <label for="year">Year:</label>
                <select id="year">
                    <option value="0" <?php if (isset($_GET['year']) && $_GET['year'] == 0) echo ' selected'; ?>>ALL</option>
                    <?php
                    foreach ($years as $year) {
                        $selected = (isset($_GET['year']) && $_GET['year'] == $year) ? ' selected' : '';
                        echo "<option value='$year'$selected>$year</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="total-con">
                <h3>Total Amount: <?php echo array_sum(array_column($salesData, 'amount')); ?></h3>
            </div>
        </div>

        <div class="table-con">
            <table class="table">
                <thead>
                    <tr>
                        <th>Sales ID</th>
                        <th>Member ID</th>
                        <th>Date</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Check if there are any sales records
                    if (!empty($salesData)) {
                        // Output data of each row
                        foreach ($salesData as $row) {
                            echo "<tr>";
                            echo "<td>" . $row["salesID"] . "</td>";
                            echo "<td>" . $row["memberID"] . "</td>";
                            echo "<td>" . $row["date"] . "</td>";
                            echo "<td>" . $row["amount"] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' style='text-align: center;'>No sales records found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer>&copy; 2024 VFlex Fitness Gym System. All rights reserved.</footer>

    <script>
        // JavaScript to handle filter change events
        document.getElementById('month').addEventListener('change', function() {
            filterSales();
        });

        document.getElementById('year').addEventListener('change', function() {
            filterSales();
        });

        function filterSales() {
            var month = document.getElementById('month').value;
            var year = document.getElementById('year').value;
            // Redirect to the page with selected filters
            window.location.href = 'pgSales.php?month=' + month + '&year=' + year;
        }
    </script>
</body>

</html>