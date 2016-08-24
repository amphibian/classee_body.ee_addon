#Classee Body

This plugin will apply several dynamic classes to your <body> tag.  Use it like so in your template:

```<body{exp:classee_body}>```

That's it.  You'll now get a classed-up `<body>` tag using URI segments, the current member group, and type of archive page (category, paged, or monthly).

For example, if the current URI was:

**http://mydomain.com/magazine/articles/c/politics/P20/**

Your `<body>` tag would look like this:

```<body class="magazine articles politics category paged P20 superadmin">```

(In this case, you'd be logged-in as a SuperAdmin, and your category keyword would be "c".)

Member groups 1 through 5 will be classed using their group names (superadmin, banned, guest, pending, member), whereas custom member groups will be classed "groupid_N" (N being the member group ID).

Numeric URI segments (for example, when calling an entry via its entry_id), and URI segments that begin with a number, will be prepended with the letter "n", i.e.

**http://mydomain.com/magazine/articles/246**

Would yield:

```<body class="magazine articles n246 groupid_7">```

If there are no URI segments to be found, your `<body>` will get the class of "home".

If you'd like to retreive only the class names, but not the class="" attribute itelf, simply add `attr="false"` as a parameter:

```{exp:classee_body attr="false"}```

You can also disable the addition of certain kinds of classes by using a pipe-delimited list within the "disable" parameter:

```{exp:classee_body disable="paged|category|monthly"}```

Valid values for the "disable" parameter are "segments", "paged", "category", "monthly", "member_group", "browser" and "platform".