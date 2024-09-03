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

                    <div class="row">

                        <?php if ($item->file_size > 0): ?>

                            <div class="col-md-3">
                                <?php echo $form->renderField('file_size'); ?>
                                <?php echo $form->renderField('file_path'); ?>
                                <?php echo $form->renderField('file_name'); ?>
                            </div>

                        <?php endif; ?>

                        <div class="col-md-<?php echo $isNew ? 12 : 9 ?>">

                            <?php echo $form->renderField('file_upload'); ?>

                        </div>
                    </div>

                    <?php echo $form->renderField('description'); ?>

                </div>
            </div>

            <div class="col-md-3">
                <div class="form-vertical">
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