<?php
namespace App\Classes\Enigma;

class Rotor {
    const LETTERS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private $description;
    private $map;
    private $reverseMap;
    private $turnover;

    public function __construct($description, $mapping, $turnoverLetter) {
        $this->description = $description;
        $this->map = self::buildMap($mapping);
        $this->reverseMap = array_flip($this->map);
        $this->turnover = (ord($turnoverLetter) - ord('A')) + 1;
    }

    public function turnover($position) {
        return $position == $this->turnover;
    }

    public function get($input, $reverse) {
        if ($reverse) {
            return $this->reverseMap[$input];
        } else {
            return $this->map[$input];
        }
    }

    public static function buildMap($mapping) {
        if (strlen($mapping) !== 26) throw new \Error("Not 26 in rotor mapping");

        $builder = [];
        for ($i = 0; $i < count(str_split($mapping)); $i++) {
            $builder[str_split(self::LETTERS)[$i]] = str_split($mapping)[$i];
        }

        return $builder;
    }

    public static function ROTOR_1_1930() {
        return new Rotor("Rotor 1, 1930", "EKMFLGDQVZNTOWYHXUSPAIBRCJ", "Q");
    }

    public static function ROTOR_2_1930() {
        return new Rotor("Rotor 2, 1930", "AJDKSIRUXBLHWTMCQGZNPYFVOE", "E");
    }

    public static function ROTOR_3_1930() {
        return new Rotor("Rotor 3, 1930", "BDFHJLCPRTXVZNYEIWGAKMUSQO", "V");
    }

    public static function ROTOR_4_1938() {
        return new Rotor("Rotor 4, 1938", "ESOVPZJAYQUIRHXLNFTGKDCMWB", "J");
    }

    public static function ROTOR_5_1938() {
        return new Rotor("Rotor 5, 1938", "VZBRGITYUPSDNHLXAWMJQOFECK", "Z");
    }

    public static function ROTOR_6() {
        return new Rotor("Rotor 6", "JPGVOUMFYQBENHZRDKASXLICTW", "Z");
    }

    public static function RELFECTOR_A() {
        return new Rotor("Reflector A", "EJMZALYXVBWFCRQUONTSPIKHGD", "Z");
    }

    public static function RELFECTOR_B() {
        return new Rotor("Reflector B", "YRUHQSLDPXNGOKMIEBFZCWVJAT", "Z");
    }

    public static function RELFECTOR_C() {
        return new Rotor("Reflector C", "FVPJIAOYEDRZXWGCTKUQSBNMHL", "Z");
    }

    public static function getRotor($name) {
        switch ($name) {
            case '1':
                return self::ROTOR_1_1930();
            break;
            case '2':
                return self::ROTOR_2_1930();
            break;
            case '3':
                return self::ROTOR_3_1930();
            break;
            case '4':
                return self::ROTOR_4_1938();
            break;
            case '5':
                return self::ROTOR_5_1938();
            break;
            case '6':
                return self::ROTOR_6();
            break;
            case 'A':
                return self::RELFECTOR_A();
            break;
            case 'B':
                return self::RELFECTOR_B();
            break;
            case 'C':
                return self::RELFECTOR_C();
            break;
        }
    }
}
