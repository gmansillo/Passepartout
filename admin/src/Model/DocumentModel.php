<?php
namespace GiovanniMansillo\Component\Dory\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\Filesystem\File;

class DocumentModel extends AdminModel
{
    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm(
            'com_dory.document', //  instructs the method to look for the document.xml file inside the forms folder of the backend
            'document',
            [
                'control' => 'jform',
                'load_data' => $loadData
            ]
        );
        if (empty($form)) {
            return false;
        }

        // // Set validations conditionally
        // if (isset($data['id']) && $data['id'] > 0) {
        //     $form->setFieldAttribute('file_upload', 'required', 'false');
        // }

        return $form;
    }

    protected function loadFormData()
    {
        $app = Factory::getApplication();
        $data = $app->getUserState('com_dory.edit.document.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Method to save the form data.
     *
     * @param   array  $data  The form data.
     *
     * @return  boolean  True on success.
     *
     * @since   1.6
     */
    public function save($data)
    {
        //         die(var_dump($data['file_upload']));
//         //TODO: Add validation on extension (maybe not here but in form field)

        //         $isNew = !isset($data['id']) || $data['id'] <= 0;

        //         $app = Factory::getApplication();
//         $input = $app->getInput();
//         $table = Table::getInstance('DocumentTable');
//         $files = $input->files->get('jform');

        //         // // Alter the name for save as copy
//         // if ($input->get('task') == 'save2copy') {
//         //     /** @var \GiovanniMansillo\Component\Dory\Administrator\Table\DocumentTable $origTable */
//         //     $origTable = clone $this->getTable();
//         //     $origTable->load($input->getInt('id'));

        //         //     if ($data['name'] == $origTable->name) {
//         //         list($name, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['name']);
//         //         $data['name'] = $name;
//         //         $data['alias'] = $alias;
//         //     } else {
//         //         if ($data['alias'] == $origTable->alias) {
//         //             $data['alias'] = '';
//         //         }
//         //     }

        //         //     $data['state'] = 0;
//         // }

        //         // if ($input->get('task') == 'save2copy' || $input->get('task') == 'copy') {
//         //     $data['hits'] = 0;
//         // }

        //         // // Alias
//         // if ($table->load(array('alias' => $data['alias'], 'catid' => $data['category']))) {
//         //     $msg = JText::_('COM_CONTENT_SAVE_WARNING');
//         // }

        //         // list($title, $alias) = $this->generateNewTitle($data['category'], $data['alias'], $data['title']);
//         // $data['alias'] = $alias;

        //         // if (isset($msg)) {
//         //     Factory::getApplication()->enqueueMessage($msg, 'warning');
//         // }

        //         // File
//         $file = $files['file_upload'] ?? $files['file_replace'];
//         die($file);

        //         if ($file['name']) {

        //             if (!$isNew) {
//                 File::delete($data["file_path"]); // Delete the previous file
//             }

        //             $src = $file["tmp_name"];
//             $dest = JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . $file['name'];

        //             $result = File::upload($src, $dest);

        //             // TODO: Define translatable error message
//             if (!$result) {
//                 $app->enqueueMessage("", 'warning');
//             }

        //             $data["file_upload"] = "assfd";
        $data["file_name"] = "";
        $data["file_md5"] = "";
        $data["file_size"] = 1;
        $data["file_path"] = "";

        //             die($file);

        //             // TODO: Implement file extension check
// // 			// File upload
// // 			$file_name         = $files["select_file"]["name"];
// // 			$dest              = JPATH_COMPONENT_ADMINISTRATOR . "/uploads/" . uniqid("", true) . "/" . JFile::makeSafe(JFile::getName($file_name));
// // 			$data["file_name"] = JFile::upload($files["select_file"]["tmp_name"], $dest) ? $dest : false;

        //             // 			if (!$data["file_name"])
// // 			{
// // 				JFactory::getApplication()->enqueueMessage(JText::_('COM_FIREDRIVE_FILE_UPLOAD_ERROR_MESSAGE'), 'error');
// // 				parent::save($key, $urlVar);

        //             // 				return;
// // 			}

        //             // 			$data["file_size"] = $files["select_file"]["size"];
// // 			$data["md5hash"]   = md5_file($data["file_name"]);





        //             // 	/**
// // 	 * Prepare data before executing controller save function
// // 	 *
// // 	 * @param mixed   $data  Form data (passed by reference)
// // 	 * @param unknown $files File to upload
// // 	 *
// // 	 * @since   5.2.1
// // 	 */
// // 	protected function prepareDataBeforeSave(&$data, $files)
// // 	{
// // 		$params       = JComponentHelper::getParams('com_firedrive');
// // 		$user         = JFactory::getUser();
// // 		$canManage    = $user->authorise('core.manage', 'com_firedrive');
// // 		$canEditState = $user->authorise('core.edit.state', 'com_firedrive');
// // 		$isNew        = empty($data["id"]);







        //             // 		$data["state"]      = $params->get('default_state', 0);
// // 		$data["visibility"] = $params->get('default_visibility', 5);
// // 		$data["created"]    = JFactory::getDate()->toSql();
// // 		$data["created_by"] = $user->id;
// // 		$data["language"]   = "*";

        //             // 		// Send notify email
// // 		// TODO: Advice administrators via email notification

        //             // 		return;
// // 	}

        //             // 	function cancel()
// // 	{

        //             // 		$app = JFactory::getApplication();

        //             // 		// Get the current edit id.
// // 		$editId = (int) $app->getUserState('com_firedrive.edit.document.id');

        //             // 		// Get the model.
// // 		$model = $this->getModel('DocumentForm', 'FiredriveModel');

        //             // 		// Check in the item
// // 		if ($editId)
// // 		{
// // 			$model->checkin($editId);
// // 		}

        //             // 		$menu = JFactory::getApplication()->getMenu();
// // 		$item = $menu->getActive();
// // 		$url  = (empty($item->link) ? 'index.php?option=com_firedrive' : $item->link);
// // 		$this->setRedirect(JRoute::_($url, false));
// // 	}

        //             // 	public function remove()
// // 	{
// // 		// Initialise variables.
// // 		$app   = JFactory::getApplication();
// // 		$model = $this->getModel('DocumentForm', 'FiredriveModel');

        //             // 		// Get the user data.
// // 		$data       = array();
// // 		$data["id"] = $app->input->getInt('id');

        //             // 		// Check for errors.
// // 		if (empty($data["id"]))
// // 		{
// // 			// Get the validation messages.
// // 			$errors = $model->getErrors();

        //             // 			// Push up to three validation messages out to the user.
// // 			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
// // 			{
// // 				if ($errors[$i] instanceof Exception)
// // 				{
// // 					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
// // 				}
// // 				else
// // 				{
// // 					$app->enqueueMessage($errors[$i], 'warning');
// // 				}
// // 			}

        //             // 			// Save the data in the session.
// // 			$app->setUserState('com_firedrive.edit.document.data', $data);

        //             // 			// Redirect back to the edit screen.
// // 			$id = (int) $app->getUserState('com_firedrive.edit.document.id');
// // 			$this->setRedirect(JRoute::_('index.php?option=com_firedrive&view=firedrive&layout=edit&id=' . $id, false));

        //             // 			return false;
// // 		}

        //             // 		// Attempt to save the data.
// // 		$return = $model->delete($data);

        //             // 		// Check for errors.
// // 		if ($return === false)
// // 		{
// // 			// Save the data in the session.
// // 			$app->setUserState('com_firedrive.edit.document.data', $data);

        //             // 			// Redirect back to the edit screen.
// // 			$id = (int) $app->getUserState('com_firedrive.edit.document.id');
// // 			$this->setMessage(JText::sprintf('Delete failed', $model->getError()), 'warning');
// // 			$this->setRedirect(JRoute::_('index.php?option=com_firedrive&view=firedrive&layout=edit&id=' . $id, false));

        //             // 			return false;
// // 		}


        //             // 		// Check in the profile.
// // 		if ($return)
// // 		{
// // 			$model->checkin($return);
// // 		}

        //             // 		// Clear the profile id from the session.
// // 		$app->setUserState('com_firedrive.edit.document.id', null);

        //             // 		// Redirect to the list screen.
// // 		$this->setMessage(JText::_('COM_FIREDRIVE_ITEM_DELETED_SUCCESSFULLY'));
// // 		$menu = JFactory::getApplication()->getMenu();
// // 		$item = $menu->getActive();
// // 		$url  = (empty($item->link) ? 'index.php?option=com_firedrive' : $item->link);
// // 		$this->setRedirect(JRoute::_($url, false));

        //             // 		// Flush the data from the session.
// // 		$app->setUserState('com_firedrive.edit.document.data', null);
// // 	}

        //             // }














        //         }
//         //  = $isNew ? $data['file_upload'] : ($data['file_replace'] != '' ? $data['file_replace'] : $data['file']);

        // Set created by
        $user = Factory::getApplication()->getIdentity();
        if (!(int) $data['created_by']) {
            $data['created_by'] = $user->id;
        }

        // Set modified
        if (!(int) $data['modified_by']) {
            $data['modified_by'] = $data['created_by'];
        }

        return parent::save($data);
    }

    /**
     * Is the user allowed to create an on the fly category?
     *
     * @return  boolean
     *
     * @since   3.6.1
     */
    private function canCreateCategory()
    {
        return $this->getCurrentUser()->authorise('core.create', 'com_dory');
    }
}