.. php:function:: largo_widgets()

   Setup the Largo custom widgets

   :package: Largo

   :since: 1.0

.. php:function:: largo_add_widget_classes()

   Add custom CSS classes to sidebar widgets

   In addition to the usual WordPress widget classes, we add:
    - iterative classes (widget-1, widget-2, etc.) reset for each sidebar
    - odd/even classes
    - default/rev/no-bg classes
    - Bootstrap's responsive classes
   To give use a lot more styling hooks

   Partially adapted from Illimar Tambek's Widget Title Links plugin
   https://github.com/ragulka/widget-title-links

   :package: Largo

   :since: 1.0

.. php:function:: largo_widget_counter_reset()

   Resets the counter for each subsequent sidebar

   :since: 1.0

.. php:function:: largo_widget_custom_fields_form()

   Add custom fields to widget forms

   :since: 1.0

   :uses: add_action() $in_widget_form'

.. php:function:: largo_register_widget_custom_fields()

   Register widget custom fields

   :since: 1.0

   :uses: add_filter() $widget_form_callback'

.. php:function:: largo_widget_update_extend()

   Add additional fields to widget update callback

   :since: 1.0

   :uses: add_filter() $widget_update_callback'

.. php:function:: largo_add_link_to_widget_title()

   Make it possible for widget titles to be links

   :since: 1.0

   :uses: add_filter() $widget_title'