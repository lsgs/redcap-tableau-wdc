********************************************************************************
# Tableau Web Data Connector for REDCap

Luke Stevens, Murdoch Children's Research Institute https://www.mcri.edu.au

********************************************************************************
## Summary

A REDCap External Module that provides a link to use to create a Tableau Web
Data Connector. It enables a REDCap user with a project API key to have data 
from their REDCap project downloaded to their Tableau Desktop instance (v10.0+).

Options available for customising the extract are:
 * Raw data or labels
 * Include Data Access Group no/yes
 * Subset of fields: specify a comma- or space-separated list export field names
 * Subset of records: specify a REDCap filter logic expression

The module settings provide scope for institutions to specify their own text
for the connector and instruction pages (default text is provided).

### Enabling in Individual Projects is Optional
The module provides a Control Center link to an Instructions page. If the module
is enabled for discovery by project users, then enabling within a project causes
a link to the Instructions page to be shown within the project page menu. 

The connector may be utilised by project API users irrespective of whether the 
module is explicitly enabled for that project. All that enabling the module in
a project does is add a link to the instructions page in the project page menu.

********************************************************************************
## Instructions

1. Enable the module in your instance of REDCap (and optionally in a project).
2. Follow the Control Center (or project page) link to the Instructions page.
3. Copy the URL displayed prominently on the Instructions page.
4. In Tableau 10.0+, go to Connect -> To a Server -> More and find 
   \"Web Data Connector\" 
5. Paste the URL as the \"web data connector URL\" and press Enter.
6. Enter your project API token into the text box and select/enter other 
   options, as desired.
7. Click Submit.

The web data connector will be executed and create a Tableau Data Source in your
Tableau workbook .

8. Click "Update Now" to extract your project data into the Data Source.

********************************************************************************