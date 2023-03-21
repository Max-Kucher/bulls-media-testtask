<?php

namespace App\Helpers\SpreadSheetsParsing;

/**
 * Can not rewind remote file -> rewrite rewind method
 *
 * @inheritDoc
 */
class NoRewindSplFileObject extends \SplFileObject
{
    public function rewind() {}
}
