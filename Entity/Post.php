<?php

namespace Core\Bundle\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Core\Bundle\CoreBundle\Entity\Actor;
use Core\Bundle\BlogBundle\Entity\Image;
use Core\Bundle\BlogBundle\Entity\Category;
use Core\Bundle\BlogBundle\Entity\Comment;
use Core\Bundle\CoreBundle\Entity\Project;
use Core\Bundle\CoreBundle\Entity\Timestampable;

/**
 * @ORM\Entity(repositoryClass="Core\Bundle\BlogBundle\Entity\Repository\PostRepository")
 * @ORM\Table(name="post")
 */
class Post  extends Timestampable
{

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="title", type="string", length=140)
     */
    protected $title;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Image", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="post_images",
     *                      joinColumns={@ORM\JoinColumn(name="post_id", referencedColumnName="id")},
     *                      inverseJoinColumns={@ORM\JoinColumn(name="image_id", referencedColumnName="id")})
     */
    private $images;
    
    /**
     * @Gedmo\Slug(fields={"title"}, updatable=true, separator="-")
     * @ORM\Column(name="slug", type="string", length=140)
     */
    protected $slug;

    /**
     * @ORM\ManyToOne(targetEntity="\Core\Bundle\CoreBundle\Entity\Actor", inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $actor;

    /**
     * @ORM\ManyToMany(targetEntity="Category", inversedBy="posts")
     * @ORM\JoinTable(name="posts_categories",
     *      joinColumns={@ORM\JoinColumn(name="post_id", referencedColumnName="id", nullable=true)},
     *      inverseJoinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="id")}
     *      )
     */
    protected $categories;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="post")
     */
    protected $comments;

    /**
     * @ORM\Column(name="published", type="datetime")
     */
    private $published;

    /**
     * @ORM\ManyToOne(targetEntity="\Core\Bundle\CoreBundle\Entity\Project", inversedBy="posts")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $project;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }
    
    /**
     * Returns the name
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param  string $title
     * @return Post
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param text $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
        
        return $this;
    }

    /**
     * Get description
     *
     * @return text
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * Add image
     *
     * @param Image $image
     *
     * @return Post
     */
    public function addImage(Image $image)
    {
        $this->images->add($image);

        return $this;
    }

    /**
     * Remove image
     *
     * @param Image $image
     */
    public function removeImage(Image $image)
    {
        $this->images->removeElement($image);
    }

    /**
     * Get images
     *
     * @return ArrayCollection
     */
    public function getImages()
    {
        return $this->images;
    }
    
    /**
     * Set slug
     *
     * @param  string $slug
     * @return Post
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
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
     * Add category
     *
     * @param Category $category
     *
     * @return Post
     */
    public function addCategory(Category $category)
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param Category $category
     */
    public function removeCategory(Category $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add comment
     *
     * @param Comment $comment
     *
     * @return Post
     */
    public function addComment(Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param Comment $comment
     */
    public function removeComment(Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Get published
     *
     * @return datetime
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set published
     *
     * @param \DateTime $published
     *
     * @return Post
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }
    
    /**
     * Set project
     *
     * @param  Project $project
     * @return Post
     */
    public function setProject(Project $project)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }
    
}
