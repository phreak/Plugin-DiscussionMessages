<?php if(!defined('APPLICATION')) exit();
/* Copyright 2014 Zachary Doll */

echo Wrap($this->Data('Title'), 'h1');

echo $this->Form->Open();
echo $this->Form->Errors();
?>
<ul>
  <li>
    <?php
    echo $this->Form->Label('Insert Name', 'Name');
    ?>
    <span style="color: gray;">For internal reference</span>
    <?php    
    echo $this->Form->TextBox('Name');
    ?>
  </li>
  <li>
    <?php
    echo $this->Form->Label('Insert', 'Body');
    ?>
    <span style="color: gray;">Plain Text and HTML</span>
	<?php        
    echo $this->Form->TextBox('Body', array('multiline' => TRUE));
    ?>
  </li>
  <li id="MobileBodyCheck">
    <?php
    echo $this->Form->CheckBox('MobileBodyCheck', 'Use a separate insert for Mobile users?', array('checked' => 'checked'));
    ?>
    <span style="color: gray;">If not checked the above insert will also be shown on the mobile theme.</span>    
  </li>
  <li id="MobileBodyRow">
    <?php
    echo $this->Form->Label('Mobile Insert', 'MobileBody');
    echo $this->Form->TextBox('MobileBody', array('multiline' => TRUE));
    ?>
  </li>
  <li>
    <?php
    echo $this->Form->Label('Discussion ID', 'DiscussionID');
    ?>
    <span style="color: gray;">Find the ID in the discussion URL (www.yourforum.com/discussion/ID/title...)</span>     
    <?php 
    echo $this->Form->TextBox('DiscussionID');
    ?>
  </li>
</ul>
<?php
echo $this->Form->Close('Save');
