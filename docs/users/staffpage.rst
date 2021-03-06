Staff roster shortcode
======================

In its simplest form, a staff roster shortcode looks like:

::

    [roster]

However, it has several optional attributes which can control which
users appear in the roster.

You can set:

``roles``
---------

By default, the ``roster`` shortcode includes only users with the
“author” role.

To specify which WordPress roles should be included use comma-separated
values like so:

::

    [roster roles="administrator,author,editor"]

All possible values for the ``roles`` attribute:

-  administrator
-  author
-  editor
-  contributor
-  subscriber

``exclude``
-----------

Use this attribute to exclude users by ID. For example, say you have a
user called “Largo Project Administrator” which has an “administrator”
role. Let’s say that user’s ID is 2. Here’s how you would exclude that
user from the staff roster:

::

    [roster roles="administrator,author,editor" exclude="2"]

To exclude more than one user, use comma-separated values. For example:

::

    [roster roles="administrator,author,editor" exclude="2,32,56"]

``include``
-----------

Similarly, you can specifically include users with the ``include``
attribute. If you wanted to ommit all “administrator” users except
“Largo Project Administrator” your ``roster`` shortcode would look like:

::

    [roster roles="author,editor" include="2"]

``show_users_with_empty_desc``
------------------------------

By default, the ``roster`` shortcode does not include users with an
empty bio/description. If you want to show users with an empty
bio/description anyway, add the attribute ``show_users_with_empty_desc``
to the ``roster`` shortcode like so:

::

    [roster roles="administrator,author,editor" exclude="2" show_users_with_empty_desc=true]
