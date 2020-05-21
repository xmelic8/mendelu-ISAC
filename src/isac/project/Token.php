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

    /**
     * Token constructor.
     * @param int $id
     * @param string $text
     */
   public function __construct(int $id, string $text)
   {
       $this->id = $id;
       $this->text = $text;
       $this->count = array();
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
}