<?php

class InhameWrapper
{

    protected $resource;
    const BASEDIR = '/tmp';

    public function stream_open($path, $mode, $options, &$opened_path)
    {
        $path = str_replace('inhame://', '', $path);
        $filePath = self::BASEDIR . DIRECTORY_SEPARATOR . $path;
        if (file_exists($filePath)) {
            $this->resource = fopen($filePath, 'r');
        }
        return true;
    }

    public function stream_read($count)
    {
        return fread($this->resource, $count);
    }

    public function stream_write($data)
    {
        return 0;
    }

    function stream_tell()
    {
        return ftell($this->resource);
    }

    function stream_eof()
    {
        return feof($this->resource);
    }

    function stream_seek($offset, $whence)
    {
        return fseek($this->resource, $offset, $whence);
    }

    function stream_stat()
    {
        return fstat($this->resource);
    }

    function __destruct()
    {
        fclose($this->resource);
    }

}
