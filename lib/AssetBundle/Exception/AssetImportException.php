<?php

namespace Froq\AssetBundle\Exception;

class AssetImportException extends \RuntimeException
{
    /**
     *
     * @param string $fileName
     *
     * @return self
     */
    public static function fileDoesntExit(string $fileName): self
    {
        $message = sprintf('Failed to import Asset: File "%s" does not exist.', $fileName);

        return new self($message);
    }

    /**
     *
     * @param string $fileName
     *
     * @return self
     */
    public static function emptyFile(string $fileName): self
    {
        $message = sprintf('Failed to import Asset: File "%s" is empty.', $fileName);

        return new self($message);
    }
}
