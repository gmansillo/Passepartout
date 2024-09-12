<?php

namespace GiovanniMansillo\Component\Dory\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\Filesystem\File;
use Joomla\CMS\Form\Form;
use Joomla\Component\Categories\Administrator\Helper\CategoriesHelper;
use stdClass;

class DocumentModel extends AdminModel
{
    /**
     * Method to get the record form.
     *
     * @param   array    $data      Data for the form. [optional]
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not. [optional]
     *
     * @return  Form|boolean  A Form object on success, false on failure
     *
     * @since   1.6
     */
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

        $app        =   Factory::getApplication();
        $id         =   $app->input->get('id', null, 'int');

        if (isset($id)) {
            $form->setFieldAttribute('file_upload', 'required', 'false');
            $form->setFieldAttribute('file_upload', 'label', 'COM_DORY_DOCUMENT_FILE_REPLACE');
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed  The data for the form.
     *
     * @since   1.6
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $app = Factory::getApplication();
        $data = $app->getUserState('com_dory.edit.document.data', []);

        if (empty($data)) {
            $data = $this->getItem();

            // Prime some default values.
            if ($this->getState('document.id') == 0) {
                $filters     = (array) $app->getUserState('com_dory.documents.filter');
                $filterCatId = $filters['category_id'] ?? null;

                $data->set('category', $app->getInput()->getInt('category', $filterCatId));
            }
        }

        $this->preprocessData('com_dory.document', $data);

        return $data;
    }

    /**
     * Allows preprocessing of the Form object.
     *
     * @param   Form    $form   The form object
     * @param   array   $data   The data to be merged into the form object
     * @param   string  $group  The plugin group to be executed
     *
     * @return  void
     *
     * @since    3.6.1
     */
    protected function preprocessForm(Form $form, $data, $group = 'content')
    {
        if ($this->canCreateCategory()) {
            $form->setFieldAttribute('category', 'allowAdd', 'true');

            // Add a prefix for categories created on the fly.
            $form->setFieldAttribute('category', 'customPrefix', '#new#');
        }

        parent::preprocessForm($form, $data, $group);
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
        $app = Factory::getApplication();
        $input = $app->getInput();
        $table = Table::getInstance('DocumentTable');
        $files = $input->files->get('jform');

        // TODO: Check max_upload_filesize and warn user
        // Utility::getMaxUploadSize()
        // TODO: Automatic category creation has a bug in the name
        // TODO: Add removing documents capability

        // Create new category, if needed.
        $createCategory = true;

        // If category ID is provided, check if it's valid.
        if (is_numeric($data['category']) && $data['category']) {
            $createCategory = !CategoriesHelper::validateCategoryId($data['category'], 'com_dory');
        }

        // Save New Category
        if ($createCategory && $this->canCreateCategory()) {
            $category = [
                // Remove #new# prefix, if exists.
                'title'     => strpos($data['category'], '#new#') === 0 ? substr($data['category'], 5) : $data['category'],
                'parent_id' => 1,
                'extension' => 'com_dory',
                'language'  => $data['language'],
                'published' => 1,
            ];

            /** @var \Joomla\Component\Categories\Administrator\Model\CategoryModel $categoryModel */
            $categoryModel = $app->bootComponent('com_categories')
                ->getMVCFactory()->createModel('Category', 'Administrator', ['ignore_request' => true]);

            // Create new category.
            if (!$categoryModel->save($category)) {
                $this->setError($categoryModel->getError());

                return false;
            }

            // Get the new category ID.
            $data['category'] = $categoryModel->getState('category.id');
        }

        // File
        $file = $files['file_upload'];
        if (isset($file['size']) && $file['size'] > 0) {

            if (isset($file['error']) && !empty($file['error'])) {
                $app->enqueueMessage($file['error'], 'warning');
                return false;
            }

            // Ensure that a malicious user hasn't tried to trick the script into working on files upon which it should not be working--for instance, /etc/passwd.
            if (!file_exists($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
                // TODO: localize error message
                $app->enqueueMessage("Error encountered uploading " . $file['tmp_name'], 'warning');
                return false;
            }

            // Delete previous file
            if ($data['file']) {
                $fileData = json_decode($data['file'], false);
                if (!File::delete($fileData->path))
                    $app->enqueueMessage("Unable to delete previous file. Remove it manually from " . $data["file_path"], 'warning'); // @TODO:  localize warning message
            }

            // File upload
            $dest = JPATH_ADMINISTRATOR . '/components/com_dory/uploads/' . uniqid(random_int(1000, 9999), true);
            if (!File::upload($file["tmp_name"], $dest)) {
                $app->enqueueMessage("Error encountered uploading " . $file['tmp_name'], 'warning');   // @TODO: localize error message
                return false;
            }

            $data["file"] = json_encode(
                [
                    "md5" => md5_file($dest),
                    "size" => $file['size'],
                    "path" => $dest,
                    "name" => File::makeSafe($file['name']),
                    "mime_content_type" => mime_content_type($dest),
                ]
            );
        }

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
