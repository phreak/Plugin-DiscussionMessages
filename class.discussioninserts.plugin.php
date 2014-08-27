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
$PluginInfo['DiscussionInserts'] = array(
	'Name' => 'Discussion Inserts',
	'Description' => 'Adds inserts to specific discussions. Created by Zachary Doll and VanillaSkins.com - #1 Themeshop for Vanilla.',
	'Version' => '0.1',
	'RequiredApplications' => array('Vanilla' => '2.0.18.13'),
	'RequiredTheme' => FALSE,
	'RequiredPlugins' => FALSE,
	'MobileFriendly' => TRUE,
	'HasLocale' => TRUE,
	'RegisterPermissions' => FALSE,
  'SettingsUrl' => '/settings/discussioninserts',
	'SettingsPermission' => 'Garden.Settings.Manage',
	'Author' => 'Zachary Doll',
	'AuthorEmail' => 'hgtonight@daklutz.com',
	'AuthorUrl' => 'http://www.daklutz.com',
	'License' => 'GPLv3'
);

class DiscussionInserts extends Gdn_Plugin {
  
  public function __construct() {
    parent::__construct();
    require_once($this->GetPluginFolder() . DS . 'library' . DS . 'functions.render.php');
  }

  public function Base_GetAppSettingsMenuItems_Handler($Sender) {
    $Menu = $Sender->EventArguments['SideMenu'];
    $Menu->AddLink('Appearance', T('Discussion Inserts'), 'settings/discussioninserts', 'Garden.Settings.Manage');
  }
  
  public function DiscussionsController_DiscussionOptions_Handler($Sender) {
    $Discussion = $Sender->EventArguments['Discussion'];
    $Options =& $Sender->Options;
    $Options .= Wrap(
            Anchor(T('Add Insert'), 'discussion/inserts/' . $Discussion->DiscussionID, array('class' => 'Popup')),
            'li');
  }
  
  public function DiscussionController_DiscussionOptions_Handler($Sender) {
    $Discussion = $Sender->EventArguments['Discussion'];
    $Options =& $Sender->EventArguments['DiscussionOptions'];
    $Options[] = array(
        'Label' => T('Add Insert'),
        'Url' => 'discussion/inserts/' . $Discussion->DiscussionID,
        'Class' => 'Popup'
    );
  }
  
  public function DiscussionController_CommentOptions_Handler($Sender) {
    if(GetValue('Type', $Sender->EventArguments, FALSE) == 'Discussion') {
      $Discussion = $Sender->EventArguments['Discussion'];
      echo Wrap(
              Anchor(T('Add Insert'), 'discussion/inserts/' . $Discussion->DiscussionID, array('class' => 'Popup')),
              'span');
    }
  }
  
  public function DiscussionController_Inserts_Create($Sender) {
    $DiscussionID = GetValue(0,$Sender->RequestArgs,NULL);
    if(is_null($DiscussionID)) {
      Redirect('settings/discussioninserts/add');
    }
    $DiscussionInsertModel = new DiscussionInsertModel();
    $Sender->Form->SetModel($DiscussionInsertModel);
    $Sender->Form->AddHidden('DiscussionID', $DiscussionID);
    
    $Sender->Title(T('Add Discussion Insert'));

    if($Sender->Form->IsPostBack() != FALSE) {
      $InsertID = $Sender->Form->Save();
      if($InsertID) {
        $Insert = $DiscussionInsertModel->GetID($InsertID);
        $Sender->InformMessage(T('Discussion Insert added successfully!'));
        $Sender->JsonTarget('.MessageList.Discussion', RenderDiscussionInsert($Insert), 'After');
        $Sender->JsonTarget('#DiscussionInsert_' . $Insert->DiscussionInsertID, NULL, 'Highlight');
      }
    }

    $Sender->Render($this->GetView('insert.php'));
  }
  
  public function DiscussionController_Render_Before($Sender) {
    $Sender->AddJsFile($this->GetResource('js/discussioninserts.js', FALSE, FALSE));
  }
  
