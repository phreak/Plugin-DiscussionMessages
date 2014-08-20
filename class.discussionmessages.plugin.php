<?php if (!defined('APPLICATION')) exit();
/*	Copyright 2014 Zachary Doll
*	This program is free software: you can redistribute it and/or modify
*	it under the terms of the GNU General Public License as published by
*	the Free Software Foundation, either version 3 of the License, or
*	(at your option) any later version.
*
*	This program is distributed in the hope that it will be useful,
*	but WITHOUT ANY WARRANTY; without even the implied warranty of
*	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*	GNU General Public License for more details.
*
*	You should have received a copy of the GNU General Public License
*	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
$PluginInfo['DiscussionMessages'] = array(
	'Name' => 'Discussion Messages',
	'Description' => 'Adds messages to specific discussions.',
	'Version' => '0.1',
	'RequiredApplications' => array('Vanilla' => '2.0.18.13'),
	'RequiredTheme' => FALSE,
	'RequiredPlugins' => FALSE,
	'MobileFriendly' => TRUE,
	'HasLocale' => TRUE,
	'RegisterPermissions' => FALSE,
  'SettingsUrl' => '/settings/discussionmessages',
	'SettingsPermission' => 'Garden.Settings.Manage',
	'Author' => 'Zachary Doll',
	'AuthorEmail' => 'hgtonight@daklutz.com',
	'AuthorUrl' => 'http://www.daklutz.com',
	'License' => 'GPLv3'
);

class DiscussionMessages extends Gdn_Plugin {
  
  public function __construct() {
    parent::__construct();
    require_once($this->GetPluginFolder() . DS . 'library' . DS . 'functions.render.php');
  }

  public function Base_GetAppSettingsMenuItems_Handler($Sender) {
    $Menu = $Sender->EventArguments['SideMenu'];
    $Menu->AddLink('Appearance', T('Discussion Messages'), 'settings/discussionmessages', 'Garden.Settings.Manage');
  }
  
  public function DiscussionsController_DiscussionOptions_Handler($Sender) {
    $Discussion = $Sender->EventArguments['Discussion'];
    $Options =& $Sender->Options;
    $Options .= Wrap(
            Anchor(T('Add message'), 'discussion/messages/' . $Discussion->DiscussionID, array('class' => 'Popup')),
            'li');
  }
  
  public function DiscussionController_DiscussionOptions_Handler($Sender) {
    $Discussion = $Sender->EventArguments['Discussion'];
    $Options =& $Sender->EventArguments['DiscussionOptions'];
    $Options[] = array(
        'Label' => T('Add message'),
        'Url' => 'discussion/messages/' . $Discussion->DiscussionID,
        'Class' => 'Popup'
    );
  }
  
  public function DiscussionController_CommentOptions_Handler($Sender) {
    if(GetValue('Type', $Sender->EventArguments, FALSE) == 'Discussion') {
      $Discussion = $Sender->EventArguments['Discussion'];
      echo Wrap(
              Anchor(T('Add message'), 'discussion/messages/' . $Discussion->DiscussionID, array('class' => 'Popup')),
              'span');
    }
  }
  
  public function DiscussionController_Messages_Create($Sender) {
    $DiscussionID = GetValue(0,$Sender->RequestArgs,NULL);
    if(is_null($DiscussionID)) {
      Redirect('settings/discussionmessages/add');
    }
    $DiscussionMessageModel = new DiscussionMessageModel();
    $Sender->Form->SetModel($DiscussionMessageModel);
    $Sender->Form->AddHidden('DiscussionID', $DiscussionID);
    
    $Sender->Title(T('Add Discussion Message'));

    if($Sender->Form->IsPostBack() != FALSE) {
      $MessageID = $Sender->Form->Save();
      if($MessageID) {
        $Message = $DiscussionMessageModel->GetID($MessageID);
        $Sender->InformMessage(T('Discussion Message added successfully!'));
        $Sender->JsonTarget('.MessageList.Discussion', RenderDiscussionMessage($Message), 'After');
        $Sender->JsonTarget('#DiscussionMessage_' . $Message->DiscussionMessageID, NULL, 'Highlight');
      }
    }

    $Sender->Render($this->GetView('message.php'));
  }
  
	public function SettingsController_DiscussionMessages_Create($Sender) {
		$Sender->AddSideMenu('settings/discussionmessages');
		$this->Dispatch($Sender, $Sender->RequestArgs);
	}
  
  public function Controller_Index($Sender) {
    $Sender->Title($this->GetPluginName() . ' ' . T('Settings'));
    
    $DiscussionMessageModel = new DiscussionMessageModel();
    $DiscussionMessages = $DiscussionMessageModel->Get();
    
    $Sender->SetData('DiscussionMessages', $DiscussionMessages);
		$Sender->Render($this->GetView('settings.php'));
  }
  
  public function Controller_Add($Sender) {
    $this->Controller_Edit($Sender);
  }
	
  public function Controller_Edit($Sender) {
    $Sender->Permission('Garden.Settings.Manage');
    
    $DiscussionMessageModel = new DiscussionMessageModel();
    $Sender->Form->SetModel($DiscussionMessageModel);

    $Sender->Title(T('Add Discussion Message'));
    $Edit = FALSE;
    $MessageID = GetValue(1, $Sender->RequestArgs, FALSE);
    if($MessageID) {
      $Sender->DiscussionMessage = $DiscussionMessageModel->GetID($MessageID);
      $Sender->Form->AddHidden('DiscussionMessageID', $MessageID);
      $Edit = TRUE;
      $Sender->Title(T('Edit Discussion Message'));
    }

    if($Sender->Form->IsPostBack() == FALSE) {
      if(property_exists($Sender, 'DiscussionMessage')) {
        $Sender->Form->SetData($Sender->DiscussionMessage);
      }
    }
    else {
      $MessageID = $Sender->Form->Save();
      if($MessageID) {
        $Message = $DiscussionMessageModel->GetID($MessageID);
        if($Edit) {
          $Sender->JsonTarget('#DiscussionMessage_' . $Message->DiscussionMessageID, RenderDiscussionMessage($Message), 'Html');
          $Sender->JsonTarget('#DiscussionMessage_' . $Message->DiscussionMessageID, NULL, 'Highlight');
          $Sender->InformMessage(T('Discussion Message updated successfully!'));
        }
        else {
          $Sender->InformMessage(T('Discussion Message added successfully!'));
          if($Sender->DeliveryType() == DELIVERY_TYPE_ALL) {
            Redirect('/settings/discussionmessages');
          }
        }
      }
    }

    $Sender->Render($this->GetView('edit.php'));
  }
  
  public function Controller_Delete($Sender) {
    $DiscussionMessageModel = new DiscussionMessageModel();
    
    $MessageID = GetValue(1, $Sender->RequestArgs, FALSE);
    $DiscussionMessage = $DiscussionMessageModel->GetID($MessageID);

    if(!$DiscussionMessage) {
      throw NotFoundException(T('Discussion Message'));
    }

    $Sender->Permission('Garden.Settings.Manage');

    $Sender->SetData('Title', T('Delete Discussion Message'));
    if($Sender->Form->IsPostBack()) {
      $Error = $DiscussionMessageModel->Delete($MessageID);
      if((is_object($Error) && $Error->Result()) || (!is_object($Error) && $Error)) {
        $Sender->Form->AddError(T('Unable to delete discussion message!'));
      }

      if($Sender->Form->ErrorCount() == 0) {
        if($Sender->DeliveryType() === DELIVERY_TYPE_ALL) {
          Redirect('settings/discussionmessages');
        }

        $Sender->JsonTarget('#DiscussionMessage_' . $MessageID, NULL, 'SlideUp');
      }
    }
    $Sender->Render($this->GetView('delete.php'));
  }
  
  public function DiscussionController_AfterComment_Handler($Sender) {
    if(GetValue('Type', $Sender->EventArguments, FALSE) == 'Discussion') {
      $this->DiscussionController_AfterDiscussion_Handler($Sender);
      $Sender->EventArguments['DM_Handled'] = TRUE;
    }
  }
  
  public function DiscussionController_AfterDiscussion_Handler($Sender) {
    if(!GetValue('DM_Handled', $Sender->EventArguments, FALSE)) {    
      $DiscussionMessageModel = new DiscussionMessageModel();
      $Discussion = GetValue('Discussion', $Sender->EventArguments);
      $DiscussionID = $Discussion->DiscussionID;
      $Messages = $DiscussionMessageModel->GetDiscussionID($DiscussionID);
      if(count($Messages)) {
        echo RenderDiscussionMessages($Messages);
      }
      $Sender->EventArguments['DM_Handled'] = TRUE;
    }
  }
	
	public function Setup() {
    $this->Structure();
	}

	public function Structure() {
    $Database = Gdn::Database();
    $Construct = $Database->Structure();

    $Construct->Table('DiscussionMessage');
    $Construct
            ->PrimaryKey('DiscussionMessageID')
            ->Column('Name', 'varchar(255)')
            ->Column('Body', 'text', FALSE, 'fulltext')
            ->Column('MobileBody', 'text', TRUE, 'fulltext')
            ->Column('DiscussionID', 'int', FALSE)
            ->Set();
  }

	public function OnDisable() {
		return TRUE;
	}
}
