<?php

namespace Edwin404\Base\Support;


use Illuminate\Support\Str;

class FileHelper
{
    public static function extension($pathname)
    {
        return strtolower(pathinfo($pathname, PATHINFO_EXTENSION));
    }

    public static function name($pathname)
    {
        return strtolower(pathinfo($pathname, PATHINFO_BASENAME));
    }

    public static function formatByte($bytes, $decimals = 2)
    {
        $size = sprintf("%u", $bytes);
        if ($size == 0) {
            return ("0 Bytes");
        }
        $units = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
        return round($size / pow(1024, ($i = floor(log($size, 1024)))), $decimals) . $units[$i];
    }

    public static function formattedSizeToBytes($size_str)
    {
        $size_str = strtolower($size_str);
        $unit = preg_replace('/[^a-z]/', '', $size_str);
        $value = floatval(preg_replace('/[^0-9.]/', '', $size_str));

        $units = array('b' => 0, 'kb' => 1, 'mb' => 2, 'gb' => 3, 'tb' => 4, 'k' => 1, 'm' => 2, 'g' => 3, 't' => 4);
        $exponent = isset($units[$unit]) ? $units[$unit] : 0;

        return ($value * pow(1024, $exponent));
    }

    public static function dirPath($filePath)
    {
        if (strpos($filePath, '/') === false) {
            return '';
        }
        return substr($filePath, 0, strrpos($filePath, '/') + 1);
    }

    public static function checkSaveFileDir($filePath)
    {
        $dir = self::dirPath($filePath);
        if (!file_exists($dir)) {
            @mkdir($dir, 0755, true);
        }
    }

    public static function savePathToLocal($path, $ext = '')
    {
        $tempPath = public_path('temp/' . md5($path) . $ext);
        if (file_exists($tempPath)) {
            return $tempPath;
        }
        if (Str::startsWith($path, 'http://') || Str::startsWith($path, 'https://') || Str::startsWith($path, '//')) {
            if (Str::startsWith($path, '//')) {
                $path = 'http://' . $path;
            }
            $image = CurlHelper::getContent($path);
            if (empty($image)) {
                return null;
            }
            @mkdir(public_path('temp'));
            file_put_contents($tempPath, $image);
        } else {
            if (Str::startsWith($path, '/')) {
                $path = substr($path, 1);
            }
            $tempPath = public_path($path);
        }
        if (!file_exists($tempPath)) {
            return null;
        }
        return $tempPath;
    }

    public static function number2dir($id, $depth = 3)
    {
        $width = $depth * 3;
        $idFormated = sprintf('%0' . $width . 'd', $id);
        $dirs = [];
        for ($i = 0; $i < $depth; $i++) {
            $dirs[] = substr($idFormated, $i * 3, 3);
        }
        return join('/', $dirs);
    }

    /**
     * 复制文件夹
     * @param $src : 必须给出，不能为空
     * @param $dst : 必须给出，不能为空
     * @param $replaceExt : 如果文件存在需要添加的后缀名
     */
    public static function copy($src, $dst, $replaceExt = null)
    {
        $src = rtrim($src, '/') . '/';
        $dst = rtrim($dst, '/') . '/';
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . $file)) {
                    self::copy($src . $file . '/', $dst . $file . '/', $replaceExt);
                } else {
                    if (null !== $replaceExt && file_exists($dst . $file)) {
                        @rename($dst . $file, $dst . $file . $replaceExt);
                    }
                    @copy($src . $file, $dst . $file);
                }
            }
        }
        @closedir($dir);
    }


    /**
     * 删除文件夹
     *
     * @param $dir : string
     * @pararm $removeSelf : bool
     *
     * @return null
     */
    public static function rm($dir, $removeSelf = true)
    {
        if (is_dir($dir)) {
            $dh = opendir($dir);
            while (($file = readdir($dh)) !== false) {
                if ($file != "." && $file != "..") {
                    $fullPath = rtrim($dir, '/') . '/' . $file;
                    if (is_dir($fullPath)) {
                        self::rm($fullPath, true);
                    } else {
                        @unlink($fullPath);
                    }
                }
            }
            closedir($dh);
            if ($removeSelf) {
                @rmdir($dir);
            }
        } else {
            @unlink($dir);
        }
        return true;
    }

    public static function listFiles($filename, $pattern = '*')
    {
        if (strpos($pattern, '|') !== false) {
            $patterns = explode('|', $pattern);
        } else {
            $patterns [0] = $pattern;
        }
        $i = 0;
        $dir = array();
        if (is_dir($filename)) {
            $filename = rtrim($filename, '/') . '/';
        }
        foreach ($patterns as $pattern) {
            $list = glob($filename . $pattern);
            if ($list !== false) {
                foreach ($list as $file) {
                    $dir [$i] ['filename'] = basename($file);
                    $dir [$i] ['path'] = dirname($file);
                    $dir [$i] ['pathname'] = realpath($file);
                    $dir [$i] ['owner'] = fileowner($file);
                    $dir [$i] ['perms'] = substr(base_convert(fileperms($file), 10, 8), -4);
                    $dir [$i] ['atime'] = fileatime($file);
                    $dir [$i] ['ctime'] = filectime($file);
                    $dir [$i] ['mtime'] = filemtime($file);
                    $dir [$i] ['size'] = filesize($file);
                    $dir [$i] ['type'] = filetype($file);
                    $dir [$i] ['ext'] = is_file($file) ? strtolower(substr(strrchr(basename($file), '.'), 1)) : '';
                    $dir [$i] ['isDir'] = is_dir($file);
                    $dir [$i] ['isFile'] = is_file($file);
                    $dir [$i] ['isLink'] = is_link($file);
                    $dir [$i] ['isReadable'] = is_readable($file);
                    $dir [$i] ['isWritable'] = is_writable($file);
                    $i++;
                }
            }
        }
        $cmp_func = create_function('$a,$b', '
        if( ($a["isDir"] && $b["isDir"]) || (!$a["isDir"] && !$b["isDir"]) ){
            return  $a["filename"]>$b["filename"]?1:-1;
        }else{
            if($a["isDir"]){
                return -1;
            }else if($b["isDir"]){
                return 1;
            }
            if($a["filename"]  ==  $b["filename"])  return  0;
            return  $a["filename"]>$b["filename"]?-1:1;
        }
        ');
        usort($dir, $cmp_func);
        return $dir;
    }

}