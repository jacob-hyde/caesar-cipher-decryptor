<?php
namespace App\Classes\Enigma;
use App\Classes\Enigma\MachineConfig;

class Machine {
    const LETTERS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private $rotor1;
    private $rotor2;
    private $rotor3;
    private $reflector;

    private $position1;
    private $position2;
    private $position3;

    private $rotor1Start;
    private $rotor2Start;
    private $rotor3Start;

    public static function machine($config, &$freeList) {
        if ($freeList->isEmpty()) {
            for ($i = 0; $i < 100000; $i++) {
                $freeList->enqueue(new Machine());
            }
        }
        $machine = $freeList->dequeue();
        $machine->init($config);
        return $machine;
    }

    public static function freeEnigmaMachine($machine, &$freeList) {
        $freeList->enqueue($machine);
    }

    public function init($config) {
        $this->rotor1 = $config->rotorA;
        $this->rotor2 = $config->rotorB;
        $this->rotor3 = $config->rotorC;
        $this->reflector = $config->reflector;
        $this->position1 = ord($config->positionA) - ord('A');
        $this->position2 = ord($config->positionB) - ord('A');
        $this->position3 = ord($config->positionC) - ord('A');
        $this->rotor1Start = $config->positionA;
        $this->rotor2Start = $config->positionB;
        $this->rotor3Start = $config->positionC;
    }

    public function step($letter) {
        $this->_moveRotors();

        $stepValue = $this->_getOutputIndex($this->rotor3, $this->position3, ord($letter) - ord('A'), false);
        $stepValue = $this->_getOutputIndex($this->rotor2, $this->position2, $stepValue, false);
        $stepValue = $this->_getOutputIndex($this->rotor1, $this->position1, $stepValue, false);
        $stepValue = $this->_getOutputIndex($this->reflector, 0, $stepValue, false);
        $stepValue = $this->_getOutputIndex($this->rotor1, $this->position1, $stepValue, true);
        $stepValue = $this->_getOutputIndex($this->rotor2, $this->position2, $stepValue, true);
        $stepValue = $this->_getOutputIndex($this->rotor3, $this->position3, $stepValue, true);
        return str_split(self::LETTERS)[$stepValue];
    }

    protected function _getOutputIndex($rotor, $rotorOffset, $inputIndex, $reverse) {
        $value = ($inputIndex + $rotorOffset) % 26;
        $letter = str_split(self::LETTERS)[$value];
        $letter = $rotor->get($letter, $reverse);
        $value = (ord($letter) - ord('A')) - $rotorOffset;
        if ($value < 0) {
            $value += 26;
        }
        return $value;
    }

    protected function _moveRotorsWithResult() {
        $this->_moveRotors();
        return [$this->position1, $this->position2, $this->position3];
    }

    private function _moveRotors() {
        $this->position3 = ($this->position3 + 1) % 26;
        if ($this->rotor3->turnover($this->position3)) {
            $this->position2 = ($this->position2 + 1) % 26;
            if ($this->rotor2->turnover($this->position2)) {
                $this->position1 = ($this->position1 + 1) % 26;
            }
        }

        if($this->rotor3->turnover($this->position3 - 1) && $this->rotor2->turnover($this->position2 + 1)) {
            $this->position2 = ($this->position2 + 1) % 26;
            if ($this->rotor2->turnover($this->position2)) {
                $this->position1 = ($this->position1 + 1) % 26;
            }
        }
    }
}
