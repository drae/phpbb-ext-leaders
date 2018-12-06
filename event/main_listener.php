<?php
/**
 *
 * This file is part of the phpBB Forum Software package.
 *
 * @copyright (c) phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * For full copyright and license information, please see
 * the docs/CREDITS.txt file.
 *
 */

namespace numeric\leaders\event;

/**
* @ignore
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class main_listener implements EventSubscriberInterface
{
	/* @var \phpbb\controller\helper */
	protected $helper;
	protected $db;
	protected $template;

	/**
	* Constructor
	*
	* @param \phpbb\controller\helper	$helper		Controller helper object
	*/
	public function __construct(\phpbb\controller\helper $helper, \phpbb\db\driver\driver_interface $db, \phpbb\template\template $template)
	{
		$this->helper = $helper;
		$this->db = $db;
		$this->template = $template;
	}

	/**
	*
	*/
	static public function getSubscribedEvents()
	{
		return array(
			'core.page_footer' => 'leaders',
		);
	}


	public function leaders($event)
	{
		$sql = 'SELECT name, class_clean, rank
			FROM roster_players
			WHERE rank IN (0, 1)
				AND left_guild <> 1
			ORDER BY rank, name';
		$this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow())
		{
			if ($row['rank'] == 0)
			{
				$this->template->assign_vars(array(
					'GM_NAME'	=> $row['name'],
					'GM_CLASS'	=> $row['class_clean'],
					'GM_LINK'	=> '/roster/character/' . $row['name'],
				));
			}
			else
			{
				$this->template->assign_block_vars('officers', array(
					'NAME'	=> $row['name'],
					'CLASS'	=> $row['class_clean'],
					'LINK'	=> '/roster/character/' . $row['name'],
				));
			}

		}
		$this->db->sql_freeresult();
	}
}
