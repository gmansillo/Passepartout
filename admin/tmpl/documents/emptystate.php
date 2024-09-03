<?php

/**
 * @package     GiovanniMansillo.Dory
 * @subpackage  com_dory
 *
 * @copyright   (C) 2021 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;

$displayData = [
    'textPrefix' => 'COM_DORY',
    'formURL'    => 'index.php?option=com_dory&view=documents',
    // 'helpURL'    => 'https://docs.joomla.org/Special:MyLanguage/Help40:Banners',
    'icon'       => 'icon-file dory',
];

$user = $this->getCurrentUser();

if ($user->authorise('core.create', 'com_dory') || count($user->getAuthorisedCategories('com_dory', 'core.create')) > 0) {
    $displayData['createURL'] = 'index.php?option=com_dory&task=document.add';
}

echo LayoutHelper::render('joomla.content.emptystate', $displayData);
