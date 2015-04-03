<?php

namespace Gravity\FileBundle\File\StreamWrapper\Local;

use Gravity\FileBundle\File\StreamWrapper\StreamWrapperInterface;

/**
 * Class LocalStreamWrapper
 *
 * @package Gravity\FileBundle\File\StreamWrapper\Local
 * @author  Andy Thorne <contrabandvr@gmail.com>
 */
abstract class LocalStreamWrapper implements StreamWrapperInterface
{
    /**
     * Stream context resource.
     *
     * @var resource
     */
    public $context;

    /**
     * A generic resource handle.
     *
     * @var resource
     */
    public $handle = null;

    /**
     * Instance URI (stream).
     *
     * A stream is referenced as "scheme://target".
     *
     * @var string
     */
    protected $uri;

    static function create()
    {
        return new static();
    }


    /**
     * Gets the path that the wrapper is responsible for.
     *
     * @return string
     *   String specifying the path.
     */
    abstract function getDirectoryPath();

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * Returns the local writable target of the resource within the stream.
     *
     * This function should be used in place of calls to realpath() or similar
     * functions when attempting to determine the location of a file. While
     * functions like realpath() may return the location of a read-only file, this
     * method may return a URI or path suitable for writing that is completely
     * separate from the URI used for reading.
     *
     * @param string $uri
     *   Optional URI.
     *
     * @return string|bool
     *   Returns a string representing a location suitable for writing of a file,
     *   or FALSE if unable to write to the file such as with read-only streams.
     */
    protected function getTarget($uri = null)
    {
        if (!isset($uri)) {
            $uri = $this->uri;
        }

        list(, $target) = explode('://', $uri, 2);

        // Remove erroneous leading or trailing, forward-slashes and backslashes.
        return trim($target, '\/');
    }

    /**
     * Implements StreamWrapperInterface::realpath().
     */
    function realpath()
    {
        return $this->getLocalPath();
    }

    /**
     * Returns the canonical absolute path of the URI, if possible.
     *
     * @param string $uri
     *   (optional) The stream wrapper URI to be converted to a canonical
     *   absolute path. This may point to a directory or another type of file.
     *
     * @return string|bool
     *   If $uri is not set, returns the canonical absolute path of the URI
     *   previously set.
     *   If $uri is set and valid for this class, returns its canonical absolute
     *   path, as determined by the realpath() function. If $uri is set but not
     *   valid, returns FALSE.
     */
    protected function getLocalPath($uri = null)
    {
        if (!isset($uri)) {
            $uri = $this->uri;
        }
        $path     = $this->getDirectoryPath() . '/' . $this->getTarget($uri);
        $realpath = realpath($path);
        if (!$realpath) {
            // This file does not yet exist.
            $realpath = realpath(dirname($path)) . '/' . basename($path);
        }
        $directory = realpath($this->getDirectoryPath());
        if (!$realpath || !$directory || strpos($realpath, $directory) !== 0) {
            return false;
        }

        return $realpath;
    }

    /**
     * Support for fopen(), file_get_contents(), file_put_contents() etc.
     *
     * @param string $uri
     *   A string containing the URI to the file to open.
     * @param int    $mode
     *   The file mode ("r", "wb" etc.).
     * @param int    $options
     *   A bit mask of STREAM_USE_PATH and STREAM_REPORT_ERRORS.
     * @param string $opened_path
     *   A string containing the path actually opened.
     *
     * @return bool
     *   Returns TRUE if file was opened successfully.
     *
     * @see http://php.net/manual/streamwrapper.stream-open.php
     */
    public function stream_open($uri, $mode, $options, &$opened_path)
    {
        $this->uri    = $uri;
        $path         = $this->getLocalPath();
        $this->handle = ($options & STREAM_REPORT_ERRORS) ? fopen($path, $mode) : @fopen($path, $mode);

        if ((bool)$this->handle && $options & STREAM_USE_PATH) {
            $opened_path = $path;
        }

        return (bool)$this->handle;
    }

    /**
     * Support for flock().
     *
     * @param int $operation
     *     One of the following:
     *     - LOCK_SH to acquire a shared lock (reader).
     *     - LOCK_EX to acquire an exclusive lock (writer).
     *     - LOCK_UN to release a lock (shared or exclusive).
     *     - LOCK_NB if you don't want flock() to block while locking (not
     *     supported on Windows).
     *
     * @return bool
     *   Always returns TRUE at the present time.
     *
     * @see http://php.net/manual/streamwrapper.stream-lock.php
     */
    public function stream_lock($operation)
    {
        if (in_array($operation, [LOCK_SH, LOCK_EX, LOCK_UN, LOCK_NB])) {
            return flock($this->handle, $operation);
        }

        return true;
    }

