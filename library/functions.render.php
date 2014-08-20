<?php if(!defined('APPLICATION')) exit();
/* Copyright 2014 Zachary Doll */

if(!function_exists('RenderDiscussionMessages')) {

  function RenderDiscussionMessages($Messages) {
    $String = '';
    foreach($Messages as $Message) {
      $String .= RenderDiscussionMessage($Message);
    }
    return $String;
  }

}

if(!function_exists('RenderDiscussionMessage')) {

  function RenderDiscussionMessage($Message) {
    $Body = $Message->Body;
    if(IsMobile() && !empty($Message->MobileBody)) {
      $Body = $Message->MobileBody;
    }
    
    return Wrap(
          DMTools($Message) .
          Gdn_Format::Html($Body),
          'div',
          array(
            'class' => 'DiscussionMessage',
            'id' => 'DiscussionMessage_' . $Message->DiscussionMessageID
    ));
  }

}

if(!function_exists('DMTools')) {

  function DMTools($Message) {
    if(Gdn::Session()->CheckPermission('Plugins.DiscussionMessages.Manage')) {
      return Wrap(
              Wrap(Anchor('Edit', 'settings/discussionmessages/edit/' . $Message->DiscussionMessageID, 'Popup'), 'li') .
              Wrap(Anchor('Delete', 'settings/discussionmessages/delete/' . $Message->DiscussionMessageID, 'Popup'), 'li')
              , 'ul', array('class' => 'Tools')
      );
    }
  }

}