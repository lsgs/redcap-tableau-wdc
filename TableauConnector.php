<?php
/**
 * REDCap External Module: Tableau Connector
 * Create a Web Data Connector for Tableau to a REDCap project.
 * @author Luke Stevens, Murdoch Children's Research Institute
 * Connector Documentation:
 *   https://github.com/tableau/webdataconnector/blob/master/docs/wdc_tutorial.md
 *   https://blog.clairvoyantsoft.com/setting-up-a-tableau-web-data-connector-62147e4bc4bf
 * TODO:
 * - Options for different export types: metadata, report, field/record filters
 *   (may require additional content in Project ODM XML e.g. report list, indication
 *   of which instruments are surveys.
 */
namespace MCRI\TableauConnector;

use ExternalModules\AbstractExternalModule;

/**
 * REDCap External Module: Tableau Connector
 */
class TableauConnector extends AbstractExternalModule
{
        public function redcap_module_link_check_display($project_id,$link)
        {
                return true;
        }

        /**
         * Print the page of instructions
         */
        public function printInstructionsPageContent() {
                $pid=PROJECT_ID;
                $panelTitle = (defined('PROJECT_ID')) ?  $this->getProjectSetting('instruction-panel-title') : $this->getSystemSetting('instruction-panel-title');
                $instructionText = (defined('PROJECT_ID')) ?  $this->getProjectSetting('instruction-panel-text') : $this->getSystemSetting('instruction-panel-text');
                $url = $this->getUrl('wdc.php', true, true);
                $url = str_replace('&pid='.PROJECT_ID, '', $url);
                echo renderPageTitle($this->getModuleName());
                ?>
                <div class="card wdc-instructions-card">
                    <h5 class="card-header"><?php echo $panelTitle;?></h5>
                    <div class="card-body">
                      <h5 class="card-title wdc-instructions-title"><?php echo $url;?></h5>
                      <div class="card-text wdc-instructions-text"><?php echo $instructionText;?></div>
                    </div>
                </div>
                <style type="text/css">
                    .wdc-instructions-card { width: max-content; }
                    .wdc-instructions-title { font-size:100%; font-weight:bold; }
                    .wdc-instructions-text > ol { margin-bottom: 0; padding-left: 1rem; }
                    .wdc-instructions-text > ol > li { margin-top: 0.5rem; }
                </style>
                <?php
        }

        public function getSystemSettingOrDefault($key) {
                $value = $this->getSystemSetting($key);
                if (is_null($value)) {
                        $config = $this->getSettingConfig($key);
                        if (array_key_exists('default', $config)) {
                                $value = $config['default'];
                        }
                }
                return $value;
        }

