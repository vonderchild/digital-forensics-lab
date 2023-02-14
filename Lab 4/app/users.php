<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Users</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>


<body>
    <div class="container mt-5">
        <h1 class="text-center">User Data</h1>
        <form action="" method="post" class="form-group">
            <div class="input-group mb-3">
                <input type="text" name="search" placeholder="Search for users" class="form-control mt-3">
                <div class="input-group-append">
                    <input type="submit" value="Search" class="btn btn-primary mt-3">
                </div>
            </div>
        </form>
        <table class="table table-striped">
            <thead class="thead">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Username</th>
                    <th scope="col">Email</th>
                </tr>
            </thead>
            <tbody>
                <?php

                $servername = "127.0.0.1";
                $username = "user";
                $password = "\$up3rs3cr3tp4ssw0rd";
                $dbname = "userdb";

                $conn = new mysqli($servername, $username, $password, $dbname);

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $search = isset($_POST['search']) ? $_POST['search'] : '';
                $query = "SELECT id, username, email FROM users";
                if ($search) {
                    $query .= " WHERE username LIKE '%$search%'";
                }
                $result = mysqli_query($conn, $query);
                while ($row = mysqli_fetch_array($result)) {
                    echo "<tr>
                          <td>" . $row['id'] . "</td>
                          <td>" . $row['username'] . "</td>
                          <td>" . $row['email'] . "</td>
                        </tr>";
                }

                $conn->close();

                ?>
            </tbody>
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>

</html>