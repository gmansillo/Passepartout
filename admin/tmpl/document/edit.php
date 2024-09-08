<?php

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Text;
use GiovanniMansillo\Component\Dory\Site\Helper\DoryHelper;

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

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', Text::_('COM_DORY_DOCUMENT_DETAILS')); ?>

        <div class="row form-vertical">
            <div class="col-md-9">
                <?php echo $form->renderField('description'); ?>
            </div>

            <div class="col-md-3">

                <?php if (!$item->file_name): ?>
                    <?php echo $form->renderField('file_upload'); ?>
                <?php endif; ?>

                <?php echo $form->renderFieldset('sideparams'); ?>
            </div>
        </div>

        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php if ($item->file_name): ?>
            <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'filedata', Text::_('COM_DORY_DOCUMENT_FILEDATA')); ?>

            <fieldset id="fieldset-filedata" class="options-form">
                <legend><?php echo Text::_('COM_DORY_DOCUMENT_FILEDATA'); ?></legend>
                <?php echo $form->renderFieldset('filedata'); ?>
            </fieldset>

            <?php echo $form->renderField('file_replace'); ?>

            <?php echo HTMLHelper::_('uitab.endTab'); ?>
        <?php endif; ?>


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