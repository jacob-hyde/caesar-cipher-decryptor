<?php
namespace App\Classes\Enigma;

class MachineConfig {
    public $positionA;
    public $positionB;
    public $positionC;
    public $rotorA;
    public $rotorB;
    public $rotorC;
    public $reflector;

    public function __construct($positionA, $positionB, $positionC, $rotorA, $rotorB, $rotorC, $reflector) {
        $this->positionA = $positionA;
        $this->positionB = $positionB;
        $this->positionC = $positionC;
        $this->rotorA = $rotorA;
        $this->rotorB = $rotorB;
        $this->rotorC = $rotorC;
        $this->reflector = $reflector;
    }
}
