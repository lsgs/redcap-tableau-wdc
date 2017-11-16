********************************************************************************
# Tableau Web Data Connector for REDCap

Luke Stevens, Murdoch Childrens Research Institute https://www.mcri.edu.au


********************************************************************************
## Summary

A REDCap External Module that provides a link to use when creating a Tableau Web
Data Connector. It enables a REDCap user with a project API key to have data 
from their REDCap project downloaded to their Tableau Desktop instance directly
using the REDCap API.

For version 1.0 only a simple download of all project data is performed, 
although you can select to receive either raw or label data. 

The module settings provide scope for institutions to specify their own text
for the connector and instruction pages (default text is provided).

The module provides a Control Center link to an Instructions page. If the module
is enabled for discovery by project users, then enabling within a project causes
a link to the Instructions page to be shown within the project page menu. 

*The connector may be utilised by project API users irrespective of whether the 
module is explicitly enabled for that project.*

********************************************************************************
## Instructions

1. Enabling the module in your instance of REDCap.
2. Follow the Control Center (or project page) link to the Instructions page.
3. Find and copy the URL displayed on the Instructions page.
4. In Tableau, search for \"Web Data Connector\" under 
   Connect -> To a Server -> More
5. Paste the URL as the \"web data connector URL\".
6. Enter your project API token into the text box.
7. Click Submit

The connector will be created and enable your project data to be downloaded 
into Tableau.

********************************************************************************
## TODO

For version 1.0 only a simple download of all project data performed. 
The following additional capabilities are envisaged for future versions:

1. Configurable REDCap API calls 
   Functionality similar to REDCap's API Playground that enables the user to 
   select different types of download, e.g. subset of fields, report, data 
   dictionary.

2. Have the getSchema function perform a data dictionary download to enable 
   configuration of field properties such as data type and format.
   
********************************************************************************
