<?php

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;

\defined('_JEXEC') or die;

$wa = $this->document->getWebAssetManager();
$wa->useScript('table.columns')
    ->useScript('multiselect');

$user = $this->getCurrentUser();
$userId = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));

?>

<form action="<?php echo Route::_('index.php?option=com_dory&view=documents'); ?>" method="post" name="adminForm"
    id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); // Search toolbar ?>

                <?php if (empty($this->items)): ?>
                    <div class="alert alert-info">
                        <span class="icon-info-circle" aria-hidden="true"></span><span
                            class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
                        <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                    </div>
                <?php else: ?>
                    <table class="table" id="documentList">
                        <caption class="visually-hidden">
                            <?php echo Text::_('COM_DORY_DOCUMENTS_TABLE_CAPTION'); ?>,
                            <span id="orderedBy"><?php echo Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
                            <span id="filteredBy"><?php echo Text::_('JGLOBAL_FILTERED_BY'); ?></span>
                        </caption>
                        <thead>
                            <tr>
                                <td class="w-1 text-center">
                                    <?php echo HTMLHelper::_('grid.checkall'); ?>
                                </td>
                                <th scope="col" class="w-1 text-center">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_DORY_HEADING_TITLE', 'a.title', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="w-10 d-none d-md-table-cell">

                                </th>
                                <th scope="col" class="w-5 d-none d-md-table-cell">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_DORY_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="w-5 text-center d-none d-md-table-cell">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_DORY_HEADING_HITS', 'a.hits', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="w-5 d-none d-md-table-cell">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($this->items as $i => $item):
                                $item->cat_link = Route::_('index.php?option=com_categories&extension=com_dory&task=edit&type=other&cid[]=' . $item->category);
                                $canCreate = $user->authorise('core.create', 'com_dory.category.' . $item->category);
                                $canEdit = $user->authorise('core.edit', 'com_dory.category.' . $item->category);
                                $canCheckin = $user->authorise('core.manage', 'com_checkin');//|| $item->checked_out == $userId || is_null($item->checked_out);
                                $canChange = $user->authorise('core.edit.state', 'com_dory.category.' . $item->category) && $canCheckin;
                                ?>
                                <tr class="row<?php echo $i % 2; ?>" data-draggable-group="<?php echo $item->category; ?>">
                                    <td class="text-center">
                                        <?php echo HTMLHelper::_('grid.id', $i, $item->id, false, 'cid', 'cb', $item->title); ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'documents.', $canChange, 'cb');  //, $item->publish_up, $item->publish_down); ?>
                                    </td>
                                    <th scope="row">
                                        <div class="break-word">
                                            <?php if ($canEdit): ?>
                                                <a href="<?php echo Route::_('index.php?option=com_dory&task=document.edit&id=' . (int) $item->id); ?>"
                                                    title="<?php echo Text::_('JACTION_EDIT'); ?> <?php echo $this->escape($item->title); ?>">
                                                    <?php echo $this->escape($item->title); ?></a>
                                            <?php else: ?>
                                                <?php echo $this->escape($item->title); ?>
                                            <?php endif; ?>
                                            <div class="small break-word">
                                                <?php echo Text::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
                                            </div>
                                            <?php if (!empty($item->category)): ?>
                                                <div class="small">
                                                    <?php echo Text::_('JCATEGORY') . ": " . $this->escape($item->category_title); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </th>
                                    <td class="small d-none d-md-table-cell">

                                    </td>
                                    <td class="small d-none d-md-table-cell">
                                        <?php echo $this->escape($item->access); ?>
                                    </td>
                                    <td class="small text-center d-none d-md-table-cell">
                                        <span class="badge bg-info"><?php echo $this->escape("0"); ?></span>
                                    </td>
                                    <td class="text-center d-none d-md-table-cell">
                                        <?php echo $item->id; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <?php // Load the pagination. ?>
                    <?php echo $this->pagination->getListFooter(); ?>

                    <?php // Load the batch processing form. ?>
                    <?php
                    if (
                        $user->authorise('core.create', 'com_dory')
                        && $user->authorise('core.edit', 'com_dory')
                        && $user->authorise('core.edit.state', 'com_dory')
                    ): ?>
                        <?php
                        /* echo HTMLHelper::_(
                            'bootstrap.renderModal',
                            'collapseModal',
                            [
                                'title' => Text::_('COM_DORY_BATCH_OPTIONS'),
                                'footer' => $this->loadTemplate('batch_footer')
                            ],
                            $this->loadTemplate('batch_body')
                        ) */ ;
                        ?>
                    <?php endif; ?>
                <?php endif; ?>

                <input type="hidden" name="task" value="">
                <input type="hidden" name="boxchecked" value="0">
                <?php echo HTMLHelper::_('form.token'); ?>
            </div>
        </div>
    </div>
</form>