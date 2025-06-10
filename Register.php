<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Registration</title>
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f0f2f5;
      margin: 0;
      padding: 0;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    header, footer {
      background-color: #004080;
      color: white;
      padding: 15px 20px;
      text-align: center;
    }

    main {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 40px 20px;
    }

    .container {
      background-color: #fff;
      padding: 30px 25px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
    }

    .container h1 {
      font-size: 24px;
      font-weight: 600;
      margin-bottom: 25px;
      color: #333;
      text-align: center;
    }

    form label {
      display: block;
      margin-bottom: 6px;
      font-weight: 600;
      color: #555;
      font-size: 14px;
    }

    form input, form select {
      width: 100%;
      padding: 10px 12px;
      margin-bottom: 16px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
      transition: border-color 0.3s;
    }

    form input:focus, form select:focus {
      border-color: #004080;
      outline: none;
    }

    button {
      width: 100%;
      padding: 12px;
      background-color: #004080;
      color: white;
      font-size: 15px;
      font-weight: bold;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    button:hover {
      background-color: #003366;
    }

    p {
      text-align: center;
      font-size: 13px;
      margin-top: 16px;
    }

    a {
      color: #004080;
      font-weight: bold;
      text-decoration: none;
    }

    a:hover {
      text-decoration: underline;
    }

    @media (max-width: 480px) {
      .container {
        padding: 20px;
      }

      .container h1 {
        font-size: 20px;
      }
    }
  </style>
</head>
<body>
  <?php include('includes/header.php'); ?>

  <main>
    <div class="container">
      <h1>Register</h1>
      <form action="Register.php" method="POST">
  <label for="firstname">First Name</label>
  <input type="text" id="firstname" name="Firstname" required>

  <label for="surname">Surname</label>
  <input type="text" id="surname" name="Surname" required>

  <label for="gender">Gender</label>
  <select id="gender" name="Gender" required>
    <option value="">Select Gender</option>
    <option value="M">Male</option>
    <option value="F">Female</option>
  </select>

  <label for="dob">Date of Birth</label>
  <input type="date" id="dob" name="DOB" required>

  <label for="employee">Employee Number (If applicable)</label>
  <input type="text" id="employee" name="EmployeeNumber">

  <label for="company">Company (If applicable)</label>
  <input type="text" id="company" name="Company">

  <label for="cell">Cell Number</label>
  <input type="tel" id="cell" name="CellNumber" required>

  <label for="email">Email</label>
  <input type="email" id="email" name="Email" required>

  <label for="password">Password</label>
  <input type="password" id="password" name="Passwords" required>

  

  <button type="submit">Click to Create Account</button>
</form>


      <p>Already have an account? <a href="Login.php">Sign in</a></p>
    </div>
  </main>


  <?php include('includes/footer.php'); ?>
</body>
</html>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'db.php'; // Include your database connection

    // Collect and sanitize input
    $firstname = $_POST['Firstname'];
    $surname = $_POST['Surname'];
    $gender = $_POST['Gender'];
    $dob = $_POST['DOB'];
    $employee = $_POST['EmployeeNumber'] ?? null;
    $company = $_POST['Company'] ?? null;
    $cell = $_POST['CellNumber'];
    $email = $_POST['Email'];
    $password = $_POST['Passwords'];
    

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $conn->prepare("INSERT INTO Users 
            (Firstname, Surname, Gender, DOB, EmployeeNumber, Company, CellNumber, Email, Passwords)
            VALUES (:firstname, :surname, :gender, :dob, :employee, :company, :cell, :email, :password)");

        $stmt->bindParam(':firstname', $firstname);
        $stmt->bindParam(':surname', $surname);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':dob', $dob);
        $stmt->bindParam(':employee', $employee);
        $stmt->bindParam(':company', $company);
        $stmt->bindParam(':cell', $cell);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        

        $stmt->execute();

        echo "<script>alert('Account created successfully!'); window.location.href='Login.php';</script>";
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

