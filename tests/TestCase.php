<?php

namespace Vajexal\AmpMailer\Tests;

use Amp\PHPUnit\AsyncTestCase;
use SebastianBergmann\Diff\Differ;

class TestCase extends AsyncTestCase
{
    protected function assertOutputMatchesPattern(string $pattern, string $output): void
    {
        $differ         = new Differ;
        $differentLines = [];

        $diff = $differ->diffToArray($pattern, $output);

        if (isset($diff[0]) && $diff[0][1] === Differ::DIFF_LINE_END_WARNING) {
            $this->fail($diff[0][0]);
        }

        for ($i = 0; $i < \count($diff); $i++) {
            if ($diff[$i][1] === Differ::OLD) {
                continue;
            }

            if ($diff[$i][1] === Differ::REMOVED && isset($diff[$i + 1]) && $diff[$i + 1][1] === Differ::ADDED) {
                // line changed
                if (\preg_match(\sprintf('/^%s$/', $diff[$i][0]), $diff[$i + 1][0])) {
                    $i++; // skip next diff
                    continue;
                }
            }

            $differentLines[] = ($diff[$i][1] === Differ::REMOVED ? '--' : '++') . \rtrim($diff[$i][0]);
        }

        $this->assertEmpty($differentLines, \sprintf("Failed asserting that output matches pattern:\n%s", \implode(PHP_EOL, $differentLines)));
    }
}
