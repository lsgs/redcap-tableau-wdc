<?php
/**
 * REDCap External Module: Tableau Connector
 * Create a Web Data Connector for Tableau to a REDCap project.
 * @author Luke Stevens, Murdoch Children's Research Institute
 * Connector Documentation:
 *   https://github.com/tableau/webdataconnector/blob/master/docs/wdc_tutorial.md
 *   https://blog.clairvoyantsoft.com/setting-up-a-tableau-web-data-connector-62147e4bc4bf
 * TODO: 
 * - Do additional data dictionary API call in myConnector.getSchema() to get 
 *   data type information for fields (currently all fields treated as string)
 * - Use rich text editor for instruction-panel-text setting
 * - Options for different export types: metadata, report, field/record filters
 */
namespace MCRI\TableauConnector;

use ExternalModules\AbstractExternalModule;

/**
 * REDCap External Module: Tableau Connector
 */
class TableauConnector extends AbstractExternalModule
{        
        /**
         * Print the page of instructions 
         */
        public function printInstructionsPageContent() {
                $pid=PROJECT_ID;
                $panelTitle = (defined('PROJECT_ID')) ?  $this->getProjectSetting('instruction-panel-title') : $this->getSystemSetting('instruction-panel-title');
                $instructionText = (defined('PROJECT_ID')) ?  $this->getProjectSetting('instruction-panel-text') : $this->getSystemSetting('instruction-panel-text');
                $url = $this->getUrl('wdc.php', false, true);
                $url = str_replace('&pid='.PROJECT_ID, '', $url);
                echo renderPageTitle($this->getModuleName());
                echo "<div class='panel panel-primary' style='margin: 2em 0;'><div class='panel-heading'>$url</div></div>";
                echo "<div class='panel panel-default'><div class='panel-heading'><strong>$panelTitle</strong></div><div class='panel-body wdc-instructions-text'>$instructionText</div>";
                ?>
<style type="text/css">
    .wdc-instructions-text { }
    .wdc-instructions-text li { margin: 1em 0; }
</style>
                <?php
        }

        /**
         * Print the module plugin page html content 
         */
        public function printConnectorPageContent() {
                global $login_logo, $institution;
                
                $logoImg = (trim($login_logo)=="") ? "" : "<img src=\"$login_logo\" title=\"$institution\" alt=\"$institution\" style='max-width:850px;'>";
                $institution = js_escape2(strip_tags(label_decode($institution)));
                $pageTitle = $this->getSystemSetting('connector-page-title');
                $instructionText = $this->getSystemSetting('connector-page-instruction-text');
                $formatLabelText = $this->getSystemSetting('connector-page-format-label');
                $tokenLabelText = $this->getSystemSetting('connector-page-token-label');
                $librarySrc = $this->getSystemSetting('tableau-wdc-js-src');
?>
<style type="text/css">
    .input-group { margin:20px 0; }
    .input-group-addon { min-width:150px; text-align:left; }
</style>
<div class="row">
  <div class="col-sm-12 text-center">
    <div style="margin:10px 0;"><?php echo $logoImg;?></div>
    <div style="margin:10px 0;" class="lead"><?php echo $pageTitle;?></div>
  </div>
</div>
<div class="row">
  <div class="col-sm-8 col-sm-offset-2">
    <p class="text-center"><?php echo $instructionText;?></p>
    <div class="form-group">
      <input type="hidden" id="url" value="<?php echo APP_PATH_WEBROOT_FULL.'api/';?>">
      <div class="input-group">
        <span class="input-group-addon" style=""><?php echo $formatLabelText;?></span>
        <span class="form-control text-left">
          <label class="radio-inline"><input type="radio" name="raworlabel" value="raw" checked>raw</label>
          <label class="radio-inline"><input type="radio" name="raworlabel" value="label">label</label>
        </span>
      </div>
      <div class="input-group">
        <span class="input-group-addon" style=""><?php echo $tokenLabelText;?></span>
        <input type="text" class="form-control" id="token" placeholder="A0B1C2D3E4...">
      </div>
      <div class="text-center">
        <button class="btn btn-primary" id="submitButton" type="button">Submit</button>
      </div>
    </div>
  </div>
</div>

<script src="<?php echo $librarySrc;?>" type="text/javascript"></script>
<script>

(function(tableau) {

    var myConnector = tableau.makeConnector();

    // Define the schema
    myConnector.getSchema = function(schemaCallback) {
      var connectionParams = JSON.parse(tableau.connectionData);
      var recordsInfo = [];
      $.ajax({
        url: connectionParams.url,
        type: "POST",
        data: {
          token: connectionParams.token,
          content: 'exportFieldNames',
          format: 'json',
          returnFormat: 'json'
        },
        contentType: "application/x-www-form-urlencoded",
        dataType: "json",
        success: function(resp){
          recordsInfo = resp;
          var recordSchema = [];
          recordsInfo.forEach(function(field){
            recordSchema.push({
              id: field.export_field_name,
              alias: (field.export_field_name===field.original_field_name) ? field.original_field_name : field.original_field_name+' '+field.choice_value,
              dataType: tableau.dataTypeEnum.string
            });
            var redcapTable = {
              id: "redcap",
              alias: "custom redcap extract",
              columns: recordSchema
            };
            schemaCallback([redcapTable]);
          });
        }
      });
    };

    // Download the data
    myConnector.getData = function(table, doneCallback) {
      var connectionParams = JSON.parse(tableau.connectionData);
      var tableData = [];
      $.ajax({
          url: connectionParams.url,
          type: "POST",
          data: {
            token: connectionParams.token,
            content: 'record',
            format: 'json',
            returnFormat: 'json',
            type: 'flat',
            rawOrLabel: connectionParams.raworlabel,
            rawOrLabelHeaders: 'raw',
            exportCheckboxLabel: 'true',
            exportSurveyFields: 'true',
            exportDataAccessGroups: 'true'
          },
          contentType: "application/x-www-form-urlencoded",
          dataType: "json",
          success: function(resp){
          resp.forEach(function(record){
            tableData.push(record);
          });
          table.appendRows(tableData);
          doneCallback();
        }
      });
    };

    tableau.registerConnector(myConnector);

    $(document).ready(function (){
      $("#submitButton").click(function() {
        var exportFormat = $("input[name=\"raworlabel\"]:checked").val();
        exportFormat = (exportFormat==='label') ? exportFormat : 'raw';
        tableau.connectionData = JSON.stringify({
          raworlabel: exportFormat,
          token: $("input#token").val(),
          url: $("input#url").val()
        });
        tableau.connectionName = "REDCap Data";
        try {
          tableau.submit();
        }
        catch(err) {
          alert(err);
        }
      });
    });
  })(tableau);
</script>
<?php
        }
}