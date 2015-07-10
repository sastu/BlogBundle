<?php

namespace Core\Bundle\BlogBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class CommentFront
{
    /**
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @Assert\NotBlank()
     */
    protected $email;
    
    /**
     * @Assert\NotBlank()
     */
    protected $comment;

    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }
    
    public function setComment($comment)
    {
        $this->comment = $comment;
    }
    
    public function getComment()
    {
        return $this->comment;
    }

}
