
var $j = jQuery.noConflict();

$j(document).ready(function() {
    console.log('PCDM JS ready');
    $j("input[name='pcdm_pb_collection_template']").bind("click", PCDM_Admin.radioTemplateClick);
    var selected_tpl = $j("input[name='pcdm_pb_collection_template']").val();
    console.log(selected_tpl)
    PCDM_Admin.switchTemplate(selected_tpl);
});

function PCDM_Admin() {

}
;

PCDM_Admin.radioTemplateClick = function() {
    PCDM_Admin.switchTemplate(this.value);
};

PCDM_Admin.switchTemplate = function(tpl){
    switch (tpl)
    {
        case 'mult_prod_tpl':
            PCDM_Admin.showFullTemplate();
            break;
        case 'sngl_prod_tpl':
            PCDM_Admin.showSingleTemplate();
            break;
    }
};

PCDM_Admin.showFullTemplate = function() {
    $j("#pcdm_pb_fieldset_3").removeClass('sngl_prod_tpl_selector');
    $j("#pcdm_pb_fieldset_3").addClass('mult_prod_tpl_selector');
};

PCDM_Admin.showSingleTemplate = function() {
    $j("#pcdm_pb_fieldset_3").removeClass('mult_prod_tpl_selector');
    $j("#pcdm_pb_fieldset_3").addClass('sngl_prod_tpl_selector');

};