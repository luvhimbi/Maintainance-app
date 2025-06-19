<?php

declare(strict_types=1);

namespace Zaxbux\BackblazeB2\Object\File;

use RuntimeException;
use Zaxbux\BackblazeB2\Classes\AbstractObjectInfo;

/**
 * @link https://www.backblaze.com/b2/docs/files.html#fileInfo
 * 
 * @package BackblazeB2\Object\File
 */
final class FileInfo extends AbstractObjectInfo {
	public const B2_FILE_INFO_MTIME = 'src_last_modified_millis';
	public const B2_FILE_INFO_LARGE_FILE_SHA1 = 'large_file_sha1';

	public static function fromArray(array $data): FileInfo {
		return new FileInfo($data);
	}

	/**
	 * @param int $timestamp     The timestamp, in milliseconds since UNIX epoch.
	 * @param bool $milliseconds Set `false` to convert time from seconds to milliseconds.
	 */
	public function setLastModifiedTimestamp(int $timestamp, ?bool $milliseconds = true) {
		$this->set(static::B2_FILE_INFO_MTIME, $milliseconds ? $timestamp : $timestamp * 1000);
	}
}