    /**
     * Support for fread(), file_get_contents() etc.
     *
     * @param int $count
     *   Maximum number of bytes to be read.
     *
     * @return string|bool
     *   The string that was read, or FALSE in case of an error.
     *
     * @see http://php.net/manual/streamwrapper.stream-read.php
     */
    public function stream_read($count)
    {
        return fread($this->handle, $count);
    }

    /**
     * Support for fwrite(), file_put_contents() etc.
     *
     * @param string $data
     *   The string to be written.
     *
     * @return int
     *   The number of bytes written.
     *
     * @see http://php.net/manual/streamwrapper.stream-write.php
     */
    public function stream_write($data)
    {
        return fwrite($this->handle, $data);
    }

    /**
     * Support for feof().
     *
     * @return bool
     *   TRUE if end-of-file has been reached.
     *
     * @see http://php.net/manual/streamwrapper.stream-eof.php
     */
    public function stream_eof()
    {
        return feof($this->handle);
    }

    /**
     * Support for fseek().
     *
     * @param int $offset
     *   The byte offset to got to.
     * @param int $whence
     *   SEEK_SET, SEEK_CUR, or SEEK_END.
     *
     * @return bool
     *   TRUE on success.
     *
     * @see http://php.net/manual/streamwrapper.stream-seek.php
     */
    public function stream_seek($offset, $whence = SEEK_SET)
    {
        // fseek returns 0 on success and -1 on a failure.
        // stream_seek   1 on success and  0 on a failure.
        return !fseek($this->handle, $offset, $whence);
    }

    /**
     * Support for fflush().
     *
     * @return bool
     *   TRUE if data was successfully stored (or there was no data to store).
     *
     * @see http://php.net/manual/streamwrapper.stream-flush.php
     */
    public function stream_flush()
    {
        return fflush($this->handle);
    }

    /**
     * Support for ftell().
     *
     * @return bool
     *   The current offset in bytes from the beginning of file.
     *
     * @see http://php.net/manual/streamwrapper.stream-tell.php
     */
    public function stream_tell()
    {
        return ftell($this->handle);
    }

    /**
     * Support for fstat().
     *
     * @return bool
     *   An array with file status, or FALSE in case of an error - see fstat()
     *   for a description of this array.
     *
     * @see http://php.net/manual/streamwrapper.stream-stat.php
     */
    public function stream_stat()
    {
        return fstat($this->handle);
    }

    /**
     * Support for fclose().
     *
     * @return bool
     *   TRUE if stream was successfully closed.
     *
     * @see http://php.net/manual/streamwrapper.stream-close.php
     */
    public function stream_close()
    {
        return fclose($this->handle);
    }

    /**
     * Gets the underlying stream resource for stream_select().
     *
     * @param int $cast_as
     *   Can be STREAM_CAST_FOR_SELECT or STREAM_CAST_AS_STREAM.
     *
     * @return resource|false
     *   The underlying stream resource or FALSE if stream_select() is not
     *   supported.
     *
     * @see http://php.net/manual/streamwrapper.stream-cast.php
     */
    public function stream_cast($cast_as)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function stream_metadata($uri, $option, $value)
    {
        $target = $this->getLocalPath($uri);
        $return = false;
        switch ($option) {
            case STREAM_META_TOUCH:
                if (!empty($value)) {
                    $return = touch($target, $value[0], $value[1]);
                } else {
                    $return = touch($target);
                }
                break;

            case STREAM_META_ACCESS:
                $return = chmod($target, $value);
                break;
        }
        if ($return) {
            // For convenience clear the file status cache of the underlying file,
            // since metadata operations are often followed by file status checks.
            clearstatcache(true, $target);
        }

        return $return;
    }

    /**
     * Support for unlink().
     *
     * @param string $uri
     *   A string containing the URI to the resource to delete.
     *
     * @return bool
     *   TRUE if resource was successfully deleted.
     *
     * @see http://php.net/manual/streamwrapper.unlink.php
     */
    public function unlink($uri)
    {
        $this->uri = $uri;

        return unlink($this->getLocalPath());
    }

    /**
     * Support for rename().
     *
     * @param string $from_uri ,
     *                         The URI to the file to rename.
     * @param string $to_uri
     *                         The new URI for file.
     *
     * @return bool
     *   TRUE if file was successfully renamed.
     *
     * @see http://php.net/manual/streamwrapper.rename.php
     */
    public function rename($from_uri, $to_uri)
    {
        return rename($this->getLocalPath($from_uri), $this->getLocalPath($to_uri));
    }

    /**
     * Support for dirname().
     *
     * @param string $uri
     *   A URI or path.
     *
     * @return string
     *   A string containing the directory name.
     */
    public function dirname($uri = null)
    {
        list($scheme) = explode('://', $uri, 2);
        $target  = $this->getTarget($uri);
        $dirname = dirname($target);

        if ($dirname == '.') {
            $dirname = '';
        }

        return $scheme . '://' . $dirname;
    }

