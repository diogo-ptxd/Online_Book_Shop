<?php
// Database connection parameters
$host = 'localhost';
$db = 'bookwise';
$user = 'user_write';
$pass = 'password';

// Connect to the database
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = 1; // Example user ID, change as needed
$result = $conn->query("SELECT * FROM users WHERE user_id = $id");
$user_data = $result->fetch_assoc();

if (!$user_data) {
    die("User not found");
}

$username = $user_data['fullname'];
$email = $user_data['email'];
$phoneNumber = $user_data['phone'];
$mobilePhoneNumber = $user_data['mobile'];
$location = $user_data['address'];
$profile_picture = $user_data['profile_picture'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/editprofile.css">
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/css/bootstrap.min.css'>
    <link rel="icon" type="image/x-icon" href="../assets/images/bookwise_logo.png">
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/js/bootstrap.bundle.min.js'></script>
    <title>BookWise - Edit Data</title>
</head>
<body>
    <div class="container">
        <div class="main-body">
            <div class="row">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-column align-items-center text-center">
                                <img src="https://bootdey.com/img/Content/avatar/avatar6.png" alt="Admin" class="rounded-circle p-1 bg-primary" width="110">
                                <div class="mt-3">
                                    <h4><?php echo $username; ?></h4>
                                    <p class="text-secondary mb-1"><?php echo ($username); ?></p>
                                    <p class="text-muted font-size-sm"><?php echo ($location); ?></p>
                                    <button class="btn btn-primary">Follow</button>
                                    <button class="btn btn-outline-primary">Message</button>
                                </div>
                            </div>
                            <hr class="my-4">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <h6 class="mb-0">Website</h6>
                                    <span class="text-secondary">placeholder</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <h6 class="mb-0">Github</h6>
                                    <span class="text-secondary">placeholder</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <h6 class="mb-0">Twitter</h6>
                                    <span class="text-secondary">@placeholder</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <h6 class="mb-0">Instagram</h6>
                                    <span class="text-secondary">placeholder</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <h6 class="mb-0">Facebook</h6>
                                    <span class="text-secondary">placeholder</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <form action="./updateprofiledata.php" method="POST" enctype="multipart/form-data">
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Full Name</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" class="form-control" placeholder="<?php echo $username; ?>" name="fullname" value="<?php echo $username; ?>">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Email</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="email" placeholder="<?php echo $email; ?>" class="form-control" name="email" value="<?php echo $email; ?>">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Phone</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" placeholder="<?php echo $phoneNumber; ?>" class="form-control" name="phone" value="<?php echo $phoneNumber; ?>">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Mobile</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" placeholder="<?php echo $mobilePhoneNumber; ?>" class="form-control" name="mobile" value="<?php echo $mobilePhoneNumber; ?>">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Address</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" placeholder="<?php echo $location; ?>" class="form-control" name="address" value="<?php echo $location; ?>">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Profile Picture</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="file" class="btn btn-outline-secondary inputfile" id="inputfile" name="profile_picture" hidden>
                                        <label for="inputfile" class="btn btn-outline-secondary labelforinputfile">Choose File</label>
                                        <span id="file-chosen">No File Chosen</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="submit" class="btn btn-primary px-4" value="Save Changes">
                                        <input type="button" class="btn btn-danger px-4" value="Cancel" onclick="window.location.href='./home.php'">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <script>
                        const actualBtn = document.getElementById('inputfile');
                        const fileChosen = document.getElementById('file-chosen');

                        actualBtn.addEventListener('change', function() {
                            fileChosen.textContent = this.files[0].name
                        })
                    </script>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
