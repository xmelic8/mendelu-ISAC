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

            while(!feof($file)) {
                $line = fgets($file);
                $this->queue[] = new Token($i, $line);
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