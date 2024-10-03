<?php

/**
 * @package     GiovanniMansillo.Dory
 * @subpackage  com_dory
 *
 * @copyright   2024 Giovanni Mansillo <https://www.gmansillo.it>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;

/** @var \GiovanniMansillo\Component\Dory\Administrator\View\Documents\HtmlView $this */

$displayData = [
    'textPrefix' => 'COM_DORY',
    'formURL'    => 'index.php?option=com_dory&view=documents',
    'icon'       => 'icon-file dory',
];

$user = $this->getCurrentUser();

if ($user->authorise('core.create', 'com_dory') || count($user->getAuthorisedCategories('com_dory', 'core.create')) > 0) {
    $displayData['createURL'] = 'index.php?option=com_dory&task=document.add';
}

echo LayoutHelper::render('joomla.content.emptystate', $displayData);
