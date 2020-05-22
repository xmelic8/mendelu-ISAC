<?php

namespace Isac\Project;

class Token
{
    /**
     * @var string
     */
   private $text;

    /**
     * @var int
     */
   private $id;

    /**
     * @var array
     */
   private $count;

   private $airline_sentiment;
   private $airline_sentiment_confidence;
   private $negativereason;
   private $negativereason_confidence;
   private $mySentiment;

    /**
     * Token constructor.
     * @param int $id
     * @param string $text
     */
   public function __construct(int $id, string $text, $airline_sentiment, $airline_sentiment_confidence, $negativereason, $negativereason_confidence)
   {
       $this->id = $id;
       $this->text = $text;
       $this->count = array();
       $this->airline_sentiment = $this->transferValue($airline_sentiment, "S");
       $this->airline_sentiment_confidence = $this->transferValue($airline_sentiment_confidence, "N");
       $this->negativereason = $this->transferValue($negativereason, "S");
       $this->negativereason_confidence = $this->transferValue($negativereason_confidence, "N");
       $this->mySentiment = null;
   }


    public function getMySentiment()
    {
        return $this->mySentiment;
    }

    /**
     * @param null $mySentiment
     */
    public function setMySentiment($mySentiment): void
    {
        $this->mySentiment = $mySentiment;
    }


    public function getAirlineSentiment()
    {
        return $this->airline_sentiment;
    }


    /**
     * @return string
     */
    public function getText() :string
    {
        return $this->text;
    }

    /**
     * @return int
     */
    public function getId() :int
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getCount(): array
    {
        return $this->count;
    }

    /**
     * @param array $count
     */
    public function setCount(array $count): void
    {
        $this->count = $count;
    }

    /**
     * @param $value
     * @param $type
     * @return float|null
     */
    private function transferValue($value, $type){
        switch ($type){
            case "N":
                $tmp = floatval($value);
                if(is_nan($tmp)){
                    return null;
                }

                return $tmp;
            case "S":
                if($value === ""){
                    return null;
                }

                return $value;
            default:
                return null;
        }
    }
}