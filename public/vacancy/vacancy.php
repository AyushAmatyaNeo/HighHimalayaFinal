<?php
// Your Oracle database connection parameters
$hostname = '10.255.0.106';
$database = 'HRIS';
$username = 'MASTER_DATABASE';
$password = 'MASTER_DATABASE';

// Create a connection to Oracle
$connection = oci_connect($username, $password, "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=$hostname)(PORT=1521))(CONNECT_DATA=(SID=$database)))");

if (!$connection) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}

// Your SQL query
$sql = "SELECT * FROM hris_vacancy where STATUS='E' order by created_dt desc";

// Prepare the SQL query
$statement = oci_parse($connection, $sql);

// Execute the query
oci_execute($statement);

// Fetch data and populate the table dynamically
$rows = oci_fetch_all($statement, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vacancy Page</title>
    <style>
        body {
    font-family: 'Arial', sans-serif;
    background-image: url(https://t3.ftcdn.net/jpg/04/57/96/08/360_F_457960814_EYEAR7bC06xnuvC1Th5pHuNSr8hOeSV3.jpg); 
    background-size: cover;
    background-repeat: no-repeat;
    background-attachment: fixed;
    background-color: rgba(240, 248, 255, 0.9);
    margin: 0;
    padding: 0;
}

h1, p {
    color: #FFD700; 
    text-align: center;
    margin: 10px 0;
}

.card {
    margin: 20px auto;
    border: 1px solid #ddd;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    background-color: #fff;
    max-width: 800px;
    overflow: hidden;
}

.card-header {
    background-color: #2F9E1E;
    color: white;
    padding: 20px;
    font-size: 20px;
    font-weight: bold;
    text-align: center;
    border-bottom: 1px solid #ddd;
}

.card-body {
    padding: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    border-radius: 8px;
    overflow: hidden;
    background-color: #f0f8ff; 
}

th, td {
    border: 1px solid #ddd;
    padding: 12px;
    text-align: center;
    font-size: 14px;
    color: #000;
}

th {
    background-color: #2F9E1E;
    color: white;
}

td {
    background-color: #fff;
    color: #000;
}

button {
    background-color: #2F9E1E;
    color: white;
    border: none;
    padding: 8px 16px;
    font-size: 14px;
    border-radius: 5px;
    cursor: pointer;
    margin: 5px;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #1c7a14;
}

@media (max-width: 768px) {
    .card {
        margin: 10px;
        width: 90%;
    }

    h1 {
        font-size: 22px;
    }

    h1, p {
        margin: 5px 0;
    }

    .card-header {
        font-size: 18px;
    }

    .card-body {
        font-size: 14px;
        padding: 15px;
    }

    table th, table td {
        font-size: 12px;
        padding: 6px;
    }

    button {
        font-size: 12px;
        padding: 6px 12px;
    }
}
    </style>

</head>

<body>
    <h1 style="color: white; text-align:center;">Lets be a family lets work together </h1>
    <p style="color: white; text-align:center;">( Click below button to Apply)</p>
    <div class="col-md-12">
        <!-- <div class="card">
            <div class="card-header">
                <div class="row">
                    
                </div>
            </div> -->
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <colgroup>
                        <!-- Adjust column widths as needed -->
                        <col width="10%">
                        <col width="30%">
                        <col width="20%">
                        <col width="10%">
                        <col width="20%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="text-center">Vacancy ID</th>
                            <th class="text-center">Position</th>
                            <th class="text-center">Availability</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
foreach ($result as $row) {
    echo "<tr>";
    echo "<td class='text-center'>" . $row['VACANCY_ID'] . "</td>";
    echo "<td class='text-center'>" . $row['POSITION'] . "</td>";
    echo "<td class='text-center'>" . $row['AVAILABILITY'] . "</td>";
    echo "<td class='text-center'>" . ($row['VACANCY_STATUS'] == 1 ? 'Open' : 'Closed') . "</td>";
    echo "<td class='text-center'>";

    // Check if the vacancy is open before displaying the "Apply" button
    if ($row['VACANCY_STATUS'] == 1) {
        echo "<button onclick=\"window.location.href='view_vacancy.php?vacancy_id=" . $row['VACANCY_ID'] . "'\" class='btn btn-secondary'>View</button>";
        echo "<button onclick=\"window.location.href='apply_vacancy.php?vacancy_id=" . $row['VACANCY_ID'] . "&position=" . urlencode($row['POSITION']) . "'\" class='btn btn-secondary'>Apply</button>";
    } else {
        echo "<button onclick=\"window.location.href='view_vacancy.php?vacancy_id=" . $row['VACANCY_ID'] . "'\" class='btn btn-secondary'>View</button>";
        // If the vacancy is closed, don't display the "Apply" button
    }

    echo "</td>";
    echo "</tr>";
}
?>
</tbody>

                </table>
            </div>
        </div>
    </div>

    <!-- Add your JavaScript code here -->
    <script>
        // Your JavaScript code goes here
    </script>
</body>

</html>
