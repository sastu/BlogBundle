<?php
namespace Core\Bundle\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Core\Bundle\BlogBundle\Entity\Post;
use Core\Bundle\CoreBundle\Entity\Actor;

/**
 * @ORM\Entity(repositoryClass="Core\Bundle\BlogBundle\Entity\Repository\CommentRepository")
 * @ORM\Table(name="comment")
 */
class Comment extends Timestampable
{

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="\Core\Bundle\CoreBundle\Entity\Actor", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $actor;

    /**
     * @ORM\ManyToOne(targetEntity="Post", inversedBy="comments")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $post;

    /**
     * @ORM\Column(type="text")
     */
    protected $comment;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isActive=false;


    /**
     * Get id
     *
     * @return id
     */
    public function getId()
    {
        return $this->id;
    }

     /**
     * Set actor
     *
     * @param  Actor $actor
     * @return Post
     */
    public function setActor(Actor $actor)
    {
        $this->actor = $actor;

        return $this;
    }

    /**
     * Get actor
     *
     * @return Actor
     */
    public function getActor()
    {
        return $this->actor;
    }

    /**
     * Set comment
     *
     * @param text $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get comment
     *
     * @return text
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set post
     *
     * @param  Post $post
     * @return Comment
     */
    public function setPost(Post $post)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get post
     *
     * @return text
     */
    public function getPost()
    {
        return $this->post;
    }

    
     /**
     * Set isActive
     *
     * @return Comment
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->isActive;
    }
    
    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

}
