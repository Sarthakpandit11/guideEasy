<?php
// Create a 200x200 default profile image
$profile = imagecreatetruecolor(200, 200);
$bg_color = imagecolorallocate($profile, 230, 230, 230);
$text_color = imagecolorallocate($profile, 150, 150, 150);
imagefill($profile, 0, 0, $bg_color);
imagestring($profile, 5, 50, 90, "Profile", $text_color);
imagepng($profile, 'images/default_profile.png');
imagedestroy($profile);

// Create a 1200x400 default cover image
$cover = imagecreatetruecolor(1200, 400);
$bg_color = imagecolorallocate($cover, 200, 200, 200);
$text_color = imagecolorallocate($cover, 100, 100, 100);
imagefill($cover, 0, 0, $bg_color);
imagestring($cover, 5, 500, 190, "Cover Image", $text_color);
imagepng($cover, 'images/default_cover.png');
imagedestroy($cover);

echo "Default images created successfully";
?> 