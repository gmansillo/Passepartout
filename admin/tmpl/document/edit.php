<?php

/**
 * @package     GiovanniMansillo.Passepartout
 * @subpackage  com_passepartout
 *
 * @copyright   2024 Giovanni Mansillo <https://www.gmansillo.it>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

/** @var \GiovanniMansillo\Component\Passepartout\Administrator\View\Document\HtmlView $this */

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate')
    // ->useScript('com_passepartout.admin-document-edit')
;
?>

<form action="<?php echo Route::_('index.php?option=com_passepartout&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="document-form" aria-label="<?php echo Text::_('COM_PASSEPARTOUT_DOCUMENT_' . ((int) $this->item->id === 0 ? 'NEW' : 'EDIT'), true); ?>" class="form-validate" enctype="multipart/form-data">

    <?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>

    <div class="main-card">
        <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'description', 'recall' => true, 'breakpoint' => 768]); ?>
        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'description', Text::_('COM_PASSEPARTOUT_FIELDSET_DETAILS')); ?>

        <div class="row form-vertical">
            <div class="col-lg-9">
                <?php echo $this->form->renderField('description'); ?>
            </div>

            <div class="col-lg-3 ">
                <?php echo $this->form->renderField('file'); ?>
                <?php echo $this->form->renderField('file_upload'); ?>

                <?php echo LayoutHelper::render('joomla.edit.global', $this); ?>

                <?php echo $this->form->renderFieldset('accessrules'); ?>
            </div>
        </div>

        <?php echo HTMLHelper::_('uitab.endTab'); ?>
        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'publishing', Text::_('JGLOBAL_FIELDSET_PUBLISHING')); ?>

        <div class="row">
            <div class="col">
                <fieldset id="fieldset-publishingdata" class="options-form">
                    <legend><?php echo Text::_('JGLOBAL_FIELDSET_PUBLISHING'); ?></legend>
                    <div>
                        <?php echo LayoutHelper::render('joomla.edit.publishingdata', $this); ?>
                    </div>
                </fieldset>
            </div>
            <?php /* <div class="col-md-6">
<fieldset id="fieldset-metadata" class="options-form">
<legend><?php echo Text::_('JGLOBAL_FIELDSET_METADATA_OPTIONS'); ?></legend>
<div>
<?php echo $this->form->renderFieldset('metadata'); ?>
</div>
</fieldset>
</div> */ ?>
        </div>

        <?php echo HTMLHelper::_('uitab.endTab'); ?>
        <?php echo HTMLHelper::_('uitab.endTabSet'); ?>
    </div>

    <input type="hidden" name="task" value="" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>