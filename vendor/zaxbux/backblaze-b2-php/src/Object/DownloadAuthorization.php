<?php

declare(strict_types=1);

namespace Zaxbux\BackblazeB2\Object;

use Zaxbux\BackblazeB2\Interfaces\B2ObjectInterface;
use Zaxbux\BackblazeB2\Traits\HydrateFromResponseTrait;
use Zaxbux\BackblazeB2\Traits\ProxyArrayAccessToPropertiesTrait;

/** @package BackblazeB2\Object */
class DownloadAuthorization implements B2ObjectInterface
{
	use ProxyArrayAccessToPropertiesTrait;
	use HydrateFromResponseTrait;

	public const ATTRIBUTE_BUCKET_ID           = 'bucketId';
	public const ATTRIBUTE_FILE_NAME_PREFIX    = 'fileNamePrefix';
	public const ATTRIBUTE_AUTHORIZATION_TOKEN = 'authorizationToken';

	public const VALID_DURATION_MIN = 1;
	public const VALID_DURATION_MAX = 604800; // one week

	/** @var string */
	private $bucketId;

	/** @var string */
	private $fileNamePrefix;

	/** @var string */
	private $authorizationToken;

	/**
	 * @param string $bucketId 
	 * @param string $fileNamePrefix 
	 * @param string $authorizationToken 
	 */
	public function __construct(
		string $bucketId,
		string $fileNamePrefix,
		string $authorizationToken
	) {
		$this->bucketId           = $bucketId;
		$this->fileNamePrefix     = $fileNamePrefix;
		$this->authorizationToken = $authorizationToken;
	}

	/**
	 * Get the value of authorizationToken
	 */
	public function authorizationToken()
	{
		return $this->authorizationToken;
	}

	/**
	 * Set the value of authorizationToken
	 *
	 * @return  self
	 */
	public function setAuthorizationToken($authorizationToken)
	{
		$this->authorizationToken = $authorizationToken;

		return $this;
	}

	/**
	 * Get the value of fileNamePrefix
	 */
	public function fileNamePrefix()
	{
		return $this->fileNamePrefix;
	}

	/**
	 * Set the value of fileNamePrefix
	 *
	 * @return  self
	 */
	public function setFileNamePrefix($fileNamePrefix)
	{
		$this->fileNamePrefix = $fileNamePrefix;

		return $this;
	}

	/**
	 * Get the value of bucketId
	 */
	public function bucketId()
	{
		return $this->bucketId;
	}

	/**
	 * Set the value of bucketId
	 *
	 * @return  self
	 */
	public function setBucketId($bucketId)
	{
		$this->bucketId = $bucketId;

		return $this;
	}

	public static function fromArray(array $data): DownloadAuthorization
	{
		return new DownloadAuthorization(
			$data[static::ATTRIBUTE_BUCKET_ID],
			$data[static::ATTRIBUTE_FILE_NAME_PREFIX],
			$data[static::ATTRIBUTE_AUTHORIZATION_TOKEN],
		);
	}

	public function jsonSerialize(): array
	{
		return [
			static::ATTRIBUTE_BUCKET_ID           => $this->bucketId,
			static::ATTRIBUTE_FILE_NAME_PREFIX    => $this->fileNamePrefix,
			static::ATTRIBUTE_AUTHORIZATION_TOKEN => $this->authorizationToken,
		];
	}
}
