<?php
include "conn.php";
session_start();

if (isset($_POST['studentSignup'])) {
    $studentNo = $_POST['StudentNo'];
    $email = $_POST['Email'];
    $lastName = $_POST['LName'];
    $firstName = $_POST['FName'];
    $middleName = $_POST['MName'];
    $extensionName = $_POST['EName'];

    // Check if the email already exists
    $checkQuery = "SELECT COUNT(*) FROM users WHERE student_no = ? OR email = ? OR (first_name = ? AND last_name = ? AND middle_name = ?) AND user_role = 1";
    $checkStmt = $connection->prepare($checkQuery);
    $checkStmt->bind_param("sssss", $studentNo, $email, $firstName, $lastName, $middleName);
    $checkStmt->execute();
    $checkStmt->bind_result($count);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($count > 0) {
        header("Location: /login/student.php");
        $_SESSION['account_exists'] = true;
        exit();
    }
    else {
        // Retrieve the form values
        $extensionName = $_POST['EName'];
        $contactNumber = $_POST['ContactNumber'];
        $birthdate = $_POST['Birthday'];
        $gender = $_POST['Gender'];
        $address = $_POST['Address'];
        $province = $_POST['Province'];
        $city = $_POST['City'];
        $barangay = $_POST['Barangay'];
        $zipCode = $_POST['ZipCode'];
        $password = $_POST['Password'];
        $confirmPassword = $_POST['ConfirmPassword'];
        $course = $_POST['Course'];
        $userRole = 1;

        // Check if the passwords match
        if ($password == $confirmPassword) {
            // Validate the new password
            if (preg_match('/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*()_+{}\[\]:;<>,.?~]).{8,}$/', $password)) {
                // Hash the new password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $query = "INSERT INTO users (student_no, last_name, first_name, middle_name, extension_name, contact_no, email, birth_date, password, user_role)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $userDetailsQuery = "INSERT INTO user_details (sex, home_address, province, city, barangay, zip_code, course_id, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $crossEnrollmentQuery = "INSERT INTO acad_cross_enrollment (user_id) VALUES (?)";
                $manualEnrollmentQuery = "INSERT INTO acad_manual_enrollment (user_id) VALUES (?)";
                $gradeAccreditationQuery = "INSERT INTO acad_grade_accreditation (user_id) VALUES (?)";
                $shiftingQuery = "INSERT INTO acad_shifting (user_id) VALUES (?)";
                $subjectOverloadQuery = "INSERT INTO acad_subject_overload (user_id) VALUES (?)";
            
                $stmt = $connection->prepare($query);
                $stmt->bind_param("sssssssssi", $studentNo, $lastName, $firstName, $middleName, $extensionName, $contactNumber, $email, $birthdate, $hashedPassword, $userRole);

                // Check if all of the names match the following regex expression. If not, the query will not proceed to execute
                if ($stmt->execute() && (preg_match("/^(?:[a-zA-ZÑñ]+\s?[\-\.']?\s?)*$/", $lastName) && preg_match("/^(?:[a-zA-ZÑñ]+\s?[\-\.']?\s?)*$/", $firstName) && preg_match("/^(?:[a-zA-ZÑñ]+\s?[\-\.']?\s?)*$/", $middleName) && preg_match("/^(?:[a-zA-ZÑñ]+\s?[\-\.']?\s?)*$/", $extensionName))) {
                    $stmt->close();
                    $lastId = $connection->insert_id;
                    $stmt = $connection->prepare($userDetailsQuery);
                    $stmt->bind_param("isssssii", $gender, $address, $province, $city, $barangay, $zipCode, $course, $lastId);
                    $stmt->execute();
                    $stmt->close();

                    // Insert initial value queries on all academic services
                    $stmt = $connection->prepare($crossEnrollmentQuery);
                    $stmt->bind_param("i", $lastId);
                    $stmt->execute();
                    $stmt->close();
                    $stmt = $connection->prepare($manualEnrollmentQuery);
                    $stmt->bind_param("i", $lastId);
                    $stmt->execute();
                    $stmt->close();
                    $stmt = $connection->prepare($gradeAccreditationQuery);
                    $stmt->bind_param("i", $lastId);
                    $stmt->execute();
                    $stmt->close();
                    $stmt = $connection->prepare($shiftingQuery);
                    $stmt->bind_param("i", $lastId);
                    $stmt->execute();
                    $stmt->close();
                    $stmt = $connection->prepare($subjectOverloadQuery);
                    $stmt->bind_param("i", $lastId);
                    $stmt->execute();
                    $stmt->close();

                    header("Location: /login/student.php");
                    $_SESSION['account_created'] = true;
                } 
                else {
                    header("Location: /login/student.php");
                    $_SESSION['account_failed'] = true;
                }
            } else {
                header("Location: /login/student.php");
                $_SESSION['invalid_password'] = true;
            }
        } else {
            header("Location: /login/student.php");
            $_SESSION['pass_does_not_match'] = true;
        }
    }
    $connection->close();
    exit;
}
else if (isset($_POST['clientSignup'])) {
    $email = $_POST['Email'];
    $lastName = $_POST['LName'];
    $firstName = $_POST['FName'];
    $middleName = $_POST['MName'];
    $extensionName = $_POST['EName'];

    $checkQuery = "SELECT COUNT(*) FROM users WHERE email = ? OR (first_name = ? AND last_name = ? AND middle_name = ?) AND user_role = 2";
    $checkStmt = $connection->prepare($checkQuery);
    $checkStmt->bind_param("ssss", $email, $firstName, $lastName, $middleName);
    $checkStmt->execute();
    $checkStmt->bind_result($count);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($count > 0) {
        header("Location: /login/client.php");
        $_SESSION['account_exists'] = true;
        exit();
    }
    else {
        $extensionName = $_POST['EName'];
        $contactNumber = $_POST['ContactNumber'];
        $birthdate = $_POST['Birthday'];
        $gender = $_POST['Gender'];
        $address = $_POST['Address'];
        $province = $_POST['Province'];
        $city = $_POST['City'];
        $barangay = $_POST['Barangay'];
        $zipCode = $_POST['ZipCode'];
        $password = $_POST['Password'];
        $confirmPassword = $_POST['ConfirmPassword'];
        $userRole = 2;

        // Check if the passwords match
        if ($password == $confirmPassword) {
            // Validate the new password
            if (preg_match('/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*()_+{}\[\]:;<>,.?~]).{8,}$/', $password)) {
                // Hash the new password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $query = "INSERT INTO users (last_name, first_name, middle_name, extension_name, contact_no, email, birth_date, password, user_role)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $userDetailsQuery = "INSERT INTO user_details (sex, home_address, province, city, barangay, zip_code, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
            
                $stmt = $connection->prepare($query);
                $stmt->bind_param("ssssssssi", $lastName, $firstName, $middleName, $extensionName, $contactNumber, $email, $birthdate, $hashedPassword, $userRole);

                // Check if all of the names match the following regex expression. If not, the query will not proceed to execute
                if ($stmt->execute() && (preg_match("/^(?:[a-zA-ZÑñ]+\s?[\-\.']?\s?)*$/", $lastName) && preg_match("/^(?:[a-zA-ZÑñ]+\s?[\-\.']?\s?)*$/", $firstName) && preg_match("/^(?:[a-zA-ZÑñ]+\s?[\-\.']?\s?)*$/", $middleName) && preg_match("/^(?:[a-zA-ZÑñ]+\s?[\-\.']?\s?)*$/", $extensionName))) {
                    $stmt->close();
                    $lastId = $connection->insert_id;
                    $stmt = $connection->prepare($userDetailsQuery);
                    $stmt->bind_param("isssssi", $gender, $address, $province, $city, $barangay, $zipCode, $lastId);
                    $stmt->execute();
                    $stmt->close();
                    header("Location: /login/client.php");
                    $_SESSION['account_created'] = true;
                } 
                else {
                    header("Location: /login/client.php");
                    $_SESSION['account_failed'] = true;
                }
            } else {
                header("Location: /login/client.php");
                $_SESSION['invalid_password'] = true;
            }
        } else {
            header("Location: /login/client.php");
            $_SESSION['pass_does_not_match'] = true;
        }
    }
}
$connection->close();
exit;
?>