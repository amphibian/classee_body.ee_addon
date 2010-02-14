This plugin will apply several dynamic classes to your `<body>` tag.  Use it like so in your template:

`<body{exp:classee_body}>`

That's it.  You'll now get a classed-up `<body>` tag using URI segments, the current member group, type of archive page (category, paged, or monthly), browser and platform.

##Examples

For example, if the current URI was:

`http://mydomain.com/magazine/articles/c/politics/P20/ `

Your `<body>` tag would look like this:

`<body class="magazine articles politics category paged P20 superadmin safari mac">`

(In this case, you'd be logged-in as a SuperAdmin, your category keyword would be "c", and you'd be browsing on a Mac using Safari.)

Member groups 1 through 5 will be classed using their group names (superadmin, banned, guest, pending, member), whereas custom member groups will be classed "groupid_N" (N being the member group ID).

Numeric URI segments (for example, when calling an entry via its entry_id) will be prepended with the letter "n", i.e.

`http://mydomain.com/magazine/articles/246`

Would yield:

`<body class="magazine articles n246 groupid_7 firefox win">`

Lastly, if there are no URI segments to be found, your `<body>` will get the class of "home".

If you'd like to retreive only the class names, but not the `class=""` attribute itelf, simply add `attr="false"` as a parameter:

`{exp:classee_body attr="false"}`

You can also disable the addition of certain kinds of classes by using a pipe-delimited list within the "disable" parameter:
	
`{exp:classee_body disable="paged|category|monthly"}`
	
Valid values for the "disable" parameter are "segments", "paged", "category", "monthly", "member_group", "browser" and "platform".

##Compatibility

This version of ClassEE Body is only compatible with ExpressionEngine 2.0 or higher. The ExpressionEngine 1.6-compatible version [can be found here](http://github.com/amphibian/pi.classee_body.ee_addon).