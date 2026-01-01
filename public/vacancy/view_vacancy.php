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

// Retrieve the vacancy ID from the URL parameter
$vacancyId = isset($_GET['vacancy_id']) ? $_GET['vacancy_id'] : '';

// Your SQL query with a WHERE clause to fetch data for the specific vacancy ID
$sql = "SELECT POSITION, AVAILABILITY, DESCRIPTION FROM hris_vacancy WHERE VACANCY_ID = :vacancy_id AND STATUS='E'";

// Prepare the SQL query
$statement = oci_parse($connection, $sql);

// Bind the vacancy ID parameter
oci_bind_by_name($statement, ':vacancy_id', $vacancyId);

// Execute the query
oci_execute($statement);

// Fetch data
$row = oci_fetch_assoc($statement);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vacancy Details</title>
    <style>
       
        /* * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        } */

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f8ff; 
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 20px;
        }

        .job-details {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 25px;
            max-width: 600px;
            width: 100%;
            text-align: left;
        }

        h2 {
            color:#2F9E1E; 
            font-size: 28px;
            margin-bottom: 20px;
            text-align: center;
        }

        p {
            font-size: 16px;
            color: #000;
            line-height: 1.6;
            margin-bottom: 10px;
        }
        button {
            background-color: #2F9E1E;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            margin: 20px auto 0;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #1c7a14;
        }

      
        @media (max-width: 768px) {
            .job-details {
                padding: 15px;
                width: 90%;
            }

            h2 {
                font-size: 24px;
            }

            h4 {
                font-size: 18px;
            }

            p {
                font-size: 14px;
            }
        }
    </style>
</head>

<body>

    <div class="job-details">
        <?php
        if ($row) {
            echo "<h2>Vacancy Details</h2>";
            echo "<h4><strong>Position :</strong> " . $row['POSITION'] . "</h4>";
            echo "<p><strong>Availability :</strong> " . $row['AVAILABILITY'] . "</p>";
            echo "<p><strong>Description :</strong> " . $row['DESCRIPTION'] . "</p>";
            echo '<button onclick="window.location.href=\'apply_vacancy.php?position=' . urlencode($row['POSITION']) . '\'">Apply Now</button>';
        } else {
            echo "<p>No details found for the selected vacancy.</p>";
        }
        ?>
    </div>

</body>

</html>

