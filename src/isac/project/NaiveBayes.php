<?php

namespace Isac\Project;

include './src/isac/project/SentenceStack.php';

use Isac\Project\SentenceStack;

class NaiveBayes
{
    const COUNT_ITEM_ZERO = 0;

    CONST MIN_POSITIVE = 0.4;
    CONST MAX_POSITIVE = 0.6;

    /**
     * @var WordsVocabularyList
     */
    private $wordsVocabularyList;

    /**
     * Submerged structure from description and attached words from training data.
     * @var array
     */
    private $dictionary;

    /**
     * List of all documents with the number of occurrences in the training data.
     * @var array
     */
    private $documentCounterForLabels;

    /**
     * List all words from documents
     * @var array
     */
    private $allTokens;

    /**
     * @var
     */
    private $queueInput;

    /**
     * NaiveBayes constructor.
     * @param WordsVocabularyList $wordsVocabularyList
     */
    public function __construct(WordsVocabularyList $wordsVocabularyList)
    {
        $this->wordsVocabularyList = $wordsVocabularyList;
        $this->dictionary = array();
        $this->documentCounterForLabels = array();
        $this->allTokens = array();
    }

    /**
     * @param $type
     */
    public function training($type) :void
    {
        if($type === $this->wordsVocabularyList::POSITIVE_FILE){
            $designationChar = "+";
            $sourceWords = $this->wordsVocabularyList->getPositive();
        }else{
            $designationChar = "-";
            $sourceWords = $this->wordsVocabularyList->getNegative();
        }

        $this->fillTrainingStructure($sourceWords, $designationChar);

        // echo "-----NEW-----\n";
        /*print_r($this->dictionary);
        print_r($this->allTokens);*/
       // print_r($this->documentCounterForLabels);
    }

    /**
     * @param array $sourceWords
     * @param string $designationChar
     */
    private function fillTrainingStructure(array $sourceWords, string $designationChar) :void{
        foreach ($sourceWords as $word) {
            $tokensWords = $this->wordsVocabularyList->parseStringToArray($word);

            if(!key_exists($designationChar, $this->documentCounterForLabels)){
                $this->dictionary[$designationChar] = array();
                if($designationChar === "+") {
                    $this->documentCounterForLabels[$designationChar] = $this->wordsVocabularyList->getPositiveCount();
                }else{
                    $this->documentCounterForLabels[$designationChar] = $this->wordsVocabularyList->getNegativeCount();
                }
            }

            //TODO v pripade, že by nebylo zadano jen slovo na radku, ale cela veta, proto foreach
            foreach ($tokensWords as $tokenWord) {
                if(!key_exists($designationChar, $this->dictionary) && !key_exists($tokenWord, $this->dictionary[$designationChar])) {
                    $this->dictionary[$designationChar][$tokenWord] = self::COUNT_ITEM_ZERO;
                }
                $this->dictionary[$designationChar][$tokenWord]++;

                if(!key_exists($tokenWord, $this->allTokens)){
                    $this->allTokens[$tokenWord] = self::COUNT_ITEM_ZERO;
                }
                $this->allTokens[$tokenWord]++;
            }
        }
    }

    /**
     * @param $fileNameSource
     */
    public function createClassification($fileNameSource) :void
    {
        $sentenceStack = new SentenceStack($fileNameSource);
        $this->queueInput = $sentenceStack->getQueue();

        for ($i = 0; $i < count($this->queueInput); $i++){
            $countArray = $this->classification($this->queueInput[$i], count($this->documentCounterForLabels));
            $this->queueInput[$i]->setCount($countArray);
            $this->queueInput[$i]->setMySentiment($this->howIsRating($countArray["+"]));
        }

       // echo "-----NEW-----\n";
       // print_r($this->queueInput);
    }

