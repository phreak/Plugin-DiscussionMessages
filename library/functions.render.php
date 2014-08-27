<?php if(!defined('APPLICATION')) exit();
/* Copyright 2014 Zachary Doll */

if(!function_exists('RenderDiscussionInserts')) {

  function RenderDiscussionInserts($Inserts) {
    $String = '';
    foreach($Inserts as $Insert) {
      $String .= RenderDiscussionInsert($Insert);
    }
    return $String;
  }

}

if(!function_exists('RenderDiscussionInsert')) {

  function RenderDiscussionInsert($Insert) {
    $Body = $Insert->Body;
    if(IsMobile() && !empty($Insert->MobileBody)) {
      $Body = $Insert->MobileBody;
    }
    
    return Wrap(
          DMTools($Insert) .
          Gdn_Format::Html($Body),
          'div',
          array(
            'class' => 'DiscussionInsert',
            'id' => 'DiscussionInsert_' . $Insert->DiscussionInsertID
    ));
  }

}

if(!function_exists('DMTools')) {

  function DMTools($Insert) {
    if(Gdn::Session()->CheckPermission('Plugins.DiscussionInserts.Manage')) {
      return Wrap(
              Wrap(Anchor('Edit', 'settings/discussioninserts/edit/' . $Insert->DiscussionInsertID, 'Popup'), 'li') .
              Wrap(Anchor('Delete', 'settings/discussioninserts/delete/' . $Insert->DiscussionInsertID, 'Popup'), 'li')
              , 'ul', array('class' => 'Tools')
      );
    }
  }

}