<?php
namespace Core\Bundle\BlogBundle\DataFixtures\ORM;

use Core\Bundle\CoreBundle\DataFixtures\SqlScriptFixture;
use Core\Bundle\BlogBundle\Entity\Category;
use Core\Bundle\CoreBundle\Entity\Actor;
use Core\Bundle\BlogBundle\Entity\Comment;
use Core\Bundle\BlogBundle\Entity\Post;
use Core\Bundle\BlogBundle\Entity\Image;

class LoadBlogData extends SqlScriptFixture
{
    public function createFixtures()
    {
         
        $core = $this->container->getParameter('core');
        if($core['fixture_data'])
        {
           
            //Create Category
            $category = new Category();
            $category->setName('Ciencia natural');
            $category->setDescription('Ciencia natural');

            $category2 = new Category();
            $category2->setName('Farmacología');
            $category2->setDescription('Farmacología');

            $category3 = new Category();
            $category3->setName('Nuevos avances');
            $category3->setDescription('Nuevos avances');

            $category4 = new Category();
            $category4->setName('Robótica');
            $category4->setDescription('Robótica');

            $category5 = new Category();
            $category5->setName('Nanotecnología');
            $category5->setDescription('Nanotecnología');

            $category6 = new Category();
            $category6->setName('Actualidad');
            $category6->setDescription('actualidad');

            //Create Comment
            //User maria
            $userRole = $this->getManager()->getRepository('CoreBundle:Role')->findOneByName('user');
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder(new Actor());
            $user = new Actor();
            $user->setUsername('maria');
            $user->setEmail('maria@email.com');
            $encodePassword = $encoder->encodePassword('maria', $user->getSalt());
            $user->setPassword($encodePassword);
            $user->setRawPassword('maria');
            $user->addRole($userRole);
            $user->setName('Maria');
            $user->setAccountType(Actor::INDIVIDUAL);
            $user->setSurnames('Stradivarius');

            $comment = new Comment();
            $comment->setActor($user);
            $comment->setComment('Cras quis velit pulvinar, semper tellus eu, bibendum libero. Aliquam nec feugiat ante. Vestibulum a vehicula dui, eu blandit lacus. Phasellus eu turpis in lectus ullamcorper luctus non ut lorem. Maecenas commodo mauris metus, quis congue sapien euismod vitae. In hac habitasse platea dictumst. Aenean dignissim, orci sollicitudin volutpat ullamcorper, risus elit suscipit mauris, non fermentum felis nibh et purus. Morbi id porta ligula.');
            $comment->setIsActive(true);



            //Create Post
            $actor = $this->getManager()->getRepository('CoreBundle:Actor')->findOneByUsername('test');

            $post = new Post();
            $post->setTitle('Nueva colección a cargo de Bob Sdrunk!');
            $post->setDescription('<p>Quisque venenatis et orci non pretium. Nunc pellentesque suscipit lorem, non volutpat ex mattis id. Vivamus dictum dolor metus. Aliquam erat volutpat. Morbi quis ante dui. Maecenas a hendrerit ante. Sed commodo, metus sit amet vulputate mattis, tortor ligula mattis turpis, quis tempus sem augue in augue. Nunc sollicitudin vel mauris eu aliquet. Donec in dui rutrum, suscipit lacus sit amet, consequat quam. Morbi sit amet faucibus nunc. Sed sagittis sit amet mi nec feugiat. Curabitur est odio, imperdiet eu scelerisque et, sodales quis magna. Duis vitae lectus sem. In posuere scelerisque ante, nec pellentesque neque ornare quis. Curabitur pretium rhoncus purus.</p>
                        <p>Cras quis velit pulvinar, semper tellus eu, bibendum libero. Aliquam nec feugiat ante. Vestibulum a vehicula dui, eu blandit lacus. Phasellus eu turpis in lectus ullamcorper luctus non ut lorem. Maecenas commodo mauris metus, quis congue sapien euismod vitae. In hac habitasse platea dictumst. Aenean dignissim, orci sollicitudin volutpat ullamcorper, risus elit suscipit mauris, non fermentum felis nibh et purus. Morbi id porta ligula.</p>
                        <p>Phasellus fringilla dui non libero ornare varius. Praesent nec justo nec dolor luctus porttitor. Vestibulum a tristique lacus. Curabitur facilisis lectus at lacus rutrum consequat. Nam eu turpis nec urna tincidunt tincidunt id sed quam. Morbi nunc urna, tristique at consequat eget, porttitor non metus. Pellentesque tortor massa, condimentum nec tristique sit amet, accumsan eget eros.</p>
                        <p>Phasellus ac urna condimentum massa egestas congue. Mauris fringilla rutrum cursus. Quisque posuere nisl iaculis elit aliquet, efficitur feugiat neque scelerisque. Nam luctus, diam ut pulvinar elementum, lacus mauris imperdiet lectus, id maximus nibh quam in quam. Integer molestie erat pulvinar vehicula placerat. Fusce tincidunt, nisi sit amet posuere aliquam, lorem urna lacinia dolor, nec venenatis nunc magna sed dui. Nam non lorem ac augue efficitur elementum non nec sem. Donec eget arcu elit. Duis erat neque, gravida nec porta a, efficitur ac nunc. Nunc dignissim diam in porta pulvinar. Nulla at rhoncus diam. Maecenas consectetur viverra mi, eget hendrerit sapien consequat eu. Sed lacinia leo sed dolor dictum fringilla. Sed sit amet imperdiet elit, nec dignissim odio.</p>
                        <p>Duis quam dui, consectetur non diam a, blandit sagittis metus. Nullam fermentum accumsan mollis. In hac habitasse platea dictumst. Sed condimentum, ante porttitor scelerisque elementum, metus sem posuere turpis, in tincidunt nunc lacus a ipsum. Ut ornare convallis est, nec fringilla turpis. Aliquam odio lorem, convallis ut urna eget, lobortis viverra lacus. Nulla quis ex at odio iaculis vulputate nec vel sapien. Duis ornare sem id malesuada aliquam. Suspendisse varius tempor purus, ac suscipit nisl feugiat a. Duis convallis velit vitae suscipit accumsan. Ut aliquet aliquet tempor. Cras nisi sem, venenatis vel elementum id, convallis nec lorem. Duis lacinia viverra tincidunt. Aliquam eleifend erat ut diam sagittis, in ullamcorper est aliquet.</p>');
            $post->setActor($actor);
            $post->setPublished(new \DateTime('now'));
            $post->addComment($comment);
            $comment->setPost($post);

            $this->getManager()->persist($category);
            $this->getManager()->persist($category2);
            $this->getManager()->persist($category3);
            $this->getManager()->persist($category4);
            $this->getManager()->persist($category5);
            $this->getManager()->persist($category6);
            $this->getManager()->persist($user);
            $this->getManager()->persist($comment);
            $this->getManager()->persist($post);


            $this->getManager()->flush();

            //Create Image
            $image = new Image();
            $image->setPath('post1.jpg');
            $filename =  __DIR__ . '/../../Resources/public/images/post1.jpg' ;
            @mkdir(__DIR__ . '/../../../../../../web/uploads/images/posts/'.$post->getId());
            @mkdir(__DIR__ . '/../../../../../../web/uploads/images/posts/'.$post->getId().'/thumbnail');
            copy($filename, __DIR__ . '/../../../../../../web/uploads/images/posts/'.$post->getId().'/post1.jpg' );
            copy($filename, __DIR__ . '/../../../../../../web/uploads/images/posts/'.$post->getId().'/thumbnail/post1.jpg' );
            $post->addImage($image);
            $this->getManager()->persist($image);

            $this->getManager()->flush();
        
        } 
        
    }

    public function getOrder()
    {
        return 2; // the order in which fixtures will be loaded
    }
}
