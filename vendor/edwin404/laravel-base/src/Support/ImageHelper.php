<?php

namespace Edwin404\Base\Support;


use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

class ImageHelper
{
    public static function limitSizeAndDetectOrientation($path, $maxWidth = 1000, $maxHeight = 1000)
    {
        try {

            // gif不做处理
            if (preg_match('/*\.gif$/i', $path)) {
                return;
            }

            $changed = false;

            $exif = @exif_read_data($path);
            $image = Image::make($path);
            if (!empty($exif['Orientation'])) {
                switch (intval($exif['Orientation'])) {
                    case 2:
                        $image->flip();
                        $changed = true;
                        break;
                    case 3:
                        $image->rotate(180);
                        $changed = true;
                        break;
                    case 4:
                        $image->rotate(180);
                        $image->flip();
                        $changed = true;
                        break;
                    case 5:
                        $image->rotate(90);
                        $image->flip();
                        $changed = true;
                        break;
                    case 6:
                        $image->rotate(-90);
                        $changed = true;
                        break;
                    case 7:
                        $image->rotate(90);
                        $image->flip();
                        $changed = true;
                        break;
                    case 8:
                        $image->rotate(90);
                        $changed = true;
                        break;
                }
            }

            $width = $image->width();
            $height = $image->height();
            if ($width > $maxWidth || $height > $maxHeight) {
                $changed = true;
                if ($width > $maxWidth) {
                    $image->resize($maxWidth, intval($maxWidth * $height / $width));
                }
                if ($height > $maxHeight) {
                    $image->resize(intval($maxHeight * $width / $height), $maxHeight);
                }
            }

            if (config('data.upload.image.compress') || $changed) {
                $image->save($path);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }

    public static function corners($sourceImageFile, $radius, $pngSaveFile)
    {
        try {# test source image
            if (file_exists($sourceImageFile)) {
                $res = is_array($info = getimagesize($sourceImageFile));
            } else {
                $res = false;
            }

            # open image
            if ($res) {
                $w = $info[0];
                $h = $info[1];
                switch ($info['mime']) {
                    case 'image/jpeg':
                        $src = imagecreatefromjpeg($sourceImageFile);
                        break;
                    case 'image/gif':
                        $src = imagecreatefromgif($sourceImageFile);
                        break;
                    case 'image/png':
                        $src = imagecreatefrompng($sourceImageFile);
                        break;
                    default:
                        $res = false;
                }
            }

            # create corners
            if ($res) {

                $q = 10; # change this if you want
                $radius *= $q;

                # find unique color
                do {
                    $r = rand(0, 255);
                    $g = rand(0, 255);
                    $b = rand(0, 255);
                } while (imagecolorexact($src, $r, $g, $b) < 0);

                $nw = $w * $q;
                $nh = $h * $q;

                $img = imagecreatetruecolor($nw, $nh);
                $alphacolor = imagecolorallocatealpha($img, $r, $g, $b, 127);
                imagealphablending($img, false);
                imagesavealpha($img, true);
                imagefilledrectangle($img, 0, 0, $nw, $nh, $alphacolor);

                imagefill($img, 0, 0, $alphacolor);
                imagecopyresampled($img, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);

                imagearc($img, $radius - 1, $radius - 1, $radius * 2, $radius * 2, 180, 270, $alphacolor);
                imagefilltoborder($img, 0, 0, $alphacolor, $alphacolor);
                imagearc($img, $nw - $radius, $radius - 1, $radius * 2, $radius * 2, 270, 0, $alphacolor);
                imagefilltoborder($img, $nw - 1, 0, $alphacolor, $alphacolor);
                imagearc($img, $radius - 1, $nh - $radius, $radius * 2, $radius * 2, 90, 180, $alphacolor);
                imagefilltoborder($img, 0, $nh - 1, $alphacolor, $alphacolor);
                imagearc($img, $nw - $radius, $nh - $radius, $radius * 2, $radius * 2, 0, 90, $alphacolor);
                imagefilltoborder($img, $nw - 1, $nh - 1, $alphacolor, $alphacolor);
                imagealphablending($img, true);
                imagecolortransparent($img, $alphacolor);

                # resize image down
                $dest = imagecreatetruecolor($w, $h);
                imagealphablending($dest, false);
                imagesavealpha($dest, true);
                imagefilledrectangle($dest, 0, 0, $w, $h, $alphacolor);
                imagecopyresampled($dest, $img, 0, 0, 0, 0, $w, $h, $nw, $nh);

                # output image
                $res = $dest;
                imagedestroy($src);
                imagedestroy($img);

                $dir = FileHelper::dirPath($pngSaveFile);
                if (!file_exists($dir)) {
                    @mkdir($dir, 0755, true);
                }

                imagepng($res, $pngSaveFile);
            }

        } catch (\Exception $e) {

            // do nothing

        }

    }

    public static function info($imagePath)
    {
        $info = [];
        try {
            $image = @Image::make($imagePath);
            $info['width'] = $image->width();
            $info['height'] = $image->height();
        } catch (\Exception $e) {
            $info['width'] = 0;
            $info['height'] = 0;
        }
        return $info;
    }

    public static function watermark($image, $type, $textOrImage)
    {
        $changed = false;
        $img = Image::make($image);
        $width = $img->width();
        $height = $img->height();
        if ($width < 100 || $height < 100) {
            return;
        }
        $gap = intval(min($width, $height) / 50);
        switch ($type) {
            case 'text':
                if (empty($textOrImage)) {
                    return;
                }
                $img->text($textOrImage, $width - $gap, $height - $gap,
                    function ($font) use ($width, $height) {
                        $fontSize = max(min($width, $height) / 30, 10);
                        $font->file(__DIR__ . '/../../../laravel-common/resources/font/MicroYahei.ttf');
                        $font->size($fontSize);
                        $font->color('rgba(255, 255, 255, 0.5)');
                        $font->align('right');
                        $font->valign('bottom');
                    });
                $changed = true;
                break;
            case 'image':

                $localWater = FileHelper::savePathToLocal($textOrImage);
                if (empty($localWater) || !file_exists($localWater)) {
                    return;
                }
                $watermark = Image::make($localWater);
                $limit = max(min($width, $height) / 10, 10);
                $waterWidth = $watermark->width();
                $waterHeight = $watermark->height();
                if ($waterWidth > $waterHeight) {
                    $waterHeight = intval($limit * $waterHeight / $waterWidth);
                    $waterWidth = $limit;
                } else {
                    $waterWidth = intval($limit * $waterWidth / $waterHeight);
                    $waterHeight = $limit;
                }
                $watermark->resize($waterWidth, $waterHeight);
                $watermark->opacity(50);
                $img->insert($watermark, 'bottom-right', $gap, $gap);
                $changed = true;
                break;
        }
        if ($changed) {
            $img->save($image);
        }
    }

}