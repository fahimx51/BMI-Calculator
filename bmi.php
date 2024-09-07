<?php 
    include 'bmi_header.php'; 
    session_start(); // Ensure that sessions are started to access the logged-in user

    $errh = $errw = "";
    $height = $weight = "";
    $bmipass = "";
    $bmi_status = "";

    // Get the logged-in user's name from the session
    if (isset($_SESSION['username'])) {
        $user_name = $_SESSION['username']; // Using 'username' as set in the login code
    } else {
        // Redirect or show error if the user is not logged in
        echo "<span style='color:red; font-size:17px;'>Error: You must be logged in to calculate your BMI.</span>";
        exit;
    }

    // Database connection setup
    $conn = new mysqli('localhost', 'root', '', 'bmi');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        // Validation for height
        if (empty($_POST['height'])) {
            $errh = "<span style='color:#ed4337; font-size:17px; display:block'>Height is required</span>";
        } else {
            $height = validate($_POST['height']);
        }
    
        // Validation for weight
        if (empty($_POST['weight'])) {
            $errw = "<span style='color:#ed4337; font-size:17px; display:block'>Weight is required</span>";
        } else {
            $weight = validate($_POST['weight']);
        }

        // Calculate BMI if height and weight are provided
        if (!empty($height) && !empty($weight)) {
            $bmi = ($weight / ($height * $height));
            $bmipass = $bmi;

            // Determine BMI status
            if ($bmi >= 13.6 && $bmi <= 18.5) {
                $bmi_status = "Low body weight";
            } elseif ($bmi > 18.5 && $bmi < 24.9) {
                $bmi_status = "Good health";
            } elseif ($bmi > 25 && $bmi < 29.9) {
                $bmi_status = "Excess body weight";
            } elseif ($bmi > 30 && $bmi < 34.9) {
                $bmi_status = "First stage of obesity";
            } elseif ($bmi > 35 && $bmi < 39.9) {
                $bmi_status = "Second stage of obesity";
            } elseif ($bmi >= 40) {
                $bmi_status = "Excess fat (Critical)";
            }

            // SQL query without prepared statements
            $sql = "INSERT INTO Users (user_name, bmi, bmi_status) VALUES ('$user_name', '$bmipass', '$bmi_status')";

            if ($conn->query($sql) === TRUE) {
                echo "<span style='color:green; font-size:17px;'>BMI data stored successfully!</span>";
            } else {
                echo "<span style='color:red; font-size:17px;'>Error: " . $conn->error . "</span>";
            }
        }
    }

    $conn->close();

    function validate($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
?>

<h2>Check Your BMI</h2>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
    <div class="section1">
        <span>Enter Your Height : </span>
        <input type="text" name="height" autocomplete="off" placeholder="Meters"><?php echo $errh; ?>
    </div>
    
    <div class="section2">
        <span>Enter Your Weight : </span>
        <input type="text" name="weight" autocomplete="off" placeholder="Kilograms"><?php echo $errw; ?>
    </div>

    <div class="submit">
        <input type="submit" name="submit" value="Check BMI">
        <input type="reset" value="Clear">
    </div>
</form>

<?php
    error_reporting(0);
    echo "<span class='pass'>Your BMI is : ". number_format($bmipass, 2) ."</span>";
?>

<?php include 'bmi_footer.php'; ?>
