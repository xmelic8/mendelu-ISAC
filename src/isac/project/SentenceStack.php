<?php

namespace Isac\Project;

include './src/isac/project/Token.php';

use Isac\Project\Token;

class SentenceStack
{
    CONST BASE_DIR = "./source/";

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var array
     */
    private $queue;

    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
        $this->queue = array();
        $this->createQueue();
    }

    private function createQueue() :void {
        if ($file = fopen(self::BASE_DIR.$this->fileName, "r")) {
            $i = 1;
            $oneRecord = 0;
            $tmpNewRecord = "";

            while(!feof($file)) {
                $line = fgets($file);
                if(trim($line) === "||||"){
                    continue;
                }

                $oneRecord++;
                $tmpNewRecord = $tmpNewRecord.(str_replace("\n", "&&&&",$line));

                if($oneRecord === 5){
                    $tmpNewRecord = mb_substr($tmpNewRecord, 0, -4);

                    $tmpArray = explode("&&&&", $tmpNewRecord);
                    $this->queue[] = new Token($i++, $tmpArray[4], $tmpArray[0], $tmpArray[1], $tmpArray[2], $tmpArray[3]);

                    $oneRecord = 0;
                    $tmpNewRecord = "";
                }
            }

            fclose($file);
        }
    }

    /**
     * @return array
     */
    public function getQueue(): array
    {
        return $this->queue;
    }
}