	public function SettingsController_DiscussionInserts_Create($Sender) {
    $Sender->Permission('Garden.Settings.Manage');
		$Sender->AddSideMenu('settings/discussioninserts');
    $Sender->AddJsFile($this->GetResource('js/discussioninserts.js', FALSE, FALSE));
		$this->Dispatch($Sender, $Sender->RequestArgs);
	}
  
  public function Controller_Index($Sender) {
    $Sender->Title($this->GetPluginName() . ' ' . T('Settings'));
    
    $DiscussionInsertModel = new DiscussionInsertModel();
    $DiscussionInserts = $DiscussionInsertModel->Get();
    
    $Sender->SetData('DiscussionInserts', $DiscussionInserts);
		$Sender->Render($this->GetView('settings.php'));
  }
  
  public function Controller_Add($Sender) {
    $this->Controller_Edit($Sender);
  }
	
  public function Controller_Edit($Sender) {    
    $DiscussionInsertModel = new DiscussionInsertModel();
    $Sender->Form->SetModel($DiscussionInsertModel);

    $Sender->Title(T('Add Discussion Insert'));
    $Edit = FALSE;
    $InsertID = GetValue(1, $Sender->RequestArgs, FALSE);
    if($InsertID) {
      $Sender->DiscussionInsert = $DiscussionInsertModel->GetID($InsertID);
      $Sender->Form->AddHidden('DiscussionInsertID', $InsertID);
      $Edit = TRUE;
      $Sender->Title(T('Edit Discussion Insert'));
    }

    if($Sender->Form->IsPostBack() == FALSE) {
      if(property_exists($Sender, 'DiscussionInsert')) {
        $Sender->Form->SetData($Sender->DiscussionInsert);
      }
    }
    else {
      $InsertID = $Sender->Form->Save();
      if($InsertID) {
        $Insert = $DiscussionInsertModel->GetID($InsertID);
        if($Edit) {
          $Sender->JsonTarget('#DiscussionInsert_' . $Insert->DiscussionInsertID, RenderDiscussionInsert($Insert), 'Html');
          $Sender->JsonTarget('#DiscussionInsert_' . $Insert->DiscussionInsertID, NULL, 'Highlight');
          $Sender->InformMessage(T('Discussion Insert updated successfully!'));
        }
        else {
          $Sender->InformMessage(T('Discussion Insert added successfully!'));
        }
        
        if($Sender->DeliveryType() == DELIVERY_TYPE_ALL) {
          Redirect('/settings/discussioninserts');
        }
      }
    }

    $Sender->Render($this->GetView('edit.php'));
  }
  
  public function Controller_Delete($Sender) {
    $DiscussionInsertModel = new DiscussionInsertModel();
    
    $InsertID = GetValue(1, $Sender->RequestArgs, FALSE);
    $DiscussionInsert = $DiscussionInsertModel->GetID($InsertID);

    if(!$DiscussionInsert) {
      throw NotFoundException(T('Discussion Insert'));
    }

    $Sender->Permission('Garden.Settings.Manage');

    $Sender->SetData('Title', T('Delete Discussion Insert'));
    if($Sender->Form->IsPostBack()) {
      $Error = $DiscussionInsertModel->Delete($InsertID);
      if((is_object($Error) && $Error->Result()) || (!is_object($Error) && $Error)) {
        $Sender->Form->AddError(T('Unable to delete discussion insert!'));
      }

      if($Sender->Form->ErrorCount() == 0) {
        if($Sender->DeliveryType() === DELIVERY_TYPE_ALL) {
          Redirect('settings/discussioninserts');
        }

        $Sender->JsonTarget('#DiscussionInsert_' . $InsertID, NULL, 'SlideUp');
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
      $DiscussionInsertModel = new DiscussionInsertModel();
      $Discussion = GetValue('Discussion', $Sender->EventArguments);
      $DiscussionID = $Discussion->DiscussionID;
      $Inserts = $DiscussionInsertModel->GetDiscussionID($DiscussionID);
      if(count($Inserts)) {
        echo RenderDiscussionInserts($Inserts);
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

    $Construct->Table('DiscussionInsert');
    $Construct
            ->PrimaryKey('DiscussionInsertID')
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
