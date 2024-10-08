<?php

/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Ramsey\Uuid;

use DateTimeInterface;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\Validator\ValidatorInterface;

/**
 * This interface encapsulates deprecated methods for ramsey/uuid; this
 * interface and its methods will be removed in ramsey/uuid 5.0.0.
 *
 * @deprecated This interface and its methods will be removed in ramsey/uuid 5.0.0.
 */
interface DeprecatedUuidFactoryInterface
{
    /**
     * Creates a UUID from a DateTimeInterface instance
     *
     * @deprecated In ramsey/uuid version 5, UUID factories will no longer have
     *     dedicated methods for creating datetime-based UUIDs. Use a dedicated
     *     factory to generate UUIDs from a DateTime instance.
     *
     * @param DateTimeInterface $dateTime The date and time
     * @param Hexadecimal|null $node A 48-bit number representing the hardware
     *     address
     * @param int<0, 16383>|null $clockSeq A 14-bit number used to help avoid
     *     duplicates that could arise when the clock is set backwards in time
     *     or if the node ID changes
     *
     * @return UuidInterface A UuidInterface instance that represents a
     *     version 1 UUID created from a DateTimeInterface instance
     */
    public function fromDateTime(
        DateTimeInterface $dateTime,
        ?Hexadecimal $node = null,
        ?int $clockSeq = null
    ): UuidInterface;

    /**
     * Returns the validator to use for the factory
     *
     * @deprecated In ramsey/uuid version 5, UUID factories will no longer have
     *     dedicated methods for getting validators. Use a dedicated validator
     *     class to validate UUIDs.
     *
     * @psalm-mutation-free
     */
    public function getValidator(): ValidatorInterface;

    /**
     * Returns a version 1 (time-based) UUID from a host ID, sequence number,
     * and the current time
     *
     * @deprecated In ramsey/uuid version 5, UUID factories will no longer have
     *     methods specific to creating subtypes. Instead, version 5 will use
     *     dedicated factories for each subtype.
     *
     * @param Hexadecimal|positive-int|non-empty-string|null $node A 48-bit
     *     number representing the hardware address; this number may be
     *     represented as an integer or a hexadecimal string
     * @param int<0, 16383>|null $clockSeq A 14-bit number used to help avoid
     *     duplicates that could arise when the clock is set backwards in time
     *     or if the node ID changes
     *
     * @return UuidInterface A UuidInterface instance that represents a
     *     version 1 UUID
     */
    public function uuid1(Hexadecimal | int | string | null $node = null, ?int $clockSeq = null): UuidInterface;

    /**
     * Returns a version 2 (DCE Security) UUID from a local domain, local
     * identifier, host ID, clock sequence, and the current time
     *
     * @deprecated In ramsey/uuid version 5, UUID factories will no longer have
     *     methods specific to creating subtypes. Instead, version 5 will use
     *     dedicated factories for each subtype.
     *
     * @param int $localDomain The local domain to use when generating bytes,
     *     according to DCE Security
     * @param IntegerObject|null $localIdentifier The local identifier for the
     *     given domain; this may be a UID or GID on POSIX systems, if the local
     *     domain is person or group, or it may be a site-defined identifier
     *     if the local domain is org
     * @param Hexadecimal|null $node A 48-bit number representing the hardware
     *     address
     * @param int<0, 63>|null $clockSeq A 6-bit number used to help avoid
     *     duplicates that could arise when the clock is set backwards in time
     *     or if the node ID changes
     *
     * @return UuidInterface A UuidInterface instance that represents a
     *     version 2 UUID
     */
    public function uuid2(
        int $localDomain,
        ?IntegerObject $localIdentifier = null,
        ?Hexadecimal $node = null,
        ?int $clockSeq = null
    ): UuidInterface;

    /**
     * Returns a version 3 (name-based) UUID based on the MD5 hash of a
     * namespace ID and a name
     *
     * @deprecated In ramsey/uuid version 5, UUID factories will no longer have
     *     methods specific to creating subtypes. Instead, version 5 will use
     *     dedicated factories for each subtype.
     *
     * @param non-empty-string|UuidInterface $ns The namespace (must be a valid UUID)
     * @param string $name The name to use for creating a UUID
     *
     * @return UuidInterface A UuidInterface instance that represents a
     *     version 3 UUID
     *
     * @psalm-pure
     */
    public function uuid3(UuidInterface | string $ns, string $name): UuidInterface;

    /**
     * Returns a version 4 (random) UUID
     *
     * @deprecated In ramsey/uuid version 5, UUID factories will no longer have
     *     methods specific to creating subtypes. Instead, version 5 will use
     *     dedicated factories for each subtype.
     *
     * @return UuidInterface A UuidInterface instance that represents a
     *     version 4 UUID
     */
    public function uuid4(): UuidInterface;

    /**
     * Returns a version 5 (name-based) UUID based on the SHA-1 hash of a
     * namespace ID and a name
     *
     * @deprecated In ramsey/uuid version 5, UUID factories will no longer have
     *     methods specific to creating subtypes. Instead, version 5 will use
     *     dedicated factories for each subtype.
     *
     * @param non-empty-string|UuidInterface $ns The namespace (must be a valid UUID)
     * @param string $name The name to use for creating a UUID
     *
     * @return UuidInterface A UuidInterface instance that represents a
     *     version 5 UUID
     *
     * @psalm-pure
     */
    public function uuid5(UuidInterface | string $ns, string $name): UuidInterface;

    /**
     * Returns a version 6 (ordered-time) UUID from a host ID, sequence number,
     * and the current time
     *
     * @deprecated In ramsey/uuid version 5, UUID factories will no longer have
     *     methods specific to creating subtypes. Instead, version 5 will use
     *     dedicated factories for each subtype.
     *
     * @param Hexadecimal|null $node A 48-bit number representing the hardware
     *     address
     * @param int<0, 16383>|null $clockSeq A 14-bit number used to help avoid
     *     duplicates that could arise when the clock is set backwards in time
     *     or if the node ID changes
     *
     * @return UuidInterface A UuidInterface instance that represents a
     *     version 6 UUID
     */
    public function uuid6(?Hexadecimal $node = null, ?int $clockSeq = null): UuidInterface;
}