    /**
     * @param Token $lineObj
     * @param $totalDocumentCounterForLabels
     * @return array
     */
    private function classification(Token $lineObj, $totalDocumentCounterForLabels) :array {
        $tmpArray = array();
        $words =  $this->wordsVocabularyList->parseStringToArray($lineObj->getText());

        foreach ($this->documentCounterForLabels as $name => $docCount) {
            $resultLog = 0;
            
            $inversedDocCount = $totalDocumentCounterForLabels - $docCount;
            if ($inversedDocCount === 0){
                continue;
            }

            foreach ($words as $word) {
                $totalCountWord = key_exists($word, $this->allTokens) ? $this->allTokens[$word] : 0;
                if ($totalCountWord === 0) {
                    continue;
                }

                $wordProbabilityPositive = (isset($this->dictionary[$name][$word]) ? $this->dictionary[$name][$word] : 0) / $docCount;
                $wordProbabilityNegative = $this->countInversed($word, $name) / $inversedDocCount;
                $probabilityFinal = $wordProbabilityPositive / ($wordProbabilityNegative + $wordProbabilityPositive);
                $probabilityFinal = (($probabilityFinal * $totalCountWord) + (0.5 * 1)) / ($totalCountWord + 1);

                if($probabilityFinal === 0){
                    $probabilityFinal = 0.01;
                }elseif ($probabilityFinal === 1){
                    $probabilityFinal = 0.99;
                }

                $resultLog += log(1 - $probabilityFinal) - log($probabilityFinal);
            }

            $tmpArray[$name] = 1 / (1 + exp($resultLog));
        }

        arsort($tmpArray, SORT_NUMERIC);

        return $tmpArray;
    }

    /**
     * @param $word
     * @param $char
     * @return int|mixed
     */
    protected function countInversed($word, $char)
    {
        $totalCountWord = $this->allTokens[$word];
        $totalLabelTokenCount = isset($this->dictionary[$char][$word]) ? $this->dictionary[$char][$word] : 0;
        return $totalCountWord - $totalLabelTokenCount;
    }

    /**
     *
     */
    public function printResult() :void{
        foreach ($this->queueInput as $obj){
            echo("LINE:".$obj->getID().": P(".$obj->getCount()["+"].")/N(".$obj->getCount()["-"].") ----- ".$obj->getText()."\n");
        }
    }

    /**
     * @param float $valuePositive
     * @return string
     */
    private function howIsRating(float $valuePositive) :string{
        if($valuePositive <= self::MIN_POSITIVE){
            return "negative";
        }elseif($valuePositive >= self::MAX_POSITIVE){
            return "positive";
        }else{
            return "neutral";
        }
    }

    public function createStatistics(){
        $finalArray = [
            "newPositiove-oldPositive" => 0,
            "newNeutral-oldNeutral" => 0,
            "newNegative-oldNegative" => 0,
            "newPositiove-oldNeutral" => 0,
            "newPositiove-oldNegative" => 0,
            "newNeutral-oldPositive" => 0,
            "newNeutral-oldNegative" => 0,
            "newNegative-oldPositive" => 0,
            "newNegative-oldNeutral" => 0,
            "count" => 0
        ];

        foreach ($this->queueInput as $value){
           if($value->getAirlineSentiment() === "positive"){
                $nameValue = "-oldPositive";
           }elseif ($value->getAirlineSentiment() === "neutral"){
                $nameValue = "-oldNeutral";
           }elseif ($value->getAirlineSentiment() === "negative"){
                $nameValue = "-oldNegative";
           }else{
                continue;
           }

           switch ($value->getMySentiment()){
                case "positive":
                    $finalArray["newPositiove".$nameValue]++;
                    break;
                case "neutral":
                    $finalArray["newNeutral".$nameValue]++;
                    break;
                case "negative":
                    $finalArray["newNegative".$nameValue]++;
                    break;
                default:
                    break;
            }

           $finalArray["count"]++;
        }

        print_r($finalArray);
    }
}