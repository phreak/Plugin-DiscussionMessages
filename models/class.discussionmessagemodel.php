<?php if(!defined('APPLICATION')) exit();
/* Copyright 2014 Zachary Doll */
class DiscussionMessageModel extends Gdn_Model {

  public function __construct() {
    parent::__construct('DiscussionMessage');
  }
  
  public function GetDiscussionID($DiscussionID, $DatasetType = FALSE) {
    $Result = $this->GetWhere(array('DiscussionID' => $DiscussionID))->Result($DatasetType);
    return $Result;
  }
}
