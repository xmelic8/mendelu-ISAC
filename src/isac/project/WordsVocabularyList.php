<?php

namespace Isac\Project;

class WordsVocabularyList
{
    CONST POSITIVE_FILE = true;
    CONST NEGATIVE_FILE = false;

    /**
     * FILE FROM https://www.enchantedlearning.com/wordlist/positivewords.shtml?fbclid=IwAR2pWK4cVjRsipIF4IMZr9-PI8CfVeloWD9UptzcHTg2dPu30w8enLAnnu8
     */
    CONST SOURCE_POSITIVE = "./source/positive.txt";

    /**
     * FILE FORM https://www.enchantedlearning.com/wordlist/negativewords.shtml?fbclid=IwAR3DUY1arxR8kQXzLh6f_Z4Az53Y1kBfBZhq2GzKNpq5Kk3kW7A_kW-WcRg
     */
    CONST SOURCE_NEGATIVE = "./source/negative.txt";

    CONST REGULAR_PATTERN = "/[ ,.?!-:;\\n\\r\\t…_]/u";

    private $positive;
    private $negative;

    private $positiveCount;
    private $negativeCount;

    /**
     * WordsVocabularyList constructor.
     */
    public function __construct()
    {
        $this->positiveCount = 0;
        $this->negativeCount = 0;
        $this->positive = $this->createWords(self::POSITIVE_FILE);
        $this->negative = $this->createWords(self::NEGATIVE_FILE);
    }

    /**
     * @param bool $type
     * @return array
     */
    private function createWords(bool $type) :array {
        if($type === self::POSITIVE_FILE){
            $file = fopen(self::SOURCE_POSITIVE, "r");
        }else{
            $file = fopen(self::SOURCE_NEGATIVE, "r");
        }

        if ($file) {
            $i = 0;
            $tmpArray = array();

            while(!feof($file)) {
                $line = fgets($file);
                $tmp = $this->parseStringToArray($line);
                $tmpArray = array_merge($tmpArray, $tmp);

                if(count($tmp)) {
                    $i++;
                }
            }

            if($type === self::POSITIVE_FILE){
                $this->positiveCount = $i;
            }else{
                $this->negativeCount = $i;
            }

            fclose($file);
        }

        return $tmpArray;
    }

    /**
     * @return array
     */
    public function getPositive() :array
    {
        return $this->positive;
    }

    /**
     * @return array
     */
    public function getNegative() :array
    {
        return $this->negative;
    }

    /**
     * @return int
     */
    public function getPositiveCount(): int
    {
        return $this->positiveCount;
    }

    /**
     * @return int
     */
    public function getNegativeCount(): int
    {
        return $this->negativeCount;
    }

    /**
     * @param $data
     * @return array
     */
    public function parseStringToArray($data) :array {
        $tmp = mb_strtolower($data, 'utf8');
        $tmp = preg_split( self::REGULAR_PATTERN, $tmp);
        $tmp = array_filter($tmp, "trim");

        return array_values($tmp);
    }
}