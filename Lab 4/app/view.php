<?php

$image = $_GET['image'];
$image_ext = pathinfo($image, PATHINFO_EXTENSION);

if ($image_ext == "jpg") {
    header("Content-Type: image/jpeg");
}

$image = file_get_contents('images/' . $image);
echo $image;

?>
