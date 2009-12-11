<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
	'pi_name'			=> 'ClassEE Body',
	'pi_version'		=> '2.0',
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
		
		$classes = '';		
		$attr = $this->EE->TMPL->fetch_param('attr', 'true');
		$open = ( $attr == 'false' ) ? '' : ' class="';
		$close = ( $attr == 'false' ) ? '' : '"';
		
		$segments = count($this->EE->uri->segments);
		$cat_trigger = $this->EE->config->item('reserved_category_word');			
				
		if($segments > 0)
		{
			
			// class per URI segment
			for($i = 1; $i <= $segments; $i++)
			{
				$seg = $this->EE->uri->segment($i);
				// Ignore the category indicator
				if($seg != $cat_trigger)
				{
					// prepend numeric segs
					$pre = '';
					if(is_numeric(substr($seg,0,1)))
					{
						$pre = 'n';
					}
					$classes .= $pre . $seg . ' ';
				}
			}
			
			// Check for pagination
			if(preg_match('/P{1}[0-9]+/', $this->EE->uri->uri_string) != FALSE)
			{
				$classes .= 'paged ';
			}
			
			// Check for category
			if(strpos($this->EE->uri->uri_string, "/$cat_trigger/") !== FALSE 
				|| preg_match('/C{1}[0-9]+/', $this->EE->uri->uri_string) != FALSE)
			{
				$classes .= 'category ';
			}
			
			// Check for monthly archive
			if ( $segments >= 2)
			{
				$m = $this->EE->uri->segment($segments);
				$y = $this->EE->uri->segment($segments-1);
				if(preg_match('/^[0-9]{4}$/', $y) != FALSE && preg_match('/^[0-9]{2}$/', $m) != FALSE)
				{
					$classes .= 'monthly ';
				}
			}				
		}
		else
		{	
			// No segs, so we're on the home page
			$classes .= 'home ';		
		}
		
		// class for member group
		$g = $this->EE->session->userdata['group_id'];
		
		switch($g)
		{
			case 1:
				$classes .= 'superadmin ';
				break;
			case 2:
				$classes .= 'banned ';
				break;
			case 3:
				$classes .= 'guest ';
				break;
			case 4:
				$classes .= 'pending ';
				break;
			case 5:
				$classes .= 'member ';
				break;				
			case ($g > 5):
				$classes .= 'groupid_' . $g . ' ';
				break;
		}
		
		// Some lightweight browser detection
		$browser = strtolower($_SERVER['HTTP_USER_AGENT']);
		
		if(strpos($browser, 'lynx') !== false)
		{
			$classes .= 'lynx ';
		}
		elseif(strpos($browser, 'chrome') !== false)
		{
			$classes .= 'chrome ';
		}
		elseif(strpos($browser, 'safari') !== false)
		{
			$classes .= 'safari ';
			$safari = 'y';
		}
		elseif(strpos($browser, 'firefox') !== false)
		{
			$classes .= 'firefox ';
		}
		elseif(strpos($browser, 'gecko') !== false)
		{
			$classes .= 'gecko ';
		}
		elseif(strpos($browser, 'msie') !== false)
		{
			$classes .= 'ie ';
		}
		elseif(strpos($browser, 'opera') !== false)
		{
			$classes .= 'opera ';
		}
		elseif(strpos($browser, 'nav') !== false && strpos($browser, 'mozilla/4.') !== false)
		{
			$classes .= 'navigator ';
		}
		
		if (isset($safari) && strpos($browser, 'mobile') !== false )
		{
			$classes .= 'iphone ';
		}
		
		// Some platform detection		
		if ( strpos($browser, 'win') !== false)
		{
			$classes .= 'win';
		}
		elseif(strpos($browser, 'mac') !== false)
		{
			$classes .= 'mac';
		}
						
		$this->return_data = $open . $classes . $close;
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

<?php
$buffer = ob_get_contents();
	
ob_end_clean(); 

return $buffer;
}
// END
}
// END CLASS
?>