<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser;

enum Format: string
{
    case Email = 'email';
    case Hostname = 'hostname';
    case IPv4 = 'ipv4';
    case IPv6 = 'ipv6';
    case Uri = 'uri';
    case Iri = 'iri';
    case Uuid = 'uuid';
}