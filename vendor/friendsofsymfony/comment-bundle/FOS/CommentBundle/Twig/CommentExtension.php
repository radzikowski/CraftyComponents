<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Twig;

use FOS\CommentBundle\Acl\CommentAclInterface;
use FOS\CommentBundle\Model\CommentInterface;
use FOS\CommentBundle\Model\VotableCommentInterface;

/**
 * Extends Twig to provide some helper functions for the CommentBundle.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class CommentExtension extends \Twig_Extension
{
    protected $commentAcl;

    public function __construct(CommentAclInterface $commentAcl = null)
    {
        $this->commentAcl = $commentAcl;
    }

    public function getTests()
    {
        return array(
            'fos_comment_votable'        => new \Twig_Test_Method($this, 'isVotable'),
        );
    }

    /**
     * Checks if the comment is an instance of a VotableCommentInterface.
     *
     * @param mixed The value to check for VotableCommentInterface
     * @return bool If $value implements VotableCommentInterface
     */
    public function isVotable($value)
    {
        if (!is_object($value)) {
            return false;
        }

        return ($value instanceof VotableCommentInterface);
    }

    public function getFunctions()
    {
        return array(
            'fos_comment_can_comment' => new \Twig_Function_Method($this, 'canComment'),
        );
    }

    /*
     * Checks if the current user is able to comment. Checks if they
     * can create root comments if no $comment is provided, otherwise
     * checks if they can reply to a given comment if supplied.
     *
     * @param CommentInterface|null $comment
     * @return bool If the user is able to comment
     */
    public function canComment(CommentInterface $comment = null)
    {
        if (null === $this->commentAcl) {
            return true;
        }

        if (null === $comment) {
            return $this->commentAcl->canCreate();
        }

        return $this->commentAcl->canReply($comment);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'fos_comment';
    }
}