<script>
    var __uploadButton = {
        swf:'@assets('assets/webuploader/Uploader.swf')',
        chunkSize: <?php echo \Edwin404\Base\Support\FileHelper::formattedSizeToBytes(ini_get('upload_max_filesize'))-500*1024; ?>
    };
</script>