<?php

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Text;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

$isnew = isset($this->item->id);
?>

<form
    action="<?php echo Route::_('index.php?option=com_dory&view=document&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post" name="adminForm" id="document-form" class="form-validate" enctype="multipart/form-data">

    <?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>

    <div class="main-card mb-3">

        <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab1', ['active' => 'details', 'recall' => true, 'breakpoint' => 768]); ?>
        <?php echo HTMLHelper::_('uitab.addTab', 'myTab1', 'filedata', Text::_('COM_DORY_DOCUMENT_FILE')); ?>
        <div class="<?= $isnew ? "row" : "row form-vertical"; ?> ">

            <div class="col">
                <?php echo $this->form->renderField('file_name'); ?>
                <?php echo $this->form->renderField('file_path'); ?>
                <?php echo $this->form->renderField('file_size'); ?>
            </div>

            <div class="col">
                <?php echo $this->form->renderField('file_md5'); ?>
                <?php echo $this->form->renderField('file_mime_content_type'); ?>
            </div>

            <div class="col-12">
                <?php echo $this->form->renderField('file_upload'); ?>
            </div>

        </div>

        <?php echo HTMLHelper::_('uitab.endTab'); ?>
        <?php echo HTMLHelper::_('uitab.endTabSet'); ?>

    </div>


    <div class="main-card">
        <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'details', 'recall' => true, 'breakpoint' => 768]); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', Text::_('COM_DORY_DOCUMENT_DETAILS')); ?>

        <div class="row">
            <div class="col-lg-3 order-lg-5">
                <fieldset class="form-vertical">
                    <?php echo $this->form->renderField('state'); ?>
                    <?php echo $this->form->renderField('category'); ?>
                    <?php echo $this->form->renderFieldset('accessrules'); ?>
                </fieldset>
            </div>
            <div class="col-lg-9 order-lg-1">
                <fieldset class="form-vertical">
                    <?php echo $this->form->renderField('description'); ?>
                </fieldset>
            </div>
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'publishing', Text::_('JGLOBAL_FIELDSET_PUBLISHING')); ?>

        <div class="row">
            <div class="col-lg-12">
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