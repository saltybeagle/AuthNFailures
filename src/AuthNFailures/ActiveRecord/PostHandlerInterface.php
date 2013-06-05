<?php
namespace AuthNFailures\ActiveRecord;

interface PostHandlerInterface
{
    public function handlePost($options = array(), $post = array(), $files = array());
}
