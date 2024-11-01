=== Simple Customer CRM Plugin ===

Contributors: tristup, sumanshill, rahuldsarker
Donate link: http://www.tristupghosh.com
Tags: Simple Customer CRM Plugin
Requires at least: 4.9.4
Requires PHP: 5.2.4
Tested up to: 5.3
Stable tag: 1.0.1
License: GPLv2 or later.
 
== Description ==

Simple Customer CRM plugin will help to capture the customer data using customer form.

== Major features in Simple Customer CRM Plugin include: ==


1. Is to create a Form through a shortcode and show it in frontend. 

2. Form will capture the data submitted by the user in form of private data.

3. Customer Custom post type will be created on plugin installation and store all the data submitted.  

4. All the additional data will be stored as Post meta data. 

== Shortcode & Attributes ==

#With all default values
[sccrm] 

#Attributes
form_title = to set the Form Title 

name_field_label = Field Label for Name
name_field_min_length = to set minimum length of the name
name_field_max_length = to set maximum length of the name

phone_field_label = Field Label for Phone 
phone_field_min_length = to set minimum length of the phone
phone_field_max_length = to set maximum length of the phone

email_field_label = Field Label for Email 

budget_field_label = Field Label for Budget 

message_field_label =  Field Label for Message 
message_field_height = to set minimum rows/height of the Message
message_field_width = to set maximum cols/width of the Message

#with all custom value 
[sccrm form_title="Customer Form" name_field_label="Your Name" name_field_min_length="5" name_field_max_length="20" phone_field_label="Your Phone" phone_field_min_length="10" phone_field_max_length="12" email_field_label="Your Email" budget_field_label="Your Budget" message_field_label="Your Message" message_field_height="5" message_field_width="50"] 

== Screenshot ==

1. List of Customer [ Customer Post Type ].
2. Frontend view of Customer Form. 
3. Shortcode in page/post.  


== Installation ==
 
This section describes how to install the plugin and get it working.

1. Go to Wordpress Dashboard > Plugins > click on Installed plugins > look for add new at the top most of the screen and click here > Add Plugins > upload plugin > choose file -> click here and upload Simple Demo Importer plugin.

2. Activate Plugin through 'Plugins' menu in WordPress.

You will find 'Import Demos' menu in your WordPress admin panel.


== Customer Info ==

title = customer name
post_content = customer message
sccrm_customer_phone  = for customer phone 
sccrm_customer_email  = for customer email id 
sccrm_customer_budget  = for customer budget 
sccrm_customer_create_date  = for customer create time and date from World Clock API 


== ThridParty API ==

Used WorldClock API to get UTC date and time. 

http://worldclockapi.com/api/json/utc/now