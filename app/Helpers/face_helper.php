<?php

function cropWajah(string $path): void
{
    if (!is_file($path)) {
        return;
    }

    $info = @getimagesize($path);
    if ($info === false) {
        return;
    }

    $mime = $info['mime'];

    switch ($mime) {
        case 'image/jpeg':
            $img = @imagecreatefromjpeg($path);
            break;
        case 'image/png':
            $img = @imagecreatefrompng($path);
            break;
        default:
            // webp / heic / dll → SKIP (tidak crash)
            return;
    }

    if (!$img) {
        return;
    }

    // Perbaiki orientasi kamera (EXIF) supaya tidak miring.
    if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
        $exif = @exif_read_data($path);
        $orientation = (int) ($exif['Orientation'] ?? 1);

        if ($orientation === 3) {
            $rotated = imagerotate($img, 180, 0);
            if ($rotated !== false) {
                imagedestroy($img);
                $img = $rotated;
            }
        } elseif ($orientation === 6) {
            $rotated = imagerotate($img, -90, 0);
            if ($rotated !== false) {
                imagedestroy($img);
                $img = $rotated;
            }
        } elseif ($orientation === 8) {
            $rotated = imagerotate($img, 90, 0);
            if ($rotated !== false) {
                imagedestroy($img);
                $img = $rotated;
            }
        }
    }

    $width  = imagesx($img);
    $height = imagesy($img);

    if ($width < 10 || $height < 10) {
        imagedestroy($img);
        return;
    }

    $size = min($width, $height);
    $x = (int)(($width - $size) / 2);
    $y = (int)(($height - $size) / 2);

    $crop = imagecrop($img, [
        'x' => $x,
        'y' => $y,
        'width' => $size,
        'height' => $size
    ]);

    if ($crop) {
        if ($mime === 'image/png') {
            imagealphablending($crop, false);
            imagesavealpha($crop, true);
            imagepng($crop, $path, 6);
        } else {
            imagejpeg($crop, $path, 90);
        }
        imagedestroy($crop);
    }

    imagedestroy($img);
}
