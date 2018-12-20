<?php
namespace App\Classes\Enigma;

class FrequencyAnalysis {
    const ENGLISH_EXPECTED_FREQUENCY = [
        'A' => 8.167, 'B' => 1.492, 'C' => 2.782, 'D' => 4.253, 'E' => 12.702,
        'F' => 2.228, 'G' => 2.015, 'H' => 6.094, 'I' => 6.966, 'J' => 0.153,
        'K' => 0.772, 'L' => 4.025, 'M' => 2.406, 'N' => 6.749, 'O' => 7.507,
        'P' => 1.929, 'Q' => 0.095, 'R' => 5.987, 'S' => 6.327, 'T' => 9.056,
        'U' => 2.758, 'V' => 0.978, 'W' => 2.360, 'X' => 0.150, 'Y' => 1.974,
        'Z' => 0.074
    ];

    private $analysis;
    private $totalCount = 0;

    public function __construct() {
        foreach (self::ENGLISH_EXPECTED_FREQUENCY as $key => $val) {
            $this->analysis[$key] = 0;
        }
    }

    public function add($c) {
        $this->analysis[$c] = $this->analysis[$c] + 1;
        $this->totalCount++;
    }

    public function calculateDifference() {
        $totalDifference = 0.0;
        foreach (self::ENGLISH_EXPECTED_FREQUENCY as $c => $value) {
            $count = $this->analysis[$c];
            if ($count == 0) {
                continue;
            }

            $actual = ($count / $this->totalCount) * 100;
            $expected = self::ENGLISH_EXPECTED_FREQUENCY[$c];
            $totalDifference += abs($actual - $expected);
        }
        return $totalDifference;
    }

}
