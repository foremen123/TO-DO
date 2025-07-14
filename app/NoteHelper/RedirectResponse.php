<?php

namespace app\NoteHelper;

use JetBrains\PhpStorm\NoReturn;

class RedirectResponse
{
    public function __construct(public string $location)
    {
    }
}