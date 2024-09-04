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
                $app = Factory::getApplication();
                $input = $app->getInput();
                $table = Table::getInstance('DocumentTable');
                $files = $input->files->get('jform');

                // TODO: Check max_upload_filesize and warn user

                // File
                $file = $files['file_upload'] ?? $files['file_replace'];
                if (isset($file['size']) && $file['size'] > 0) {

                        if (isset($file['error']) && !empty($file['error'])) {
                                // TODO: stop uploading and show error message
                                die($file['error']);
                                return false;
                        }

                        // Ensure that a malicious user hasn't tried to trick the script into working on files upon which it should not be working--for instance, /etc/passwd.
                        if (!file_exists($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
                                // TODO: show error
                                die("Error in file upload");
                                return false;
                        }

                        // Delete previous file
                        if ($data['file_path'])
                                if (!File::delete($data["file_path"])); // TODO:  show warning message

                        // File upload
                        $dest = JPATH_ADMINISTRATOR . '/components/com_dory/uploads/' . uniqid(random_int(1000, 9999), true);
                        if (!File::upload($file["tmp_name"], $dest)); // TODO: stop saving and show error message

                        $data["file_name"] = File::makeSafe($file['name']);
                        $data["file_md5"] = md5_file($dest);
                        $data["file_size"] = $file['size'];
                        $data["file_path"] = $dest;
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
