#WordPres 3.5 Media Modal Clone#


The WordPress Media Modal introduced in version 3.5 is nice clean interface element that I thought
represented the shape of things to come for opening dialogs.

Unfortunately, there doesn't seem to be any pre-packaged way to open these dialogs of our own within the WordPress
system. In addition, the modal Backbone classes introduced in 3.5 aren't appropriate targets to extend for use
in a Plugin -- not to mention that they don't exist prior to 3.5. So I set out to build my own.

The two folders in the repository, "modal-backbone" and "modal-iframe" represent two complete plugins that
implement modals similar to those created in wp.media.view.Modal. They each represent different development path;
it's up to you to choose the type that's appropriate for your plugin.

## IFrame Modal ##

The plugin in "modal-iframe" has a UI consistent with the Media Modal, but uses only jQuery for it's JavaScript
interaction and an IFrame to display the content.

Use of this style of modal allows the modal content to remain isolated from the Add/Edit Post/Page. Resources used
in the workflow aren't loaded until they're actually used. It's also very easy to customize for your own plugins.

## Backbone Modal ##

The plugin in the "modal-backbone" folder also has a UI in-line with the Media Modal. However, this modal is a full
Backbone application, ready for you to extend.  The application uses a custom "Templates" class to defer loading the
html for the UI until it's needed.

## General Info ##

Both plugins strive to implement WordPress PHP and JavaScript as well as Backbone best practices, including localization
class wrapping and namespacing, to name a few. It's also Delete-Key-Friendly: If you prefer to go it another way, have fun.

Comments, issues and pull requests are very welcome as I'll be happy to include any best practice or time-saving technique
that makes the example better.

### Reference ###

See [WordPress Answers question: "Is it possible to reuse wp.media.editor Modal for dialogs other than media" ](http://wordpress.stackexchange.com/questions/85442/)

## License ##

Copyright (C) 2013  [Jer Brand / aut0poietic](http://irresponsibleart.com)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

