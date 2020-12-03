<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Tests;

use Amp\PHPUnit\AsyncTestCase;
use SebastianBergmann\Diff\Differ;
use const Vajexal\AmpMailer\Smtp\SMTP_LINE_BREAK;

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

    protected function assertOutputMatchesPatternLineByLine(string $pattern, string $output, string $delimiter = SMTP_LINE_BREAK): void
    {
        $patternLines = \explode($delimiter, $pattern);
        $outputLines  = \explode($delimiter, $output);

        if (\count($patternLines) !== \count($outputLines)) {
            $this->fail('Pattern lines count != output lines count. Use assertOutputMatchesPattern for more detailed comparison');
        }

        $differentLines = [];

        for ($i = 0; $i < \count($patternLines); $i++) {
            if ($patternLines[$i] === $outputLines[$i]) {
                continue;
            }

            if (\preg_match(\sprintf('/^%s$/', $patternLines[$i]), $outputLines[$i])) {
                continue;
            }

            $differentLines[] = \sprintf('%d: %s - %s', $i, $patternLines[$i], $outputLines[$i]);
        }

        $this->assertEmpty(
            $differentLines,
            \sprintf(
                "Failed asserting that output matches pattern (use assertOutputMatchesPattern for more detailed comparison):\n%s",
                \implode(PHP_EOL, $differentLines)
            )
        );
    }
}
