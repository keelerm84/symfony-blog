<?php

namespace Koios\BlogBundle\Services;

use \Guzzle\Service\Client;
use \Koios\BlogBundle\Services\BlogClientInterface;

class GuzzleBlogClient implements BlogClientInterface {
    protected $client = null;
    protected $username = false;
    protected $password = false;

    public function __construct(Client $client) {
        $this->client = $client;
    }

    public function getBlog($id) {
	  $command = $this->client->getCommand('GetBlog', array('id' => $id));
	  $blog = $this->client->execute($command);

      return array('blog' => $blog, 'lastModified' => $command->getResponse()->getLastModified());
    }

    public function getBlogs() {
        $command = $this->client->getCommand('GetBlogs');
        $blogs = $this->client->execute($command);

        return array('blogs' => $blogs, 'lastModified' => $command->getResponse()->getLastModified());
    }

    public function createBlog(array $data) {
        $command = $this->client->getCommand('CreateBlog', array_merge($data, $this->generateAuthenticationHeaders()));
        $this->client->execute($command);
    }

    public function deleteBlogs(array $ids) {
        $command = $this->client->getCommand("DeleteBlogs", array_merge(array('blogs' => $ids), $this->generateAuthenticationHeaders()));
        $this->client->execute($command);
    }

    public function editBlog($id, array $data) {
        $command = $this->client->getCommand('EditBlog', array_merge(array(
                       'id'      => $id,
                       'blog'    => $data['blog'],
                       'title'   => $data['title'],
                       'tags'    => $data['tags']
                       ), $this->generateAuthenticationHeaders()));
        $this->client->execute($command);
    }

    public function getBlogComments($id) {
        $command = $this->client->getCommand('GetBlogComments', array('id' => $id));
        $comments = $this->client->execute($command);

        return array('comments' => $comments, 'lastModified' => $command->getResponse()->getLastModified());
    }

    public function getComments($limit = 10) {
        $command = $this->client->getCommand('GetComments', array('limit' => $limit));
        $comments = $this->client->execute($command);

        return array('comments' => $comments, 'lastModified' => $command->getResponse()->getLastModified());
    }

    public function createComment($user, $comment, $id) {
        $command = $this->client->getCommand('CreateComment', array('user' => $user, 'comment' => $comment, 'id' => $id));
        $this->client->execute($command);
    }

    public function getTagWeights() {
        $command = $this->client->getCommand('GetTagWeights');
        $tagWeights = $this->client->execute($command);

        return array('weights' => $tagWeights, 'lastModified' => $command->getResponse()->getLastModified());
    }

    public function setAuthentication($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }

    public function clearAuthentication() {
        $this->username = false;
        $this->password = false;
    }

    protected function generateAuthenticationHeaders() {
        return array('headers' => array('Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password)));
    }
}
