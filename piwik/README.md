Piwik extension
===============

This extension provide GUI (CRUD) for Piwik interaction. 

Configuration
=============

- This extension create directory in `@appRoot/uploads`. 
Make sure this directory has write permissions.
                                        

GetCode widget
==============

When you created a Tracking Code in backend (GUI), you can put it on your page. 
For this, you must add in your template (most often it's layout) code like this one: 

    <?=  GetCode::widget(['scope' => 'backend']) ?>