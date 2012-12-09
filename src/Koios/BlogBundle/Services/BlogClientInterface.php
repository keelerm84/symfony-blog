<?php

namespace Koios\BlogBundle\Services;

interface BlogClientInterface {
    public function getBlog($id);
    public function getBlogs();
    public function createBlog(array $data);
    public function deleteBlogs(array $ids);
    public function editBlog($id, array $data);

    public function getBlogComments($id);
    public function getComments($limit = 10);
    public function createComment($user, $comment, $id);

    public function getTagWeights();

    public function setAuthentication($username, $password);
    public function clearAuthentication();
}
