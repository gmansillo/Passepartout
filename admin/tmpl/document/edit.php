<?php

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Text;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

$item = $this->item;
$form = $this->form;
$isNew = !isset($item->id) || $item->id <= 0;
?>

<form
    action="<?php echo Route::_('index.php?option=com_dory&view=document&layout=edit&id=' . (int) $item->id); ?>"
    method="post" name="adminForm" id="document-form" class="form-validate" enctype="multipart/form-data">

    <?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>

    <div class="main-card">

        <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'details', 'recall' => true, 'breakpoint' => 768]); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', empty($item->id) ? Text::_('COM_DORY_NEW_DOCUMENT') : Text::_('COM_DORY_DOCUMENT_DETAILS')); ?>

        <div class="row">
            <div class="col-md-9">
                <div class="form-vertical">
                    <?php echo $form->renderField('description'); ?>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-vertical">

                    <?php if ($item->file_name): ?>

                        <!-- <?php echo $form->renderField('file_name'); ?> -->

                        <div class="control-group">
                            <div class="control-label">
                                <label id="jform_file-lbl" for="jform_file"><?= Text::_('COM_DORY_DOCUMENT_FILE_NAME'); ?></label>
                            </div>
                            <div class="controls">

                                <field
                                    name="file_path"
                                    type="text"
                                    readonly="true"
                                    dlabel="COM_DORY_DOCUMENT_FILE_PATH" />

                                <div class="row">
                                    <!-- <div class="col-3">
                                        <img src="" />
                                    </div> -->
                                    <div class="col">

                                        <h5><a href="<?= $item->file_path; ?>" target="_blank"><?= $item->file_name; ?></a> (<?= $item->file_size; ?>byte)</h5>
                                        <!-- <div class="btn btn-outline-secondary" type="button" id="button-addon2"><i class="icon icon-info"></i></div> -->

                                        <small><?= Text::_('COM_DORY_DOCUMENT_FILE_MD5') ?>: <strong><?= $item->file_md5; ?></strong></small>

                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- 
                        <?php echo $form->renderField('file_size'); ?>
                        <?php echo $form->renderField('file_md5'); ?>
                        <?php echo $form->renderField('file_path'); ?> -->

                        <?php /* TODO: show formatted document with icon and file data */ ?>

                        <?php echo $form->renderField('file_replace'); ?>

                    <?php else: ?>

                        <?php echo $form->renderField('file_upload'); ?>

                    <?php endif; ?>

                    <?php echo $form->renderFieldset('sideparams'); ?>

                </div>
            </div>
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'publishing', Text::_('JGLOBAL_FIELDSET_PUBLISHING')); ?>
        <div class="row">
            <div class="col-md-12">
                <fieldset id="fieldset-publishingdata" class="options-form">
                    <legend><?php echo Text::_('JGLOBAL_FIELDSET_PUBLISHING'); ?></legend>
                    <div>
                        <?php echo LayoutHelper::render('joomla.edit.publishingdata', $this); ?>
                    </div>
                </fieldset>
            </div>
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.endTabSet'); ?>
    </div>

    <input type="hidden" name="task" value="">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>