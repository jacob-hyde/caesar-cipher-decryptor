<?php
namespace App\Classes\Enigma;
use App\Classes\Enigma\Triple;
use App\Classes\Enigma\Machine;
use App\Classes\Enigma\MachineConfig;
use App\Classes\Enigma\Rotor;
use App\Classes\Enigma\FrequencyAnalysis;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class Enigma {
    const LETTERS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const message = 'ZTQBLVXKPBPGAVQBRYDYQEZNKRLMZTMRGBJSQKHDPHHNTNIDLYVFCOKZYYSMJFAHQBTEAVFKOXRPSQX';
    const nonLettersOrSpacePattern = "/[^A-Z\s]/";
    const crib = null;
    const rotors = [1,2,3];
    const reflectors = ['A', 'B', 'C'];
    public $freeList;
    public $output;
    public $total;
    public $message;

    public static function freeList() {
        $freeList = new \SplQueue;
        for ($i = 0; $i < 200000; $i++) {
            $freeList->enqueue(new Machine());
        }
        return $freeList;
    }

    public function __construct($a, $b) {
        $this->freeList = self::freeList();
        $this->message = $a;
        $this->message = preg_replace("/[\s]/", " ", preg_replace(self::nonLettersOrSpacePattern, ' ', strtoupper($this->message)));
        $this->message = explode(" ", $this->message);
        $this->message = array_filter($this->message, 'trim');
        $this->message = array_filter($this->message, 'strlen');
        $this->message = array_unique($this->message, SORT_STRING);
        $this->message = implode("", $this->message);
    }

    public function decrypt() {
        $letters = str_split(self::LETTERS);

        $rotors = [];
        foreach (self::rotors as $rotor) {
            $rotors[] = Rotor::getRotor($rotor);
        }
        $reflectors = [];
        foreach (self::reflectors as $reflector) {
            $reflectors[] = Rotor::getRotor($reflector);
        }
        $startingPositions = $this->permutations(str_split(self::LETTERS), true);
        $rotorPermutations = $this->permutations($rotors, false);
        $tasks = [];

        $output = new ConsoleOutput();
        $progressBar1 = new ProgressBar($output, count($rotorPermutations));
        foreach ($rotorPermutations as $rotors) {
            $progressBar1->advance();
            print "\n";
            foreach ($reflectors as $reflector) {
                $progressBar2 = new ProgressBar($output, count($startingPositions));
                foreach ($startingPositions as $startingPosition) {
                    $progressBar2->advance();
                    $config = new MachineConfig($startingPosition->o1, $startingPosition->o2, $startingPosition->o3, $rotors->o1, $rotors->o2, $rotors->o3, $reflector);
                    $task = new EnigmaCallable($config, $this->message, self::crib, $this->freeList);
                    $result = $task->run($this->freeList);
                    $tasks[] = $result;
                }
                $progressBar2->finish();
            }
        }
        $progressBar1->finish();
        usort($tasks, function ($item1, $item2) {
            return $item1[0] <=> $item2[0];
        });
        dd($tasks[0]);
        dd($tasks[1]);
        dd($tasks[2]);
    }

    public function permutations($items, $duplicates) {
        $permutations = [];
        foreach ($items as $o1) {
            foreach ($items as $o2) {
                if (!$duplicates && $o1 == $o2) {
                    continue;
                }

                foreach ($items as $o3) {
                    if (!$duplicates && ($o1 == $o2 || $o1 == $o3 || $o2 == $o3)) {
                        continue;
                    }
                    $permutations[] = new Triple($o1, $o2, $o3);
                }
            }
        }
        return $permutations;
    }
}

class EnigmaCallable {
    private $machine;
    private $analysis;
    private $message;
    private $crib;

    public function __construct($config, $message, $crib, $freeList) {
        $this->message = $message;
        $this->crib = $crib;
        $this->machine = Machine::machine($config, $freeList);
        $this->analysis = new FrequencyAnalysis();
    }

    public function run(&$freeList) {
        $result = '';
        foreach (str_split($this->message) as $c) {
            $letter = $this->machine->step($c);
            $result .= $letter;
            $this->analysis->add($letter);
        }

        $score = $this->analysis->calculateDifference();
        Machine::freeEnigmaMachine($this->machine, $freeList);
        return [$score, $result];
    }
}
