<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
    This file is part of Classee Body add-on for ExpressionEngine.

    Classee Body is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Classee Body is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    Read the terms of the GNU General Public License
    at <http://www.gnu.org/licenses/>.
    
    Copyright 2016 Derek Hogue - http://amphibian.info
*/

class Classee_body
{

	function __construct()
	{
		$this->return_data = '';

		$attr = ee()->TMPL->fetch_param('attr', 'true');
		$browser = (isset($_SERVER['HTTP_USER_AGENT'])) ? strtolower($_SERVER['HTTP_USER_AGENT']) : 'unknown';
		$cat_trigger = ee()->config->item('reserved_category_word');
		$classes = array();
		$disable = (ee()->TMPL->fetch_param('disable')) ?
			explode('|', ee()->TMPL->fetch_param('disable')) :
			array();
		$group = ee()->session->userdata['group_id'];
		$segments = count(ee()->uri->segments);

		if($segments > 0)
		{
			// One class per URI segment
			if(!in_array('segments', $disable))
			{
				for($i = 1; $i <= $segments; $i++)
				{
					$seg = ee()->uri->segment($i);
					// Ignore the category indicator
					if($seg != $cat_trigger)
					{
						$classes[] = (is_numeric(substr($seg,0,1))) ? 'n'.$seg : $seg;
					}
				}
			}

			// Check for pagination
			if(!in_array('paged', $disable) && preg_match('/P{1}[0-9]+/', ee()->uri->uri_string) != FALSE)
			{
				$classes[] = 'paged';
			}

			// Check for category
			if(!in_array('category', $disable) && strpos(ee()->uri->uri_string, "/$cat_trigger/") !== FALSE
				|| preg_match('/C{1}[0-9]+/', ee()->uri->uri_string) != FALSE)
			{
				$classes[] = 'category';
			}

			// Check for monthly archive
			if(!in_array('monthly', $disable) && $segments >= 2)
			{
				$m = ee()->uri->segment($segments);
				$y = ee()->uri->segment($segments-1);
				if(preg_match('/^[0-9]{4}$/', $y) != FALSE && preg_match('/^[0-9]{2}$/', $m) != FALSE)
				{
					$classes[] = 'monthly';
				}
			}
		}
		else
		{
			// No segs, so we're on the home page
			$classes[] = 'home';
		}

		// Class for member group
		if(!in_array('member_group', $disable))
		{
			switch($group)
			{
				case 1:
					$classes[] = 'superadmin';
					break;
				case 2:
					$classes[] = 'banned';
					break;
				case 3:
					$classes[] = 'guest';
					break;
				case 4:
					$classes[] = 'pending';
					break;
				case 5:
					$classes[] = 'member';
					break;
				case ($group > 5):
					$classes[] = 'groupid_' . $group;
					break;
			}
		}

		// Some lightweight browser detection
		if(!in_array('browser', $disable) && $browser != 'unknown')
		{
			if(strpos($browser, 'lynx') !== false)
			{
				$classes[] = 'lynx';
			}
			elseif(strpos($browser, 'chrome') !== false)
			{
				$classes[] = 'chrome';
			}
			elseif(strpos($browser, 'safari') !== false)
			{
				$classes[] = 'safari';

				if(strpos($browser, 'ipod') !== false)
				{
					$classes[] = 'ipod';
				}
				elseif(strpos($browser, 'iphone') !== false)
				{
					$classes[] = 'iphone';
				}
				elseif(strpos($browser, 'ipad') !== false)
				{
					$classes[] = 'ipad';
				}
			}
			elseif(strpos($browser, 'firefox') !== false)
			{
				$classes[] = 'firefox';
			}
			elseif(strpos($browser, 'gecko') !== false)
			{
				$classes[] = 'gecko';
			}
			elseif(strpos($browser, 'msie') !== false)
			{
				if(strpos($browser, 'msie 10') !== false)
				{
					$classes[] = 'ie10';
				}
				elseif(strpos($browser, 'msie 9') !== false)
				{
					$classes[] = 'ie9';
				}
				elseif(strpos($browser, 'msie 8') !== false)
				{
					$classes[] = 'ie8';
				}
				elseif(strpos($browser, 'msie 7') !== false)
				{
					$classes[] = 'ie7';
				}
				elseif(strpos($browser, 'msie 6') !== false)
				{
					$classes[] = 'ie6';
				}
				elseif(strpos($browser, 'msie 5') !== false)
				{
					$classes[] = 'ie5';
				}
				else
				{
					$classes[] = 'ie';
				}
			}
			elseif(strpos($browser, 'opera') !== false)
			{
				$classes[] = 'opera';
			}
			elseif(strpos($browser, 'nav') !== false && strpos($browser, 'mozilla/4.') !== false)
			{
				// Haha, Navigator, that's funny.
				$classes[] = 'navigator';
			}

		}

		// Some platform detection
		if(!in_array('platform', $disable) && $browser != 'unknown')
		{
			if (strpos($browser, 'win') !== false)
			{
				$classes[] = 'win';
			}
			elseif(strpos($browser, 'mac') !== false)
			{
				$classes[] = 'mac';
			}
		}

		$this->return_data = ($attr == 'false') ? implode(' ', $classes) : ' class="'.implode(' ', $classes).'"';
	}

}

?>
