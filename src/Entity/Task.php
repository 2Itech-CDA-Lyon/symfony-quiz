<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\QuizRepository;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class Task
{

 /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $description;

    /**
     * @ORM\Column(type="integer")
     */
    protected $difficulty;


    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="quizzes")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity=Question::class, mappedBy="quiz", orphanRemoval=true)
     */
    private $questions;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
    
    public function getDifficulty(): int
    {
        return $this->difficulty;
    }

    public function setDifficulty(int $difficulty): void
    {
        $this->difficulty = $difficulty;
    }
}

?>