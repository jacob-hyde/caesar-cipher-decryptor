<?php
namespace App\Classes;
use App\Classes\WordPatterns;
use App\Events\DecryptPercentUpdate;
use App\Events\DecryptLetterMatches;
class Decrypt {
    public $bar;
    public $percent;
    public $text;
    public $callback;
    public $cipherWordList;
    private $_wordPatterns;
    const letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const nonLettersOrSpacePattern = "/[^A-Z\s]/";

    /**
     * Class Contructor
     * Creates a word pattern for each word in the dictionary
     * @param text - The text to decode
     * @param key - The key if provided to grab more words from to decode the text
     */
    public function __construct($text, $key = null) {
        ini_set('memory_limit', '12096M');
        ini_set('max_execution_time', 300);
        $this->_wordPatterns = WordPatterns::createWordPatterns($key);
        $this->text = $text;
        $this->_createCipherWordList();
    }

    /**
     * The main method that runs the decryption by creating a letter map
     * and looping through all words in the cipher and creating a letter map
     * and compairing to the dictionary letter/word map
     */
    public function decrypt() {
        $intersectedMap = self::getBlankCipherletterMapping();
        $i = 0;
        foreach ($this->cipherWordList as $cipherWord) {
            if ($this->bar) {
                $this->bar->advance();
            }
            $i++;
            $this->percent = round($i/count($this->cipherWordList), 2) * 100;
            event(new DecryptPercentUpdate($this->percent));

            $newMap = self::getBlankCipherletterMapping();

            //creates a pattern like 0.1.2.1.3
            $wordPattern = WordPatterns::getWordPattern($cipherWord);

            //if we have nothing similar in our dicionary map continue
            if (!isset($this->_wordPatterns[$wordPattern])) {
                continue;
            }

            //for each word in the dictionary corresponding to the letter patterns
            //add each unique letter to this words map
            foreach ($this->_wordPatterns[$wordPattern] as $word) {
                $newMap = $this->_addLettersToMapping($newMap, $cipherWord, $word);
            }

            //interesect map with the main one with the one for this word
            $intersectedMap = $this->_intersectMappings($intersectedMap, $newMap);

        }

        $letterMapping = $this->_removeSolvedLettersFromMapping($intersectedMap);

        return $this->decryptWithCipherLetterMapping($this->text, $letterMapping);
    }

    /**
     * Given the cipher text and letter mapping create a cross reference key
     */
    public function decryptWithCipherLetterMapping($cipherText, $letterMapping) {
        $key = array_fill(0, strlen(self::letters), 'x');
        foreach (str_split(self::letters) as $i => $cipherLetter) {

            if (count($letterMapping[$cipherLetter]) === 1) {
                $keyIndex = array_search($letterMapping[$cipherLetter][0], str_split(self::letters));
                $key[$keyIndex] = $cipherLetter;
            }
        }
        $key = implode('', $key);

        return $this->translateMessage($key, $cipherText);
    }

    /**
     * Translate a message from a key
     */
    public function translateMessage($key, $message) {
        $translated = '';
        $charsA = $key;
        $charsB = self::letters;

        foreach (str_split($message) as $i => $symbol) {
            if (in_array(strtoupper($symbol), str_split($charsA))) {
                $symIndex = array_search(strtoupper($symbol), str_split($charsA));
                if (strtoupper($symbol) == $symbol) {
                    $translated .= strtoupper($charsB[$symIndex]);
                } else {
                    $translated .= strtolower($charsB[$symIndex]);
                }
            } else {
                $translated .= $symbol;
            }
        }
        return $translated;
    }

    /**
     * Generates a unique array 
     */
    private function _createCipherWordList() {
        $this->cipherWordList = preg_replace("/[\s]/", " ", preg_replace(self::nonLettersOrSpacePattern, ' ', strtoupper($this->text)));
        $this->cipherWordList = explode(" ", $this->cipherWordList);
        $this->cipherWordList = array_filter($this->cipherWordList, 'trim');
        $this->cipherWordList = array_filter($this->cipherWordList, 'strlen');
        $this->cipherWordList = array_unique($this->cipherWordList, SORT_STRING);
    }

    private function _addLettersToMapping($letterMapping, $cipherWord, $word) {
        for ($i = 0; $i < strlen($cipherWord); $i++) {
            if (!in_array($word[$i], $letterMapping[$cipherWord[$i]])) {
                $letterMapping[$cipherWord[$i]][] = $word[$i];
            }
        }
        return $letterMapping;
    }

    private function _intersectMappings($mapA, $mapB) {
        $intersectedMapping = self::getBlankCipherletterMapping();
        $letterMatches = [];
        foreach (str_split(self::letters) as $letter) {
            $letterMatches[$letter] = [];
            if (count($mapA[$letter]) == 0) {
                $intersectedMapping[$letter] = $mapB[$letter];
            } else if (count($mapB[$letter]) == 0) {
                $intersectedMapping[$letter] = $mapA[$letter];
            } else {
                foreach ($mapA[$letter] as $mappedLetter) {
                    if (in_array($mappedLetter, $mapB[$letter])) {
                        $intersectedMapping[$letter][] = $mappedLetter;
                        $letterMatches[$letter][] = $mappedLetter;
                    }
                }
            }
        }
        event(new DecryptLetterMatches($letterMatches));
        return $intersectedMapping;
    }

    private function _removeSolvedLettersFromMapping($letterMapping) {
        $loopAgain = true;
        while ($loopAgain) {
            $loopAgain = false;
            $solvedLetters = [];
            foreach (str_split(self::letters) as $cipherLetter) {
                if (count($letterMapping[$cipherLetter]) == 1) {
                    $letterMapping[$cipherLetter] = array_values($letterMapping[$cipherLetter]);
                    $solvedLetters[] = $letterMapping[$cipherLetter][0];
                }
            }
            foreach (str_split(self::letters) as $cipherLetter) {
                foreach ($solvedLetters as $s) {
                    if (count($letterMapping[$cipherLetter]) != 1 && in_array($s, $letterMapping[$cipherLetter])) {
                        if (($key = array_search($s, $letterMapping[$cipherLetter])) !== false) {
                            unset($letterMapping[$cipherLetter][$key]);
                        }
                        if (count($letterMapping[$cipherLetter]) == 1) {
                            $loopAgain = true;
                        }
                    }
                }
            }
        }
        $letterMatches = [];
        foreach (str_split(self::letters) as $cipherLetter) {
            $letterMatches[$cipherLetter] = $letterMapping[$cipherLetter];
        }
        event(new DecryptLetterMatches($letterMatches));
        return $letterMapping;
    }


    public static function getBlankCipherletterMapping() {
        return [
            'A' => [], 'B' => [], 'C' => [], 'D' => [], 'E' => [], 'F' => [],
            'G' => [], 'H' => [], 'I' => [], 'J' => [], 'K' => [], 'L' => [],
            'M' => [], 'N' => [], 'O' => [], 'P' => [], 'Q' => [], 'R' => [],
            'S' => [], 'T' => [], 'U' => [], 'V' => [], 'W' => [], 'X' => [],
            'Y' => [], 'Z' => []
        ];
    }


}
