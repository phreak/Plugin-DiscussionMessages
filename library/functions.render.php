<?php if(!defined('APPLICATION')) exit();
/* Copyright 2014 Zachary Doll */

if(!function_exists('RenderDiscussionMessages')) {

  function RenderDiscussionMessages($Messages) {
    foreach($Messages as $Message) {
      echo Wrap(
              RenderDMTools($Message) . 
              Gdn_Format::Html($Message->Body),
              'div',
              array(
                  'class' => 'DiscussionMessage',
                  'id' => 'DiscussionMessage_' . $Message->DiscussionMessageID
              ));
    }
  }

}


if(!function_exists('RenderDMTools')) {
  
  function RenderDMTools($Message) {
    if(Gdn::Session()->CheckPermission('Plugins.DiscussionMessages.Manage')) {
      echo Wrap(
            Wrap(Anchor('Edit', 'settings/discussionmessages/edit/' . $Message->DiscussionMessageID), 'li', array('class' => 'Popup')) .
            Wrap(Anchor('Delete', 'settings/discussionmessages/delete/' . $Message->DiscussionMessageID), 'li', array('class' => 'Popup'))
            ,
            'ul',
            array('class' => 'Tools')
            );
    }
  }
}