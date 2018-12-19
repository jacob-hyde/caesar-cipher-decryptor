<?php

namespace App\Classes;

class WordPatterns {

    public static function createWordPatterns($publicKey = null) {
        $dicionaryPath = storage_path('app/dictionary.txt');
        $dictionary = file_get_contents($dicionaryPath);
        $dictionary = preg_split("/\\r\\n|\\r|\\n/", $dictionary);

        if ($publicKey !== null) {
            $publicKey = preg_split("/\\r\\n|\\r|\\n/", $publicKey);
            if ($publicKey) {
                $i = 0;
                foreach($publicKey as $line) {
                    $i++;
                    $line = strtoupper($line);
                    preg_match_all("/([^\"\-\/\.\(\)\]\[\:\,0-9\?\;\@\~\*\#\<\>\{\}\%\$\_\^\&\\n\! ][A-Za-z\']*)/", $line, $words);
                    $words = $words[0];
                    foreach ($words as $word) {
                        $word = trim($word);
                        if (strlen($word) <= 1) {
                            continue;
                        }
                        $word = explode('\'', $word);
                        if (count($word) === 2) {
                            array_push($dictionary, $word[0].$word[1]);
                        } else {
                            array_push($dictionary, $word[0]);
                        }
                    }
                }
            }
        }

        //add one letter words
        array_merge($dictionary, ['A', 'I', 'O']);
        //add common used words
        array_merge($dictionary, ['FOR', 'WAS', 'HIS', 'THAT']);
        $dictionary = array_unique($dictionary, SORT_STRING);
        $allPatterns = [];
        foreach ($dictionary as $word) {
            $pattern = self::getWordPattern(trim($word));
            if ($pattern === "") continue;
            if (!isset($allPatterns[$pattern])) {
                $allPatterns[$pattern] = [$word];
            } else {
                array_push($allPatterns[$pattern], $word);
            }
        }
        return $allPatterns;
    }

    public static function getWordPattern($word) {
        if (empty($word)) return "";
        $word = strtoupper($word);
        $nextNum = 0;
        $letterNums = [];
        $wordPattern = [];

        foreach (str_split($word) as $letter) {
            if (!isset($letterNums[$letter])) {
                $letterNums[$letter] = (string)$nextNum;
                $nextNum++;
            }
            array_push($wordPattern, $letterNums[$letter]);
        }
        return implode('.', $wordPattern);
    }
}
