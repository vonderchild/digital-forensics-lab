<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Images</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center">View an Image</h1>
        <form action="" class="form-group">
            <div class="input-group mb-3">
                <input type="text" id="file" name="file" class="form-control mt-3" placeholder="Enter file name">
                <div class="input-group-append">
                    <input type="submit" value="Submit" class="btn btn-primary mt-3">
                </div>
            </div>
        </form>
        <?php
        if (isset($_GET['file'])) {
            $filename = $_GET['file'];
            $url = "http://$_SERVER[HTTP_HOST]/view.php?image=" . $filename;
            header('Location: ' . $url);
        }
        ?>
        <div class="row">
            <div class="col-4">
                <img src="images/starry_night.jpg" alt="image1" class="img-fluid rounded">
                <p class="text-center mt-3">starry_night.jpg</p>
            </div>
            <div class="col-4">
                <img src="images/almond_blossom.jpg" alt="image2" class="img-fluid rounded">
                <p class="text-center mt-3">almond_blossom.jpg</p>
            </div>
            <div class="col-4">
                <img src="images/red_vineyards.jpg" alt="image3" class="img-fluid rounded">
                <p class="text-center mt-3">red_vineyards.jpg</p>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>

</html>