        /**
         * Print the module plugin page html content
         */
        public function printConnectorPageContent() {
                global $login_logo, $institution;

                $logoImg = (trim($login_logo)=="") ? "" : "<img src=\"$login_logo\" title=\"$institution\" alt=\"$institution\" style='max-width:850px;'>";
                $institution = js_escape2(strip_tags(label_decode($institution)));
                $pageTitle = $this->getSystemSettingOrDefault('connector-page-title');
                $instructionText = $this->getSystemSettingOrDefault('connector-page-instruction-text');
                $formatLabelText = $this->getSystemSettingOrDefault('connector-page-format-label');
                $fieldFormatLabelText = $this->getSystemSettingOrDefault('connector-page-fieldformat-label');
                $dagLabelText = $this->getSystemSettingOrDefault('connector-page-dag-label');
                $tokenLabelText = $this->getSystemSettingOrDefault('connector-page-token-label');
                $fieldListLabelText = $this->getSystemSettingOrDefault('connector-page-fieldlist-label');
                $filterLogicLabelText = $this->getSystemSettingOrDefault('connector-page-filterlogic-label');
                $librarySrc = $this->getSystemSettingOrDefault('tableau-wdc-js-src');
?>
<style type="text/css">
    /*.input-group { margin:20px 0; }*/
    .input-group-prepend { min-width:250px; }
    .input-group-text { min-width:250px; }
</style>
<div class="row">
  <div class="col-sm-12 text-center">
    <div style="margin:10px 0;"><?php echo $logoImg;?></div>
    <div style="margin:10px 0;" class="lead"><?php echo $pageTitle;?></div>
  </div>
</div>
<div class="row">
  <div class="col-sm-10 col-sm-offset-1">
    <p class="text-center"><?php echo $instructionText;?></p>
    <div class="form-group">
      <input type="hidden" id="url" value="<?php echo APP_PATH_WEBROOT_FULL.'api/';?>">
      <div class="x-input-group mb-2">
        <div class="input-group-prepend">
            <span class="input-group-text" style=""><?php echo $tokenLabelText;?></span>
        </div>
        <input type="text" class="form-control" id="token" placeholder="A0B1C2D3E4...">
      </div>
      <div class="x-input-group mb-2">
        <div class="input-group-prepend">
            <span class="input-group-text" style=""><?php echo $formatLabelText;?></span>
        </div>
        <div class="form-control">
          <label class="form-check-label form-check-inline"><input type="radio" name="raworlabel" value="raw" class="form-check-input" checked>raw</label>
          <label class="form-check-label form-check-inline"><input type="radio" name="raworlabel" value="label" class="form-check-input">label</label>
        </div>
      </div>
      <div class="x-input-group mb-2">
        <div class="input-group-prepend">
            <span class="input-group-text" style=""><?php echo $fieldFormatLabelText;?></span>
        </div>
        <div class="form-control">
          <label class="form-check-label form-check-inline"><input type="radio" name="varorlabel" value="var" class="form-check-input" checked>variable</label>
          <label class="form-check-label form-check-inline"><input type="radio" name="varorlabel" value="label" class="form-check-input">label</label>
        </div>
      </div>
      <div class="x-input-group mb-2">
        <div class="input-group-prepend">
            <span class="input-group-text" style=""><?php echo $dagLabelText;?></span>
        </div>
        <div class="form-control">
          <label class="form-check-label form-check-inline"><input type="radio" name="incldag" value="0" class="form-check-input" checked>no</label>
          <label class="form-check-label form-check-inline"><input type="radio" name="incldag" value="1" class="form-check-input">yes</label>
        </div>
      </div>
      <div class="x-input-group mb-2">
        <div class="input-group-prepend">
            <span class="input-group-text" style=""><?php echo $fieldListLabelText;?></span>
        </div>
        <input type="text" class="form-control" id="fieldList" placeholder="Comma- or space-separated list of export field names">
      </div>
      <div class="x-input-group mb-2">
        <div class="input-group-prepend">
            <span class="input-group-text" style=""><?php echo $filterLogicLabelText;?></span>
        </div>
        <input type="text" class="form-control" id="filterLogic" placeholder="REDCap-style filter logic expression">
      </div>
      <div class="text-center mb-2">
        <button class="btn btn-primary" id="submitButton" type="button">Submit</button>
      </div>
    </div>
  </div>
</div>

<script src="<?php echo $librarySrc;?>" type="text/javascript"></script>
<script type="text/javascript">
if (typeof tableau==='undefined') { alert('Error: could not download tableau connector code at <?php echo $librarySrc;?>'); }
(function(tableau) {

    var myConnector = tableau.makeConnector();

    // Define the schema
    myConnector.getSchema = function(schemaCallback) {
        var connectionData = JSON.parse(tableau.connectionData);

        $.ajax({
            url: connectionData.url,
            type: "POST",
            data: {
                token: connectionData.token,
                content: 'project_xml',
                returnFormat: 'json',
                returnMetadataOnly: true,
                exportSurveyFields: connectionData.surveyFields,
                exportDataAccessGroups: connectionData.dags,
                exportFiles: false
            },
            contentType: "application/x-www-form-urlencoded",
            dataType: "xml",
            success: function(response) {
                try {
                    var redcapTable = buildDataSource(connectionData, response);
                    schemaCallback([redcapTable]);
                }
                catch (e) {
                    console.log(e);
                }
            }
        });
    };

    // Download the data
    myConnector.getData = function(table, doneCallback) {
        var connectionData = JSON.parse(tableau.connectionData);

        // if subset of fields is specified, look for any checkbox columns
        // and swap out for the checkbox field name
        var fieldList = [];
        if (connectionData.fieldList.length>0) {
            connectionData.fieldList.forEach(function(f) {
                if (f.indexOf('___')>0) {
                    var cbNameParts = f.split('___');
                    if ($.inArray(cbNameParts[0], fieldList)===-1) {
                        fieldList.push(cbNameParts[0]);
                    }
                } else {
                    fieldList.push(f);
                }
            });
        }

        var tableData = [];
        $.ajax({
            url: connectionData.url,
            type: "POST",
            data: {
                token: connectionData.token,
                content: 'record',
                format: 'json',
                returnFormat: 'json',
                type: 'flat',
                fields: fieldList,
                rawOrLabel: connectionData.raworlabel,
                varOrLabel: connectionData.varorlabel,
                rawOrLabelHeaders: 'raw',
                exportCheckboxLabel: false,
                exportSurveyFields: connectionData.surveyfields,
                exportDataAccessGroups: connectionData.dags,
                filterLogic: connectionData.filterLogic
            },
            contentType: "application/x-www-form-urlencoded",
            dataType: "json",
            success: function(resp){
                resp.forEach(function(record){
		    $.each(record, function(key, value) {
			if (value === "" || value === null) {
			  delete record[key];
			    }
		      });
		     tableData.push(record);
                });
                table.appendRows(tableData);
                doneCallback();
            }
        });
    };

    tableau.registerConnector(myConnector);

    function buildDataSource(connectionData, response){
        var $response = $(response);
        var pName = $response.find('GlobalVariables').find( 'StudyName' ).text();
        var isLong = $response.find('MetaDataVersion').find( 'StudyEventRef' ).length > 0;

    //                var numRptEvents = $response.find( 'redcap\\:RepeatingEvent' ).length; // this find works in the simulator but for some unknown reason not in Tableau - instead find repeat setup by looping through GlobalVariables children
    //                var numRptForms = $response.find( 'redcap\\:RepeatingInstrument' ).length;
        var hasRepeatingEventsOrForms = false;
        $response.find('GlobalVariables').children().each(function(){
            if (this.nodeName==='redcap:RepeatingInstrumentsAndEvents') {
                hasRepeatingEventsOrForms = true;
            }
        });

        var filterFields = connectionData.fieldList.length>0;

        var fields = [];
        $response.find('MetaDataVersion').find( 'ItemRef' ).each(function(v){
            var rcExportVarname = this.attributes['ItemOID'].value;
            var varNode = $response.find( 'ItemDef[OID="'+rcExportVarname+'"]');
            var rcFType = varNode.attr('redcap:FieldType');

            if (rcFType !== 'descriptive') {
                if (fields.length>0 && filterFields &&
                        $.inArray(rcExportVarname, connectionData.fieldList)===-1) {
                    if (rcFType==='checkbox') {
                        // if the user has specified the checkbox variable name rather than full export names (with ___) then still allow
                        var cbNameParts = rcExportVarname.split('___');
                        if ($.inArray(cbNameParts[0], connectionData.fieldList)===-1) {
                            return;
                        }
                    } else {
                        return; // if field list specified, skip if current field is not listed
                    }
                }

                var f = {};
                f.id = rcExportVarname;

		if (connectionData.varorlabel==='var') {
		  f.alias = rcExportVarname;
		} else {
		  f.alias = varNode.find( 'TranslatedText' ).text();
		}
                f.description = varNode.find( 'TranslatedText' ).text();

                var dataType = 'string';

                if (connectionData.raworlabel==='raw') {
                    dataType = varNode.attr('DataType');
                }

                if (rcFType==='checkbox') {
		  if (connectionData.varorlabel==='label') {
		    f.alias = f.description+' (choice='+getCheckboxChoiceLabel($response, rcExportVarname)+')';
		  }

		  f.description = getCheckboxChoiceLabel($response, rcExportVarname)+' | '+f.description;
                }

                f.dataType = odmTypeToTableauType(dataType);

                fields.push(f);

                if (fields.length === 1){ // i.e. directly after record id field...
                    if (filterFields && $.inArray(rcExportVarname, connectionData.fieldList)===-1) {
                        connectionData.fieldList.unshift(rcExportVarname); // ensure record id is included in list of fields, when specified
                    }
                    if (isLong) {
                        fields.push({
                            id: "redcap_event_name",
                            alias: "redcap_event_name",
                            description: "Event Name",
                            dataType: tableau.dataTypeEnum.string
                        });
                    }
                    if (hasRepeatingEventsOrForms) {//numRptEvents+numRptForms>0) {
                        fields.push({
                            id: "redcap_repeat_instrument",
                            alias: "redcap_repeat_instrument",
                            description: "Repeat Instrument",
                            dataType: tableau.dataTypeEnum.string
                        });
                        fields.push({
                            id: "redcap_repeat_instance",
                            alias: "redcap_repeat_instance",
                            description: "Repeat Instance",
                            dataType: tableau.dataTypeEnum.int
                        });
                    }
                    if (connectionData.dags) {
                        fields.push({
                            id: "redcap_data_access_group",
                            alias: "redcap_data_access_group",
                            description: "Data Access Group",
                            dataType: tableau.dataTypeEnum.string
                        });
                    }
                }
            }
        });

        var redcapTable = {
            id: "redcap",
            alias: pName,
            columns: fields
        };

        tableau.connectionData = JSON.stringify(connectionData);

        return redcapTable;
    };

    function odmTypeToTableauType(dtype) {

        switch (dtype) {
            case 'integer': return tableau.dataTypeEnum.int; break;
            case 'text': return tableau.dataTypeEnum.string; break;
            case 'float': return tableau.dataTypeEnum.float; break;
            case 'date': return tableau.dataTypeEnum.date; break;
            case 'datetime': return tableau.dataTypeEnum.datetime; break;
            case 'partialDatetime': return tableau.dataTypeEnum.datetime; break;
            case 'boolean': return tableau.dataTypeEnum.int; break;
            default: return tableau.dataTypeEnum.string;
        }
    };

    function getCheckboxChoiceLabel($response, rcExportVarname) {
        var choiceOptionString = $response.find( 'CodeList[OID="'+rcExportVarname+'.choices"]' ).attr('redcap:CheckboxChoices');
        var choiceVarVal = rcExportVarname.split('___');
        var choiceLabel = choiceVarVal;
        choiceOptionString.split(' | ').forEach(function(c) {
            if (c.lastIndexOf(choiceVarVal[1]+', ', 0)===0) { // if (c.startsWith(choiceVarVal[1]+', ')) { // do not use startsWith() !
                choiceLabel = c.replace(choiceVarVal[1]+', ', '');
            }
        });
        return choiceLabel;
    }

    $(document).ready(function (){

        $("#submitButton").click(function() {
            var exportFormat = $("input[name=\"raworlabel\"]:checked").val();
            exportFormat = (exportFormat==='label') ? exportFormat : 'raw';

            var exportFieldFormat = $("input[name=\"varorlabel\"]:checked").val();
            exportFieldFormat = (exportFieldFormat==='label') ? exportFieldFormat : 'var';

            var includeDag = ("1" == $("input[name=\"incldag\"]:checked").val());

            var fields = $("input#fieldList").val();
            var fieldList = (fields.trim().length>0) ? fields.split(/[ ,\t]+/) : [];
/* Passing tableau.connectionData as an object works in the simulator but not in Tableau.
 * Debugging shows tableau.connectionData = "[object Object]" i.e. that string, not the object!
 * (https://tableau.github.io/webdataconnector/docs/wdc_debugging)
 * Passing tableau.connectionData as a string is a workaround, hence:
 * tableau.connectionData = JSON.stringify(connectionData);
 */
            var connectionData = {
                raworlabel: exportFormat,
                varorlabel: exportFieldFormat,
                surveyfields: false, // can't yet tell from odm xml whiat instruments are surveys
                dags: includeDag,
                token: $("input#token").val(),
                fieldList: fieldList,
                filterLogic: $("input#filterLogic").val(),
                url: $("input#url").val()
            };
            tableau.connectionData = JSON.stringify(connectionData);
            tableau.connectionName = "REDCap Data";
            try {
                tableau.submit();
            }
            catch(err) {
                console.log(err);
            }
        });
    });
})(tableau);
</script>
<?php
        }
}
