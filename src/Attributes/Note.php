<?php

namespace Fomo\Attributes;

use Attribute;

/*
 * This feature is for note-taking only
 */
#[Attribute(Attribute::TARGET_ALL|Attribute::IS_REPEATABLE)]
class Note
{
    public function __construct(string $note) {}
}