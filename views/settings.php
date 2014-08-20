<?php if (!defined('APPLICATION')) exit();
/* Copyright 2014 Zachary Doll */

echo Wrap($this->Data('Title'), 'h1');

echo $this->Form->Open();
echo $this->Form->Errors();

echo Wrap(Wrap(T('Plugin.DiscussionMessages.Settings.Desc'), 'div'), 'div', array('class' => 'Wrap'));
echo Wrap(Anchor(T('Plugin.DiscussionMessages.Add'), 'settings/discussionmessages/add', array('class' => 'SmallButton')), 'div', array('class' => 'Wrap'));

?>
<table id="Actions" class="AltRows">
  <thead>
    <tr>
      <th><?php echo T('Name'); ?></th>
      <th><?php echo T('Discussion'); ?></th>
      <th><?php echo T('Body'); ?></th>
      <th><?php echo T('Mobile Body'); ?></th>
      <th><?php echo T('Options'); ?></th>
    </tr>
  </thead>
  <tbody>
    <?php
    $Alt = 'Alt';
    foreach($this->Data('DiscussionMessages') as $DiscussionMessage) {
      $Alt = $Alt ? '' : 'Alt';
      $Row = '';
      $Row .= Wrap(Gdn_Format::Html($DiscussionMessage->Name), 'td');
      $Row .= Wrap(Anchor(T('[Link]'), '/discussion/' . $DiscussionMessage->DiscussionID), 'td');
      $Row .= Wrap(Gdn_Format::Html($DiscussionMessage->Body), 'td');
      $Row .= Wrap(Gdn_Format::Html($DiscussionMessage->MobileBody), 'td');
      $Row .= Wrap(Anchor(T('Edit'), 'settings/discussionmessages/edit/' . $DiscussionMessage->DiscussionMessageID, array('class' => 'SmallButton')) . Anchor(T('Delete'), 'settings/discussionmessages/delete/' . $DiscussionMessage->DiscussionMessageID, array('class' => 'Danger Popup SmallButton')), 'td');
      echo Wrap($Row, 'tr', array('id' => 'DiscussionMessageID_' . $DiscussionMessage->DiscussionMessageID, 'data-discussionmessageid' => $DiscussionMessage->DiscussionMessageID, 'class' => $Alt));
    }
    ?>
  </tbody>
</table>
