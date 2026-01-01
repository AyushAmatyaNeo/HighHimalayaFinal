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
$position = isset($_GET['position']) ? urldecode($_GET['position']) : '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $position = $_POST['position'];
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $applicant_question = $_POST['applicant_question'];
    $refrence_from = $_POST['refrence_from'];
    $coverLetter = $_POST['cover-letter'];
    // $status='NA';

    // File upload handling
    $uploadDir = 'uploads/'; // Specify the directory to store uploaded files

    if (isset($_FILES['resume']) && !empty($_FILES['resume']['name'])) {
        // Check if 'resume' index is set and not empty
        $uploadFile = $uploadDir . basename($_FILES['resume']['name']);

        if (move_uploaded_file($_FILES['resume']['tmp_name'], $uploadFile)) {
            // File uploaded successfully, proceed with database insertion
            $resumePath = $uploadFile;

                        // Your SQL query for insertion
           // $sql = "INSERT INTO HRIS_VACANCY_APPLICATION (ID, POSITION, FULL_NAME, GENDER, EMAIL, CONTACT_NUMBER, COVER_LETTER, RESUME_PATH) VALUES (:nextId, :position, :name, :gender, :email, :contact, :coverLetter, :resumePath)";
            $sql = "INSERT INTO HRIS_VACANCY_APPLICATION (ID, POSITION, FULL_NAME, GENDER, EMAIL, CONTACT_NUMBER, COVER_LETTER, RESUME_PATH, STATUS, APPLICANT_QUESTION, FEFRENCE_FROM) VALUES ((SELECT MAX(ID) + 1 FROM HRIS_VACANCY_APPLICATION), :position, :name, :gender, :email, :contact, :coverLetter, :resumePath, 'NA', :applicant_question, :refrence_from)";

            // Prepare the SQL query
            $statement = oci_parse($connection, $sql);

            // Bind parameters
            
            oci_bind_by_name($statement, ':position', $position);
            oci_bind_by_name($statement, ':name', $name);
            oci_bind_by_name($statement, ':gender', $gender);
            oci_bind_by_name($statement, ':email', $email);
            oci_bind_by_name($statement, ':contact', $contact);
            oci_bind_by_name($statement, ':applicant_question', $applicant_question);
            oci_bind_by_name($statement, ':refrence_from', $refrence_from);
            oci_bind_by_name($statement, ':coverLetter', $coverLetter);
            oci_bind_by_name($statement, ':resumePath', $resumePath);

            // Execute the query
            $success = oci_execute($statement);

            if ($success) {
                echo '<script>';
                echo 'alert("Application submitted successfully!");';
                echo 'window.location.href = "vacancy.php";';
                echo '</script>';
                exit();
    
            } else {
                $e = oci_error($statement);
                echo '<script>';
                echo 'alert("Error submitting application. ' . htmlentities($e['message'], ENT_QUOTES) . '");';
                echo '</script>';
            }
        } else {
            echo "Error uploading resume file.";
        }
    } else {
        echo "Please upload a resume file.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Vacancy Application</title>
    <style>
       body {
    font-family: 'Arial', sans-serif;
    background-color: #f0f8ff; 
    margin: 0;
    padding: 20px; 
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
}

form {
    background-color: #fff; 
    padding: 25px;
    border-radius: 15px; 
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); 
    width: 60%;
    max-width: 600px; 
    text-align: left;
}

h3 {
    text-align: center;
    color: #2F9E1E; 
    font-size: 28px;
    margin-bottom: 20px;
}

label {
    display: inline-block;
    margin-bottom: 8px;
    width: 30%;
    font-weight: bold;
    color: #333333;
}

input,
textarea {
    width: 50%;
    padding: 10px;
    margin-bottom: 16px;
    box-sizing: border-box;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 16px;
}

textarea {
    resize: vertical;
}

.gender-label {
    width: 90px;
    justify-content: space-around;
    margin-right: 70px;
}

.gender-radio {
    width: 50px;
    display: inline-block;
    /* margin-left;: 150px */
}

button {
    background-color: #2F9E1E;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    display: block;
    margin: 20px auto 0;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #1c7a14; 
}

.cover-letter-label {
    width: 30%;
    display: inline-block;
    margin-bottom: 4px;
    font-weight: bold;
    color: #333333;
}

.cover-letter-textarea {
    width: 68%;
    display: inline-block;
    margin-bottom: 16px;
    vertical-align: top; 
}

.label {
    width: 100px;
}

@media (max-width: 768px) {
    form {
        padding: 15px;
        width: 90%;
    }

    h3 {
        font-size: 24px;
    }

    label {
        font-size: 14px;
    }

    input, textarea {
        width: 100%; 
    }
}

    </style>

</head>

<body>

    <form action="" method="post" enctype="multipart/form-data">
    <h3 style="color: green; text-align:center;">Fill the Below Application Form Correctly</h3>

    <label for="position">Position:</label>
    <input type="text" id="position" name="position" value="<?= htmlspecialchars($position) ?>" readonly required>


        <label for="name">Full Name:</label>
        <input type="text" id="name" name="name" required>

        <label class="gender-label">Gender:</label>
        <input type="radio" id="male" name="gender" value="M" class="gender-radio" required>
        <label for="male" class="label">Male</label>
        <input type="radio" id="female" name="gender" value="F" class="gender-radio" required>
        <label for="female" class="label">Female</label>
<br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="contact">Contact Number:</label>
        <input type="text" id="contact" name="contact" minlength="10" required>

        <label for="contact">Applicant Question:</label>
        <input type="text" id="applicant_question" name="applicant_question" required>

        <label for="contact">Refrence From:</label>
        <input type="text" id="refrence_from" name="refrence_from" required>


        <label class="cover-letter-label" for="cover-letter">Cover Letter:</label>
        <textarea id="cover-letter" name="cover-letter" class="cover-letter-textarea" rows="4" required></textarea>


        <label for="resume">Upload Resume:</label>
        <input type="file" id="resume" name="resume" accept=".pdf, .doc, .docx" required><br>

        <button type="submit">Apply</button>
    </form>
</body>
</html>
