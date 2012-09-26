<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
	'pi_name'			=> 'ClassEE Body',
	'pi_version'		=> '2.0.4',
	'pi_author'			=> 'Derek Hogue',
	'pi_author_url'		=> 'http://github.com/amphibian/pi.classee_body.ee2_addon/',
	'pi_description'	=> 'Applies dynamic classes to your BODY tag.',
	'pi_usage'			=> Classee_body::usage()
);

class Classee_body
{

	function Classee_body()
	{
		$this->EE =& get_instance();
				
		$this->return_data = '';
		
		$attr = $this->EE->TMPL->fetch_param('attr', 'true');
		$browser = (isset($_SERVER['HTTP_USER_AGENT'])) ? strtolower($_SERVER['HTTP_USER_AGENT']) : 'unknown';		
		$cat_trigger = $this->EE->config->item('reserved_category_word');			
		$classes = array();
		$disable = ($this->EE->TMPL->fetch_param('disable')) ? 
			explode('|', $this->EE->TMPL->fetch_param('disable')) : 
			array();	
		$group = $this->EE->session->userdata['group_id'];		
		$segments = count($this->EE->uri->segments);
				
		if($segments > 0)
		{
			// One class per URI segment
			if(!in_array('segments', $disable))
			{
				for($i = 1; $i <= $segments; $i++)
				{
					$seg = $this->EE->uri->segment($i);
					// Ignore the category indicator
					if($seg != $cat_trigger)
					{
						$classes[] = (is_numeric(substr($seg,0,1))) ? 'n'.$seg : $seg;
					}
				}
			}
			
			// Check for pagination
			if(!in_array('paged', $disable) && preg_match('/P{1}[0-9]+/', $this->EE->uri->uri_string) != FALSE)
			{
				$classes[] = 'paged';
			}
			
			// Check for category
			if(!in_array('category', $disable) && strpos($this->EE->uri->uri_string, "/$cat_trigger/") !== FALSE 
				|| preg_match('/C{1}[0-9]+/', $this->EE->uri->uri_string) != FALSE)
			{
				$classes[] = 'category';
			}
			
			// Check for monthly archive
			if(!in_array('monthly', $disable) && $segments >= 2)
			{
				$m = $this->EE->uri->segment($segments);
				$y = $this->EE->uri->segment($segments-1);
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
    
    
// ----------------------------------------
//  Plugin Usage
// ----------------------------------------

// This function describes how the plugin is used.
//  Make sure and use output buffering

function usage()
{
ob_start(); 
?>
This plugin will apply several dynamic classes to your <body> tag.  Use it like so in your template:

<body{exp:classee_body}>

That's it.  You'll now get a classed-up <body> tag using URI segments, the current member group, and type of archive page (category, paged, or monthly).

For example, if the current URI was:

http://mydomain.com/magazine/articles/c/politics/P20/ 

Your <body> tag would look like this:

<body class="magazine articles politics category paged P20 superadmin">

(In this case, you'd be logged-in as a SuperAdmin, and your category keyword would be "c".)

Member groups 1 through 5 will be classed using their group names (superadmin, banned, guest, pending, member), whereas custom member groups will be classed "groupid_N" (N being the member group ID).

Numeric URI segments (for example, when calling an entry via its entry_id), and URI segments that begin with a number, will be prepended with the letter "n", i.e.

http://mydomain.com/magazine/articles/246

Would yield:

<body class="magazine articles n246 groupid_7">

If there are no URI segments to be found, your <body> will get the class of "home".

If you'd like to retreive only the class names, but not the class="" attribute itelf, simply add attr="false" as a parameter:

{exp:classee_body attr="false"}

You can also disable the addition of certain kinds of classes by using a pipe-delimited list within the "disable" parameter:

{exp:classee_body disable="paged|category|monthly"}

Valid values for the "disable" parameter are "segments", "paged", "category", "monthly", "member_group", "browser" and "platform".

<?php
$buffer = ob_get_contents();
	
ob_end_clean(); 

return $buffer;
}
// END
}
// END CLASS
?>