    /**
     * Support for mkdir().
     *
     * @param string $uri
     *   A string containing the URI to the directory to create.
     * @param int    $mode
     *   Permission flags - see mkdir().
     * @param int    $options
     *   A bit mask of STREAM_REPORT_ERRORS and STREAM_MKDIR_RECURSIVE.
     *
     * @return bool
     *   TRUE if directory was successfully created.
     *
     * @see http://php.net/manual/streamwrapper.mkdir.php
     */
    public function mkdir($uri, $mode, $options)
    {
        $this->uri = $uri;
        $recursive = (bool)($options & STREAM_MKDIR_RECURSIVE);
        if ($recursive) {
            // $this->getLocalPath() fails if $uri has multiple levels of directories
            // that do not yet exist.
            $localpath = $this->getDirectoryPath() . '/' . $this->getTarget($uri);
        } else {
            $localpath = $this->getLocalPath($uri);
        }
        if ($options & STREAM_REPORT_ERRORS) {
            return mkdir($localpath, $mode, $recursive);
        } else {
            return @mkdir($localpath, $mode, $recursive);
        }
    }

    /**
     * Support for rmdir().
     *
     * @param string $uri
     *   A string containing the URI to the directory to delete.
     * @param int    $options
     *   A bit mask of STREAM_REPORT_ERRORS.
     *
     * @return bool
     *   TRUE if directory was successfully removed.
     *
     * @see http://php.net/manual/streamwrapper.rmdir.php
     */
    public function rmdir($uri, $options)
    {
        $this->uri = $uri;
        if ($options & STREAM_REPORT_ERRORS) {
            return rmdir($this->getLocalPath());
        } else {
            return @rmdir($this->getLocalPath());
        }
    }

    /**
     * Support for stat().
     *
     * @param string $uri
     *   A string containing the URI to get information about.
     * @param int    $flags
     *   A bit mask of STREAM_URL_STAT_LINK and STREAM_URL_STAT_QUIET.
     *
     * @return array
     *   An array with file status, or FALSE in case of an error - see fstat()
     *   for a description of this array.
     *
     * @see http://php.net/manual/streamwrapper.url-stat.php
     */
    public function url_stat($uri, $flags)
    {
        $this->uri = $uri;
        $path      = $this->getLocalPath();
        // Suppress warnings if requested or if the file or directory does not
        // exist. This is consistent with PHP's plain filesystem stream wrapper.
        if ($flags & STREAM_URL_STAT_QUIET || !file_exists($path)) {
            return @stat($path);
        } else {
            return stat($path);
        }
    }

    /**
     * Support for opendir().
     *
     * @param string $uri
     *   A string containing the URI to the directory to open.
     * @param int    $options
     *   Unknown (parameter is not documented in PHP Manual).
     *
     * @return bool
     *   TRUE on success.
     *
     * @see http://php.net/manual/streamwrapper.dir-opendir.php
     */
    public function dir_opendir($uri, $options)
    {
        $this->uri    = $uri;
        $this->handle = opendir($this->getLocalPath());

        return (bool)$this->handle;
    }

    /**
     * Support for readdir().
     *
     * @return string
     *   The next filename, or FALSE if there are no more files in the directory.
     *
     * @see http://php.net/manual/streamwrapper.dir-readdir.php
     */
    public function dir_readdir()
    {
        return readdir($this->handle);
    }

    /**
     * Support for rewinddir().
     *
     * @return bool
     *   TRUE on success.
     *
     * @see http://php.net/manual/streamwrapper.dir-rewinddir.php
     */
    public function dir_rewinddir()
    {
        rewinddir($this->handle);
        // We do not really have a way to signal a failure as rewinddir() does not
        // have a return value and there is no way to read a directory handler
        // without advancing to the next file.
        return true;
    }

    /**
     * Support for closedir().
     *
     * @return bool
     *   TRUE on success.
     *
     * @see http://php.net/manual/streamwrapper.dir-closedir.php
     */
    public function dir_closedir()
    {
        closedir($this->handle);
        // We do not really have a way to signal a failure as closedir() does not
        // have a return value.
        return true;
    }


    /**
     * @param int $option
     * @param int $arg1
     * @param int $arg2
     *
     * @return bool
     */
    public function stream_set_option($option, $arg1, $arg2)
    {
        switch ($option) {
            case STREAM_OPTION_BLOCKING:
                stream_set_blocking($this->handle, $arg1);
                break;

            case STREAM_OPTION_READ_TIMEOUT:
                stream_set_timeout($this->handle, $arg1, $arg2);
                break;

            case STREAM_OPTION_WRITE_BUFFER :
                stream_set_write_buffer($this->handle, $arg2);
                break;

            default:
                return false;
        }

        return true;
    }
}
